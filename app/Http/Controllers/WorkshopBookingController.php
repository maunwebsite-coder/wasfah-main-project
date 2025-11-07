<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\Notification;
use App\Services\WorkshopLinkSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkshopBookingController extends Controller
{
    public function __construct(
        protected WorkshopLinkSecurityService $linkSecurity,
    ) {
    }

    public function store(Request $request)
    {
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $workshop = Workshop::findOrFail($request->workshop_id);

        // التحقق من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        // التحقق من أن الورشة نشطة ومتاحة للحجز
        if (!$workshop->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الورشة غير متاحة للحجز'
            ], 400);
        }

        if ($workshop->is_fully_booked) {
            return response()->json([
                'success' => false,
                'message' => 'الورشة مكتملة العدد'
            ], 400);
        }

        if (!$workshop->is_registration_open) {
            return response()->json([
                'success' => false,
                'message' => 'انتهى موعد التسجيل'
            ], 400);
        }

        // التحقق من عدم وجود حجز سابق لنفس المستخدم
        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
                                         ->where('user_id', Auth::id())
                                         ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'لديك حجز سابق في هذه الورشة'
            ], 400);
        }

        // إنشاء الحجز
        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => $workshop->price,
            'notes' => $request->notes,
        ]);

        // إنشاء إشعار للمستخدم
        Notification::createNotification(
            Auth::id(),
            'workshop_booking',
            'تم إرسال طلب حجز الورشة',
            "تم إرسال طلب حجز ورشة '{$workshop->title}' بنجاح. يرجى التحقق من ملفك الشخصي لمتابعة حالة الحجز والورشات المحجوزة.",
            [
                'workshop_id' => $workshop->id,
                'workshop_slug' => $workshop->slug,
                'booking_id' => $booking->id,
                'action_url' => route('bookings.show', ['booking' => $booking->id]),
            ]
        );

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب الحجز بنجاح. يمكنك الآن الدخول إلى حسابك الشخصي لرؤية الورشات المحجوزة.',
            'booking' => $booking
        ]);
    }

    public function index()
    {
        $bookings = WorkshopBooking::with(['workshop', 'user'])
                                  ->where('user_id', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(WorkshopBooking $booking)
    {
        $this->ensureBookingOwner($booking);
        return view('bookings.show', compact('booking'));
    }

    public function cancel(WorkshopBooking $booking)
    {
        $this->ensureBookingOwner($booking);

        // التحقق من إمكانية الإلغاء
        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 400);
        }

        if ($booking->status === 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء حجز مؤكد'
            ], 400);
        }

        // إلغاء الحجز
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'تم الإلغاء من قبل المستخدم'
        ]);

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح'
        ]);
    }

    /**
     * عرض غرفة الاجتماع داخل موقع وصفة دون مشاركة الرابط الخارجي.
     */
    public function join(Request $request, WorkshopBooking $booking)
    {
        if (!$request->hasValidSignature()) {
            if (!$request->query->has('signature')) {
                return redirect()->to(
                    $this->linkSecurity->makeParticipantJoinUrl($booking)
                );
            }

            abort(403, 'رابط الانضمام غير صالح أو منتهي الصلاحية.');
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'يجب تسجيل الدخول للوصول إلى غرفة الورشة.');
        }

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        $booking->load('workshop');
        $workshop = $booking->workshop;

        if ($booking->status !== 'confirmed') {
            if ($user && $booking->user_id === $user->id) {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('error', 'لا يمكنك الدخول للورشة قبل تأكيد الحجز.');
            }

            abort(403, 'لا يمكنك الدخول للورشة قبل تأكيد الحجز.');
        }

        if (!$workshop || !$workshop->is_online || !$workshop->meeting_link) {
            if ($user && $booking->user_id === $user->id) {
                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('error', 'هذه الورشة ليست أونلاين أو أن رابط الاجتماع غير متاح حالياً.');
            }

            abort(404, 'هذه الورشة ليست أونلاين أو أن رابط الاجتماع غير متاح حالياً.');
        }

        if (!in_array($workshop->meeting_provider, ['jitsi', 'jaas'], true)) {
            return redirect()->away($workshop->meeting_link);
        }

        $workshop->loadMissing('chef');
        $workshop->loadCount([
            'bookings as confirmed_bookings_count' => fn ($query) => $query->where('status', 'confirmed'),
        ]);

        if ($redirect = $this->enforceJoinDeviceLock($request, $booking)) {
            return $redirect;
        }

        $hostName = $workshop->instructor ?: optional($workshop->chef)->name;
        $requestedName = trim((string) $request->query('name', ''));
        $guestDisplayName = $requestedName !== '' ? $requestedName : 'ضيف وصفة';
        $shouldPromptForDisplayName = !$user;
        $effectiveName = $user?->name ?? $guestDisplayName;

        if ($workshop->meeting_provider === 'jaas') {
            $missingJaasConfig = $this->getMissingJaasConfigKeys();

            if (!empty($missingJaasConfig)) {
                Log::error('JaaS configuration is incomplete for booking join.', [
                    'booking_id' => $booking->id,
                    'workshop_id' => $workshop->id,
                    'user_id' => $user?->id,
                    'missing_keys' => $missingJaasConfig,
                ]);

                return redirect()
                    ->route('bookings.show', $booking)
                    ->with('error', 'لا يمكن فتح غرفة الاجتماع حالياً بسبب مشكلة في الإعدادات. يرجى التواصل مع فريق الدعم أو المحاولة لاحقاً.');
            }
        }

        try {
            $embedConfig = $this->buildJitsiEmbedConfig(
                $workshop,
                $effectiveName,
                $user?->email,
                false
            );
        } catch (\Throwable $exception) {
            Log::error('Failed to build Jitsi embed configuration for booking join.', [
                'booking_id' => $booking->id,
                'workshop_id' => $workshop->id,
                'user_id' => $user?->id,
                'provider' => $workshop->meeting_provider,
                'exception_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'حدث خطأ أثناء تجهيز غرفة الاجتماع. يرجى المحاولة لاحقاً أو التواصل مع فريق الدعم.');
        }

        unset($embedConfig['passcode']);

        $meetingLockSupported = $this->meetingLockSupported();

        return view('bookings.join', [
            'booking' => $booking,
            'workshop' => $workshop,
            'embedConfig' => $embedConfig,
            'user' => $user,
            'hostName' => $hostName,
            'startsAtIso' => optional($workshop->start_date)->toIso8601String(),
            'meetingStartedAtIso' => optional($workshop->meeting_started_at)->toIso8601String(),
            'meetingLockedAtIso' => $meetingLockSupported
                ? optional($workshop->meeting_locked_at)->toIso8601String()
                : null,
            'isMeetingLocked' => $meetingLockSupported ? (bool) $workshop->meeting_locked_at : false,
            'participantName' => $effectiveName,
            'participantEmail' => $user?->email,
            'shouldPromptForDisplayName' => $shouldPromptForDisplayName,
            'guestDisplayName' => $guestDisplayName,
            'supportsMeetingLock' => $meetingLockSupported,
            'secureJoinUrl' => $this->linkSecurity->makeParticipantJoinUrl($booking),
            'secureStatusUrl' => $this->linkSecurity->makeParticipantStatusUrl($booking),
        ]);
    }

    public function status(Request $request, WorkshopBooking $booking)
    {
        if (!$request->hasValidSignature()) {
            abort(403);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'meeting_started' => false,
                'started_at' => null,
                'message' => 'يجب تسجيل الدخول للوصول إلى حالة الورشة.',
            ], 401);
        }

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'meeting_started' => false,
                'started_at' => null,
            ], 403);
        }

        $booking->load('workshop');
        $workshop = $booking->workshop;

        return response()->json([
            'meeting_started' => (bool) ($workshop?->meeting_started_at),
            'started_at' => $workshop?->meeting_started_at?->toIso8601String(),
            'meeting_locked' => $this->meetingLockSupported() && $workshop
                ? (bool) $workshop->meeting_locked_at
                : false,
            'locked_at' => $this->meetingLockSupported() && $workshop
                ? $workshop->meeting_locked_at?->toIso8601String()
                : null,
        ]);
    }

    protected function enforceJoinDeviceLock(Request $request, WorkshopBooking $booking): ?\Illuminate\Http\RedirectResponse
    {
        $currentUser = $request->user();
        $booking->loadMissing('user');

        $bookingEmail = strtolower((string) optional($booking->user)->email);
        $currentEmail = strtolower((string) ($currentUser?->email ?? ''));
        $deviceLockSupported = $this->bookingDeviceLockSupported();
        $allowSameEmailBypass = $this->allowsSameEmailMultiDevice();

        // Allow multiple devices when the booking owner authenticates with the same email and the feature is enabled.
        if ($allowSameEmailBypass && $bookingEmail !== '' && $currentEmail !== '' && hash_equals($bookingEmail, $currentEmail)) {
            if ($deviceLockSupported) {
                $updates = [];

                if (!$booking->first_joined_at) {
                    $updates['first_joined_at'] = now();
                }

                if (!$booking->join_device_ip) {
                    $updates['join_device_ip'] = $request->ip();
                }

                if (!$booking->join_device_user_agent) {
                    $updates['join_device_user_agent'] = $this->truncateUserAgent($request->userAgent());
                }

                if (!empty($updates)) {
                    $booking->forceFill($updates)->save();
                }
            }

            return null;
        }

        if (!$deviceLockSupported) {
            return null;
        }

        $cookieName = $this->getJoinDeviceCookieName($booking);
        $storedTokenHash = $booking->join_device_token;
        $fingerprint = $this->makeDeviceFingerprint($request);

        if ($storedTokenHash) {
            $cookieToken = $request->cookie($cookieName);

            if (!is_string($cookieToken) || $cookieToken === '') {
                return $this->denyJoinFromUnrecognizedDevice($booking, $request, 'missing_cookie');
            }

            $hashedCookieToken = hash('sha256', $cookieToken);

            if (!hash_equals($storedTokenHash, $hashedCookieToken)) {
                return $this->denyJoinFromUnrecognizedDevice($booking, $request, 'cookie_mismatch');
            }

            if (!empty($booking->join_device_fingerprint) && !hash_equals($booking->join_device_fingerprint, $fingerprint)) {
                return $this->denyJoinFromUnrecognizedDevice($booking, $request, 'fingerprint_mismatch');
            }

            return null;
        }

        $plainToken = Str::random(64);
        $firstJoinedAt = $booking->first_joined_at ?: now();

        $booking->forceFill([
            'first_joined_at' => $firstJoinedAt,
            'join_device_token' => hash('sha256', $plainToken),
            'join_device_fingerprint' => $fingerprint,
            'join_device_ip' => $request->ip(),
            'join_device_user_agent' => $this->truncateUserAgent($request->userAgent()),
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

    protected function getJoinDeviceCookieName(WorkshopBooking $booking): string
    {
        $code = $booking->public_code ?: $booking->id;

        return 'wasfah_booking_device_' . strtolower((string) $code);
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

    protected function denyJoinFromUnrecognizedDevice(WorkshopBooking $booking, Request $request, string $reason): \Illuminate\Http\RedirectResponse
    {
        Log::warning('Blocked workshop booking join from unrecognized device.', [
            'booking_id' => $booking->id,
            'booking_public_code' => $booking->public_code,
            'user_id' => $booking->user_id,
            'reason' => $reason,
            'request_ip' => $request->ip(),
            'request_user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('error', 'لا يمكن فتح رابط الورشة من جهاز مختلف. يرجى التواصل مع فريق الدعم لتحديث الوصول.');
    }

    protected function bookingDeviceLockSupported(): bool
    {
        static $supported;

        if ($supported === null) {
            $supported = Schema::hasColumns('workshop_bookings', [
                'join_device_token',
                'join_device_fingerprint',
                'join_device_ip',
                'join_device_user_agent',
            ]);
        }

        return $supported;
    }

    protected function allowsSameEmailMultiDevice(): bool
    {
        return (bool) config('workshop-links.allow_same_email_multi_device', true);
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

    protected function ensureBookingOwner(WorkshopBooking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
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

    protected function buildJitsiEmbedConfig(Workshop $workshop, ?string $displayName = null, ?string $email = null, bool $isModerator = false): array
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
