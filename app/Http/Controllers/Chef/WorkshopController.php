<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\EnhancedImageUploadService;
use App\Services\JitsiMeetingService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkshopController extends Controller
{
    public function __construct(protected JitsiMeetingService $jitsiMeetingService)
    {
    }

    public function index(): \Illuminate\View\View
    {
        $baseQuery = Workshop::query()->where('user_id', Auth::id());

        $workshopsQuery = (clone $baseQuery)
            ->withCount([
                'bookings as confirmed_bookings' => fn ($query) => $query->where('status', 'confirmed'),
            ])
            ->orderByDesc('start_date');

        /** @var LengthAwarePaginator $workshops */
        $workshops = $workshopsQuery
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'online' => (clone $baseQuery)->where('is_online', true)->count(),
            'drafts' => (clone $baseQuery)->where('is_active', false)->count(),
        ];

        return view('chef.workshops.index', compact('workshops', 'stats'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('chef.workshops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateWorkshop($request);

        $workshop = new Workshop();
        $this->fillWorkshopData($workshop, $data);
        $this->handleImageUpload($request, $workshop);
        $this->applyMeetingProvider($request, $workshop, $data['meeting_link'] ?? null);
        $workshop->user_id = Auth::id();
        $workshop->instructor = $data['instructor'] ?? Auth::user()->name;
        $workshop->instructor_bio = $data['instructor_bio'] ?? Auth::user()->chef_specialty_description;
        $workshop->instructor_avatar = Auth::user()->avatar;
        $workshop->is_featured = false;
        $workshop->save();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم إنشاء الورشة بنجاح! يمكنك متابعة حالة الحجوزات من لوحة التحكم.');
    }

    public function edit(Workshop $workshop): \Illuminate\View\View
    {
        $this->authorizeWorkshop($workshop);

        return view('chef.workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $this->authorizeWorkshop($workshop);

        $data = $this->validateWorkshop($request, $workshop->id);
        $this->fillWorkshopData($workshop, $data);
        $this->handleImageUpload($request, $workshop);
        $this->applyMeetingProvider($request, $workshop, $data['meeting_link'] ?? $workshop->meeting_link);
        $workshop->instructor = $data['instructor'] ?? $workshop->instructor ?? Auth::user()->name;
        $workshop->instructor_bio = $data['instructor_bio'] ?? $workshop->instructor_bio ?? Auth::user()->chef_specialty_description;
        $workshop->save();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم تحديث الورشة بنجاح!');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->authorizeWorkshop($workshop);

        if ($workshop->image) {
            EnhancedImageUploadService::deleteImage($workshop->image);
        }

        $workshop->delete();

        return redirect()
            ->route('chef.workshops.index')
            ->with('success', 'تم حذف الورشة بنجاح.');
    }

    public function join(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);

        if (!$workshop->is_online || !$workshop->meeting_link) {
            return redirect()
                ->route('chef.workshops.index')
                ->with('error', 'هذه الورشة ليست أونلاين أو أن رابط الاجتماع غير متاح.');
        }

        if (!in_array($workshop->meeting_provider, ['jitsi', 'jaas'], true)) {
            return redirect()->away($workshop->meeting_link);
        }

        $currentUser = Auth::user();
        if ($workshop->meeting_provider === 'jaas') {
            $missingJaasConfig = $this->getMissingJaasConfigKeys();

            if (!empty($missingJaasConfig)) {
                Log::error('JaaS configuration is incomplete for chef workshop join.', [
                    'workshop_id' => $workshop->id,
                    'user_id' => $currentUser?->id,
                    'missing_keys' => $missingJaasConfig,
                ]);

                $message = 'لا يمكن فتح غرفة الاجتماع لأن إعدادات Jitsi JaaS غير مكتملة. يرجى التواصل مع فريق الدعم.';

                if ($workshop->meeting_link) {
                    $message .= ' يمكنك استخدام رابط الاجتماع الخارجي مؤقتاً: ' . $workshop->meeting_link;
                }

                return redirect()
                    ->route('chef.workshops.index')
                    ->with('error', $message);
            }
        }

        if ($redirect = $this->enforceHostJoinDeviceLock($request, $workshop)) {
            return $redirect;
        }

        try {
            $embedConfig = $this->buildJitsiEmbedConfig(
                $workshop,
                $currentUser?->name,
                $currentUser?->email,
                true
            );
        } catch (\Throwable $exception) {
            Log::error('Failed to build Jitsi embed configuration for chef workshop.', [
                'workshop_id' => $workshop->id,
                'user_id' => $currentUser?->id,
                'provider' => $workshop->meeting_provider,
                'exception_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('chef.workshops.index')
                ->with('error', 'تعذر تحميل غرفة الاجتماع بسبب خطأ غير متوقع. يرجى المحاولة لاحقاً أو التواصل مع فريق الدعم.');
        }

        $recentParticipants = $workshop->bookings()
            ->with('user:id,name,email')
            ->where('status', 'confirmed')
            ->orderByDesc('confirmed_at')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        return view('chef.workshops.join', [
            'workshop' => $workshop,
            'embedConfig' => $embedConfig,
            'user' => $currentUser,
            'recentParticipants' => $recentParticipants,
            'startsAtIso' => optional($workshop->start_date)->toIso8601String(),
        ]);
    }

    public function startMeeting(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);
        $meetingLockSupported = $this->meetingLockSupported();

        if (!$request->expectsJson() && !$workshop->meeting_started_at) {
            $request->validate([
                'confirm_host' => ['accepted'],
            ], [
                'confirm_host.accepted' => 'يرجى تأكيد أنك المضيف قبل بدء الاجتماع.',
            ]);
        }

        if (!$workshop->is_online || !$workshop->meeting_link) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن بدء اجتماع لورشة غير أونلاين أو بدون رابط جاهز.',
            ], 422);
        }

        $alreadyStarted = (bool) $workshop->meeting_started_at;

        if (!$alreadyStarted) {
            $workshop->meeting_started_at = now();
            $workshop->meeting_started_by = Auth::id();
        }

        if (!$workshop->meeting_started_by) {
            $workshop->meeting_started_by = Auth::id();
        }

        if ($meetingLockSupported && $workshop->meeting_locked_at !== null) {
            $workshop->meeting_locked_at = null;
        }

        $dirtyAttributes = ['meeting_started_at', 'meeting_started_by'];

        if ($meetingLockSupported) {
            $dirtyAttributes[] = 'meeting_locked_at';
        }

        if ($workshop->isDirty($dirtyAttributes)) {
            $workshop->save();
        }

        $payload = [
            'success' => true,
            'already_started' => $alreadyStarted,
            'started_at' => $workshop->meeting_started_at->toIso8601String(),
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with('success', $alreadyStarted ? 'تم بدء الاجتماع مسبقاً.' : 'تم فتح الغرفة ويمكن للمشاركين الدخول الآن.');
    }

    public function updatePresence(Request $request, Workshop $workshop)
    {
        $this->authorizeWorkshop($workshop);

        $validated = $request->validate([
            'state' => ['required', Rule::in(['online', 'offline'])],
        ]);

        $state = $validated['state'];
        $meetingStarted = (bool) $workshop->meeting_started_at;
        $meetingLockSupported = $this->meetingLockSupported();

        if (!$meetingLockSupported) {
            $dirty = false;

            if ($state === 'online' && !$meetingStarted) {
                $workshop->meeting_started_at = now();
                $workshop->meeting_started_by = Auth::id();
                $dirty = true;
            }

            if ($state === 'online' && !$workshop->meeting_started_by) {
                $workshop->meeting_started_by = Auth::id();
                $dirty = true;
            }

            if ($dirty) {
                $workshop->save();
            }

            return response()->json([
                'success' => true,
                'meeting_started' => (bool) $workshop->meeting_started_at,
                'meeting_locked' => false,
                'locked_at' => null,
            ]);
        }

        if (!$meetingStarted) {
            if ($state === 'offline' && $workshop->meeting_locked_at !== null) {
                $workshop->meeting_locked_at = null;
                $workshop->save();
            }

            return response()->json([
                'success' => true,
                'meeting_started' => false,
                'meeting_locked' => (bool) $workshop->meeting_locked_at,
                'locked_at' => optional($workshop->meeting_locked_at)->toIso8601String(),
            ]);
        }

        $lockChanged = false;

        if ($state === 'online') {
            if ($workshop->meeting_locked_at !== null) {
                $workshop->meeting_locked_at = null;
                $lockChanged = true;
            }
        } else {
            if ($workshop->meeting_locked_at === null) {
                $workshop->meeting_locked_at = now();
                $lockChanged = true;
            }
        }

        $starterChanged = false;

        if (!$workshop->meeting_started_by && $state === 'online') {
            $workshop->meeting_started_by = Auth::id();
            $starterChanged = true;
        }

        if ($lockChanged || $starterChanged) {
            $workshop->save();
        }

        return response()->json([
            'success' => true,
            'meeting_started' => true,
            'meeting_locked' => (bool) $workshop->meeting_locked_at,
            'locked_at' => optional($workshop->meeting_locked_at)->toIso8601String(),
        ]);
    }

    public function generateMeetingLink(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
        ]);

        $startsAt = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;

        $meeting = $this->jitsiMeetingService->createMeeting(
            $validated['title'],
            Auth::id(),
            $startsAt
        );

        return response()->json([
            'success' => true,
            'meeting_link' => $meeting['url'],
            'room' => $meeting['room'],
            'passcode' => $meeting['passcode'],
        ]);
    }

    protected function enforceHostJoinDeviceLock(Request $request, Workshop $workshop): ?RedirectResponse
    {
        if (!$this->hostDeviceLockSupported()) {
            return null;
        }

        $currentUser = Auth::user();

        if ($currentUser && method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin()) {
            return null;
        }

        $cookieName = $this->getHostJoinDeviceCookieName($workshop);
        $storedTokenHash = $workshop->host_join_device_token;
        $fingerprint = $this->makeDeviceFingerprint($request);

        if ($storedTokenHash) {
            $cookieToken = $request->cookie($cookieName);

            if (!is_string($cookieToken) || $cookieToken === '') {
                return $this->denyHostJoinFromUnrecognizedDevice($workshop, $request, 'missing_cookie');
            }

            $hashedCookieToken = hash('sha256', $cookieToken);

            if (!hash_equals($storedTokenHash, $hashedCookieToken)) {
                return $this->denyHostJoinFromUnrecognizedDevice($workshop, $request, 'cookie_mismatch');
            }

            if (!empty($workshop->host_join_device_fingerprint) && !hash_equals($workshop->host_join_device_fingerprint, $fingerprint)) {
                return $this->denyHostJoinFromUnrecognizedDevice($workshop, $request, 'fingerprint_mismatch');
            }

            return null;
        }

        $plainToken = Str::random(64);
        $firstJoinedAt = $workshop->host_first_joined_at ?: now();

        $workshop->forceFill([
            'host_first_joined_at' => $firstJoinedAt,
            'host_join_device_token' => hash('sha256', $plainToken),
            'host_join_device_fingerprint' => $fingerprint,
            'host_join_device_ip' => $request->ip(),
            'host_join_device_user_agent' => $this->truncateUserAgent($request->userAgent()),
        ])->save();

        Cookie::queue(
            cookie(
                $cookieName,
                $plainToken,
                60 * 24 * 365,
                '/',
                config('session.domain'),
                config('session.secure', false),
                true,
                false,
                config('session.same_site', 'lax')
            )
        );

        return null;
    }

    protected function getHostJoinDeviceCookieName(Workshop $workshop): string
    {
        return 'wasfah_host_workshop_device_' . strtolower((string) $workshop->id);
    }

    protected function makeDeviceFingerprint(Request $request): string
    {
        $userAgent = (string) $request->userAgent();
        $acceptLanguage = (string) $request->header('accept-language', '');

        return hash('sha256', $userAgent . '|' . $acceptLanguage);
    }

    protected function truncateUserAgent(?string $userAgent): string
    {
        $agent = (string) $userAgent;

        if (function_exists('mb_substr')) {
            return mb_substr($agent, 0, 1024);
        }

        return substr($agent, 0, 1024);
    }

    protected function denyHostJoinFromUnrecognizedDevice(Workshop $workshop, Request $request, string $reason): RedirectResponse
    {
        Log::warning('Blocked chef workshop join from unrecognized device.', [
            'workshop_id' => $workshop->id,
            'user_id' => Auth::id(),
            'reason' => $reason,
            'request_ip' => $request->ip(),
            'request_user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('chef.workshops.index')
            ->with('error', 'لا يمكن فتح غرفة الورشة من جهاز مختلف. يرجى التواصل مع فريق الدعم لتحديث الوصول.');
    }

    protected function hostDeviceLockSupported(): bool
    {
        static $supported;

        if ($supported === null) {
            $supported = Schema::hasColumns('workshops', [
                'host_join_device_token',
                'host_join_device_fingerprint',
                'host_join_device_ip',
                'host_join_device_user_agent',
            ]);
        }

        return $supported;
    }

    protected function meetingLockSupported(): bool
    {
        static $supported;

        if ($supported === null) {
            $supported = Schema::hasColumns('workshops', [
                'meeting_started_at',
                'meeting_locked_at',
            ]);
        }

        return $supported;
    }

    protected function validateWorkshop(Request $request, ?int $workshopId = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'content' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:120'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'duration' => ['required', 'integer', 'min:30', 'max:600'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['JOD', 'AED', 'SAR'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_deadline' => ['nullable', 'date', 'before:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_online' => ['required', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'instructor_bio' => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'materials_needed' => ['nullable', 'string'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'auto_generate_meeting' => ['sometimes', 'boolean'],
        ];

        $messages = [
            'image.image' => 'يرجى اختيار ملف صورة صالح.',
            'image.mimes' => 'الصيغ المدعومة هي JPG, PNG, GIF أو WebP فقط.',
            'image.max' => 'حجم الصورة كبير جداً. الحد الأقصى هو 2 ميجابايت.',
        ];

        $data = $request->validate($rules, $messages);

        if (!empty($data['is_online']) && empty($data['meeting_link']) && !$request->boolean('auto_generate_meeting')) {
            throw ValidationException::withMessages([
                'meeting_link' => 'يرجى إدخال رابط اجتماع أو اختيار خيار توليد رابط Jitsi تلقائياً.',
            ]);
        }

        if (empty($data['is_online']) && empty($data['location'])) {
            throw ValidationException::withMessages([
                'location' => 'يرجى تحديد موقع الورشة الحضورية.',
            ]);
        }

        return $data;
    }

    protected function fillWorkshopData(Workshop $workshop, array $data): void
    {
        $workshop->fill([
            'title' => $data['title'],
            'description' => $data['description'],
            'content' => $data['content'] ?? null,
            'category' => $data['category'],
            'level' => $data['level'],
            'duration' => $data['duration'],
            'max_participants' => $data['max_participants'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'registration_deadline' => $data['registration_deadline'] ?? null,
            'location' => $data['location'] ?? null,
            'address' => $data['address'] ?? null,
            'what_you_will_learn' => $data['what_you_will_learn'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'materials_needed' => $data['materials_needed'] ?? null,
            'is_online' => (bool) $data['is_online'],
            'is_active' => isset($data['is_active'])
                ? (bool) $data['is_active']
                : false,
        ]);

        if (!$workshop->is_online && empty($workshop->location)) {
            $workshop->location = 'سيتم تحديد الموقع لاحقاً';
        }
    }

    protected function handleImageUpload(Request $request, Workshop $workshop): void
    {
        if ($request->hasFile('image')) {
            if ($workshop->image) {
                EnhancedImageUploadService::deleteImage($workshop->image);
            }

            $uploadResult = EnhancedImageUploadService::uploadImage(
                $request->file('image'),
                'workshops',
                85,
                1200,
                1200
            );

            if (!$uploadResult['success']) {
                throw ValidationException::withMessages([
                    'image' => $uploadResult['error'] ?? 'تعذر رفع الصورة، يرجى المحاولة مرة أخرى.',
                ]);
            }

            $workshop->image = $uploadResult['path'];
        } elseif ($request->boolean('remove_image') && $workshop->image) {
            EnhancedImageUploadService::deleteImage($workshop->image);
            $workshop->image = null;
        }
    }

    protected function applyMeetingProvider(Request $request, Workshop $workshop, ?string $inputLink = null): void
    {
        $supportsHostLock = $this->hostDeviceLockSupported();
        $supportsMeetingLock = $this->meetingLockSupported();

        if (!$workshop->is_online) {
            $workshop->meeting_link = null;
            $workshop->meeting_provider = 'manual';
            $workshop->jitsi_room = null;
            $workshop->jitsi_passcode = null;
            $workshop->meeting_started_at = null;
            $workshop->meeting_started_by = null;
            if ($supportsMeetingLock) {
                $workshop->meeting_locked_at = null;
            }

            if ($supportsHostLock) {
                $workshop->host_first_joined_at = null;
                $workshop->host_join_device_token = null;
                $workshop->host_join_device_fingerprint = null;
                $workshop->host_join_device_ip = null;
                $workshop->host_join_device_user_agent = null;
            }
            return;
        }

        $autoGenerate = $request->boolean('auto_generate_meeting');

        $workshop->meeting_started_at = null;
        $workshop->meeting_started_by = null;
        if ($supportsMeetingLock) {
            $workshop->meeting_locked_at = null;
        }

        if ($supportsHostLock) {
            $workshop->host_first_joined_at = null;
            $workshop->host_join_device_token = null;
            $workshop->host_join_device_fingerprint = null;
            $workshop->host_join_device_ip = null;
            $workshop->host_join_device_user_agent = null;
        }

        if ($autoGenerate || empty($inputLink)) {
            $meeting = $this->jitsiMeetingService->createMeeting(
                $workshop->title,
                Auth::id(),
                $workshop->start_date instanceof Carbon ? $workshop->start_date : Carbon::parse($workshop->start_date)
            );

            $workshop->meeting_link = $meeting['url'];
            $workshop->meeting_provider = $meeting['provider'] ?? 'jitsi';
            $workshop->jitsi_room = $meeting['room'];
            $workshop->jitsi_passcode = $meeting['passcode'];
            $workshop->location = $workshop->location ?: 'أونلاين عبر Jitsi Meet';
        } else {
            $workshop->meeting_link = $inputLink;
            $workshop->meeting_provider = 'manual';
            $workshop->jitsi_room = null;
            $workshop->jitsi_passcode = null;
        }
    }

    protected function authorizeWorkshop(Workshop $workshop): void
    {
        if ($workshop->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الورشة.');
        }
    }

    protected function getMissingJaasConfigKeys(): array
    {
        $jaasConfig = config('services.jitsi.jaas', []);

        $requiredKeys = ['app_id', 'api_key', 'private_key_path'];
        $missing = [];

        foreach ($requiredKeys as $key) {
            $value = $jaasConfig[$key] ?? null;

            if (blank($value)) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    protected function buildJitsiEmbedConfig(Workshop $workshop, ?string $displayName = null, ?string $email = null, bool $isModerator = true): array
    {
        $provider = $workshop->meeting_provider ?: config('services.jitsi.provider', 'meet');

        if ($provider === 'jaas') {
            $jaasConfig = config('services.jitsi.jaas', []);
            $baseUrl = $jaasConfig['base_url'] ?? 'https://8x8.vc';
            $parsedBase = parse_url($baseUrl);
            $domain = $parsedBase['host'] ?? '8x8.vc';
            $scheme = $parsedBase['scheme'] ?? 'https';
            $appId = $jaasConfig['app_id'] ?? null;

            if (!$appId) {
                throw new \RuntimeException('JaaS app ID is not configured. Please set JITSI_JAAS_APP_ID.');
            }

            $roomSlug = trim($workshop->jitsi_room ?? '');
            if ($roomSlug === '') {
                $roomSlug = Str::slug($workshop->title . '-' . $workshop->id, '-');
            }
            $roomSlug = trim(str_replace(' ', '-', $roomSlug), '/');

            $roomPath = "{$appId}/{$roomSlug}";
            $tokenService = app(\App\Services\JitsiJaasTokenService::class);
            $allowParticipantSubject = (bool) config('services.jitsi.allow_participant_subject_edit', true);
            $shouldIssueModeratorToken = $isModerator || (!$isModerator && $allowParticipantSubject);
            $userContext = [
                'name' => $displayName,
                'email' => $email,
            ];
            $jwt = $shouldIssueModeratorToken
                ? $tokenService->createModeratorToken($roomSlug, $userContext, $workshop->start_date)
                : $tokenService->createParticipantToken($roomSlug, $userContext, $workshop->start_date);

            return [
                'provider' => 'jaas',
                'domain' => $domain,
                'room' => $roomPath,
                'jwt' => $jwt,
                'passcode' => null,
                'external_api_url' => "{$scheme}://{$domain}/external_api.js",
            ];
        }

        $meetingUrl = $workshop->meeting_link;
        $parsedMeeting = $meetingUrl ? parse_url($meetingUrl) : [];
        $fallbackBase = parse_url(config('services.jitsi.base_url', 'https://meet.jit.si'));

        $domain = $parsedMeeting['host']
            ?? ($fallbackBase['host'] ?? 'meet.jit.si');

        $scheme = $parsedMeeting['scheme']
            ?? ($fallbackBase['scheme'] ?? 'https');

        $room = $workshop->jitsi_room
            ?? ltrim($parsedMeeting['path'] ?? '', '/')
            ?? Str::slug($workshop->title . '-' . $workshop->id, '-');

        $room = trim($room);

        if (str_contains($room, '/')) {
            $segments = array_filter(explode('/', $room));
            $room = end($segments);
        }

        if (!$room) {
            $room = Str::slug($workshop->title . '-' . $workshop->id, '-');
        }

        return [
            'domain' => $domain,
            'room' => $room,
            'passcode' => $workshop->jitsi_passcode,
            'external_api_url' => "{$scheme}://{$domain}/external_api.js",
        ];
    }
}
