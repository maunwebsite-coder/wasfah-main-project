<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkshopBookingController extends Controller
{
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
            ['workshop_id' => $workshop->id, 'booking_id' => $booking->id]
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

        $hostName = $workshop->instructor ?: optional($workshop->chef)->name;
        $requestedName = trim((string) $request->query('name', ''));
        $guestDisplayName = $requestedName !== '' ? $requestedName : 'ضيف وصفة';
        $shouldPromptForDisplayName = !$user;
        $effectiveName = $user?->name ?? $guestDisplayName;

        $embedConfig = $this->buildJitsiEmbedConfig(
            $workshop,
            $effectiveName,
            $user?->email,
            false
        );
        unset($embedConfig['passcode']);

        return view('bookings.join', [
            'booking' => $booking,
            'workshop' => $workshop,
            'embedConfig' => $embedConfig,
            'user' => $user,
            'hostName' => $hostName,
            'startsAtIso' => optional($workshop->start_date)->toIso8601String(),
            'meetingStartedAtIso' => optional($workshop->meeting_started_at)->toIso8601String(),
            'meetingLockedAtIso' => optional($workshop->meeting_locked_at)->toIso8601String(),
            'isMeetingLocked' => (bool) $workshop->meeting_locked_at,
            'participantName' => $effectiveName,
            'participantEmail' => $user?->email,
            'shouldPromptForDisplayName' => $shouldPromptForDisplayName,
            'guestDisplayName' => $guestDisplayName,
        ]);
    }

    public function status(WorkshopBooking $booking)
    {
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
            'meeting_locked' => (bool) ($workshop?->meeting_locked_at),
            'locked_at' => $workshop?->meeting_locked_at?->toIso8601String(),
        ]);
    }

    protected function ensureBookingOwner(WorkshopBooking $booking): void
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
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
            $jwt = $isModerator
                ? $tokenService->createModeratorToken($roomSlug, [
                    'name' => $displayName,
                    'email' => $email,
                ], $workshop->start_date)
                : $tokenService->createParticipantToken($roomSlug, [
                    'name' => $displayName,
                    'email' => $email,
                ], $workshop->start_date);

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
