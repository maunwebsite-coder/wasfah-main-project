@extends('layouts.app')

@section('title', 'تشغيل ورشة: ' . $workshop->title)

@push('styles')
<style>
    .jitsi-shell {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .jitsi-wrapper {
        min-height: 75vh;
        height: clamp(640px, 85vh, 1100px);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 30px 60px -20px rgba(249, 115, 22, 0.28);
        border: 1px solid rgba(249, 115, 22, 0.18);
        width: 100%;
    }

    @media (max-width: 640px) {
        .jitsi-shell {
            gap: 0.75rem;
        }

        .jitsi-wrapper {
            min-height: 60vh;
            border-radius: 1rem;
        }

    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-amber-50 via-orange-50 to-white py-10 text-slate-900">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-green-200 bg-green-50 text-green-800' : 'border-rose-200 bg-rose-50 text-rose-700' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,2.15fr)_minmax(0,0.95fr)]">
            <div class="space-y-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-orange-500">غرفة الشيف</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $workshop->title }}</h1>
                </div>
                <p class="text-sm leading-relaxed text-slate-600">
                    هذه الغرفة خاصة بك كمضيف. استخدم لوحة التحكم أدناه لبدء الجلسة ومشاركة رابط الدخول مع المشاركين ومتابعة حالة الانضمام في الوقت الحقيقي.
                </p>
                <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 shadow-sm">
                        <i class="fas fa-video text-orange-500"></i>
                        {{ $workshop->is_online ? 'جلسة مباشرة عبر الإنترنت' : 'جلسة حضورية' }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 shadow-sm">
                        <i class="fas fa-chalkboard-teacher text-orange-500"></i>
                        المضيف: {{ $user->name }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 shadow-sm">
                        <i class="fas fa-signal text-orange-500"></i>
                        البث عبر {{ $workshop->meeting_provider === 'jitsi' ? 'Jitsi' : 'مزود خارجي' }}
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-orange-200 bg-white p-5 shadow-xl ring-1 ring-orange-100/60" id="countdownCard" @if ($startsAtIso) data-starts-at="{{ $startsAtIso }}" @endif>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-orange-500">موعد البدء</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">
                            {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') ?: 'لم يتم تحديد موعد ثابت' }}
                        </p>
                    </div>
                    <span id="countdownBadge" class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600 transition-colors">
                        {{ $startsAtIso ? 'جاهزون للبدء' : 'موعد مرن' }}
                    </span>
                </div>
                <p id="countdownLabel" class="mt-4 text-sm leading-relaxed text-slate-600">
                    {{ $startsAtIso ? 'يتم حساب الوقت المتبقي تلقائياً.' : 'يمكنك فتح الغرفة حالما تكون جاهزاً لبدء الورشة.' }}
                </p>
                <p class="mt-6 flex items-center gap-2 rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-xs text-orange-700">
                    <i class="fas fa-info-circle text-orange-400"></i>
                    بعد بدء الاجتماع سيتم إخطار المشاركين تلقائياً ويصبح بإمكانهم الدخول من صفحة الحجز الخاصة بهم.
                </p>
            </div>
        </div>

        <div class="jitsi-shell mb-10" id="jitsi-shell">
            <div class="jitsi-wrapper bg-slate-950" id="jitsi-container"></div>
        </div>

        <div class="mb-8">
            <div class="rounded-3xl border border-orange-200 bg-white p-6 shadow-xl ring-1 ring-orange-100/60">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-orange-500">حالة الغرفة</p>
                        @if ($workshop->meeting_started_at)
                            <h2 class="mt-2 text-xl font-semibold text-slate-900">الغرفة مفتوحة للمشاركين</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                تم تفعيل الاجتماع {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}. يمكنك مراقبة المشتركين من داخل الغرفة أو إيقافها من لوحة التحكم إن لزم.
                            </p>
                        @else
                            <h2 class="mt-2 text-xl font-semibold text-slate-900">المشاركون بانتظارك للانضمام</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                بمجرد تأكيدك أنك المضيف والضغط على زر <strong>بدء الاجتماع الآن</strong> سيُفتح الرابط في صفحات المشاركين فوراً.
                            </p>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm text-orange-700">
                        <div class="flex items-center gap-2 text-xs text-orange-500">
                            <i class="fas fa-user-clock text-orange-500"></i>
                            آخر تحديث
                        </div>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ now()->locale('ar')->translatedFormat('d F Y • h:i a') }}
                        </p>
                    </div>
                </div>

                <form
                    id="startMeetingForm"
                    method="POST"
                    action="{{ route('chef.workshops.start', $workshop) }}"
                    class="mt-6 space-y-4"
                >
                    @csrf
                    @if (!$workshop->meeting_started_at)
                        <label for="confirmHostCheckbox" class="flex items-start gap-3 rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-xs text-orange-800 shadow-sm">
                            <input
                                type="checkbox"
                                name="confirm_host"
                                id="confirmHostCheckbox"
                                value="1"
                                required
                                class="mt-0.5 h-4 w-4 rounded border-orange-300 accent-orange-500 focus:ring-orange-400"
                            >
                            <span>
                                أؤكد أنني المضيف الرسمي لهذه الورشة وسأتأكد من جاهزية الغرفة والدعم للمشاركين أثناء البث المباشر.
                            </span>
                        </label>
                        @error('confirm_host')
                            <p class="text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    @endif

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-2.5 font-semibold text-white shadow-lg transition focus:outline-none focus:ring-2 focus:ring-orange-300 hover:from-amber-600 hover:to-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                        id="startMeetingButton"
                        data-requires-confirmation="{{ $workshop->meeting_started_at ? 'false' : 'true' }}"
                        @if ($workshop->meeting_started_at)
                            disabled
                            data-meeting-started="true"
                        @else
                            disabled
                        @endif
                    >
                        <i class="fas {{ $workshop->meeting_started_at ? 'fa-check' : 'fa-play' }}"></i>
                        <span class="start-button-label">
                            {{ $workshop->meeting_started_at ? 'الاجتماع قيد التشغيل' : 'بدء الاجتماع الآن' }}
                        </span>
                    </button>
                </form>
            </div>
        </div>

        @if ($recentParticipants->isNotEmpty())
            <div class="mb-10 rounded-3xl border border-orange-200 bg-white p-6 shadow-xl ring-1 ring-orange-100/60">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-slate-900">آخر المشاركين الذين أكدوا حضورهم</h3>
                    <span class="text-xs text-slate-500">يمكنك رؤية القائمة الكاملة من صفحة الحجوزات.</span>
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach ($recentParticipants as $participantBooking)
                        @php
                            $participantName = $participantBooking->user?->name ?? 'مشارك';
                            $initial = mb_strtoupper(mb_substr($participantName, 0, 1));
                            $timestamp = $participantBooking->confirmed_at ?? $participantBooking->updated_at ?? $participantBooking->created_at;
                            $humanTime = $timestamp ? $timestamp->locale('ar')->diffForHumans() : 'حديثاً';
                        @endphp
                        <li class="flex items-center justify-between gap-3 rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-sm font-semibold text-orange-600">
                                    {{ $initial }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $participantName }}</p>
                                    <p class="text-xs text-slate-500">{{ $participantBooking->user?->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500">{{ $humanTime }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-600">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-500">
                    <i class="fas fa-user-tie"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-wider text-orange-500">سيظهر اسمك للمشاركين كالتالي</p>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                </div>
            </div>
            <a
                href="{{ route('chef.workshops.index') }}"
                class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-orange-600 transition hover:bg-orange-50 hover:border-orange-300"
            >
                <i class="fas fa-arrow-right"></i>
                العودة لقائمة الورشات
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ $embedConfig['external_api_url'] }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const confirmHostCheckbox = document.getElementById('confirmHostCheckbox');
        const startMeetingButton = document.getElementById('startMeetingButton');
        const startMeetingForm = document.getElementById('startMeetingForm');
        const countdownCard = document.getElementById('countdownCard');
        const countdownLabel = document.getElementById('countdownLabel');
        const countdownBadge = document.getElementById('countdownBadge');
        const startsAtIso = countdownCard?.dataset.startsAt || null;
        const presenceUrl = @json(route('chef.workshops.presence', $workshop));
        const csrfToken = @json(csrf_token());
        let lastPresenceState = null;

        const sendPresence = (state, { keepalive = false, force = false } = {}) => {
            if (!presenceUrl) {
                return;
            }

            if (!force && lastPresenceState === state) {
                return;
            }

            lastPresenceState = state;

            const payload = new FormData();
            payload.append('_token', csrfToken);
            payload.append('state', state);

            const requestInit = {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
            };

            if (keepalive) {
                requestInit.keepalive = true;
            }

            fetch(presenceUrl, requestInit).catch(() => {
                // Network hiccups are non-blocking; we'll retry on the next event.
            });
        };

        const sendPresenceBeacon = (state) => {
            if (!presenceUrl) {
                return;
            }

            lastPresenceState = state;

            const payload = new FormData();
            payload.append('_token', csrfToken);
            payload.append('state', state);

            if (navigator.sendBeacon) {
                navigator.sendBeacon(presenceUrl, payload);
                return;
            }

            fetch(presenceUrl, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                keepalive: true,
            }).catch(() => {
                // Best-effort fallback during page unload.
            });
        };

        if (startMeetingButton?.dataset.requiresConfirmation === 'true') {
            const toggleStartButton = () => {
                const confirmed = Boolean(confirmHostCheckbox?.checked);
                startMeetingButton.disabled = !confirmed;
            };

            toggleStartButton();
            confirmHostCheckbox?.addEventListener('change', toggleStartButton);
        }

        startMeetingForm?.addEventListener('submit', () => {
            if (!startMeetingButton) {
                return;
            }

            startMeetingButton.disabled = true;
            startMeetingButton.dataset.loading = 'true';
            startMeetingButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>جاري تفعيل الغرفة...</span>';
        });

        if (startsAtIso && countdownLabel && countdownBadge) {
            const startDate = new Date(startsAtIso);
            if (!Number.isNaN(startDate.getTime())) {
                const rtf = new Intl.RelativeTimeFormat('ar', { numeric: 'auto' });

                const setBadgeState = (state) => {
                    countdownBadge.classList.remove(
                        'bg-orange-100', 'text-orange-600',
                        'bg-amber-100', 'text-amber-600',
                        'bg-rose-100', 'text-rose-600'
                    );

                    if (state === 'upcoming') {
                        countdownBadge.classList.add('bg-orange-100', 'text-orange-600');
                    } else if (state === 'soon') {
                        countdownBadge.classList.add('bg-amber-100', 'text-amber-600');
                    } else {
                        countdownBadge.classList.add('bg-rose-100', 'text-rose-600');
                    }
                };

                const getRelativeParts = (diffSeconds) => {
                    const units = [
                        { limit: 60, inSeconds: 1, name: 'second' },
                        { limit: 3600, inSeconds: 60, name: 'minute' },
                        { limit: 86400, inSeconds: 3600, name: 'hour' },
                        { limit: Infinity, inSeconds: 86400, name: 'day' },
                    ];

                    const absolute = Math.abs(diffSeconds);
                    for (const unit of units) {
                        if (absolute < unit.limit) {
                            const value = Math.round(diffSeconds / unit.inSeconds);
                            return { value, unit: unit.name };
                        }
                    }
                    return { value: 0, unit: 'minute' };
                };

                const updateCountdown = () => {
                    const now = new Date();
                    const diffMs = startDate.getTime() - now.getTime();
                    const diffSeconds = diffMs / 1000;

                    if (Math.abs(diffSeconds) < 45) {
                        countdownLabel.textContent = diffMs >= 0
                            ? 'حان الوقت لبدء الاجتماع. اضغط زر البدء عندما تكون جاهزاً.'
                            : 'مر الموعد الأساسي. يمكنك فتح الغرفة فوراً للمنضمين.';
                        setBadgeState(diffMs >= 0 ? 'soon' : 'late');
                        countdownBadge.textContent = diffMs >= 0 ? 'الآن' : 'تجاوزنا الموعد';
                        return;
                    }

                    const { value, unit } = getRelativeParts(diffSeconds);
                    const relative = rtf.format(value, unit);

                    if (diffSeconds > 0) {
                        countdownLabel.textContent = `يبدأ ${relative}`;
                        countdownBadge.textContent = diffSeconds < 3600 ? 'اقترب الوقت' : 'جاهزون للبدء';
                        setBadgeState(diffSeconds < 3600 ? 'soon' : 'upcoming');
                    } else {
                        countdownLabel.textContent = `بدأ ${relative}`;
                        countdownBadge.textContent = 'تجاوزنا الموعد';
                        setBadgeState('late');
                    }
                };

                updateCountdown();
                setInterval(updateCountdown, 60000);
            }
        }

        const container = document.getElementById('jitsi-container');

        if (typeof JitsiMeetExternalAPI === 'undefined' || !container) {
            alert('تعذر تحميل غرفة الاجتماع. يرجى إعادة تحديث الصفحة أو التحقق من الاتصال.');
            return;
        }

        const domain = @json($embedConfig['domain']);
        const initialHeight = container.offsetHeight || 640;

        const essentialToolbar = [
            'microphone',
            'camera',
            'desktop',
            'chat',
            'raisehand',
            'tileview',
            'fullscreen',
            'settings',
            'hangup',
        ];

        const options = {
            roomName: @json($embedConfig['room']),
            parentNode: container,
            width: '100%',
            height: initialHeight,
            lang: 'ar',
            configOverwrite: {
                prejoinPageEnabled: false,
                prejoinConfig: {
                    enabled: false,
                    hideDisplayName: true,
                    hideExtraJoinButtons: ['microsoft', 'google', 'email', 'call-in'],
                },
                requireDisplayName: false,
                enableWelcomePage: false,
                enableClosePage: false,
                enableUserRolesBasedOnToken: false,
                disableDeepLinking: true,
                startWithAudioMuted: false,
                startWithVideoMuted: false,
                disableInviteFunctions: true,
                disableSelfViewSettings: true,
                disableReactions: true,
                toolbarButtons: essentialToolbar,
            },
            interfaceConfigOverwrite: {
                SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                LANG_DETECTION: false,
                DEFAULT_REMOTE_DISPLAY_NAME: 'مشارك',
                DEFAULT_LOCAL_DISPLAY_NAME: 'أنا',
                FILM_STRIP_MAX_HEIGHT: 120,
                SETTINGS_SECTIONS: ['devices'],
                TOOLBAR_BUTTONS: essentialToolbar,
            },
            userInfo: {
                displayName: @json($user->name),
                email: @json($user->email),
            },
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        api.addListener('videoConferenceJoined', () => {
            sendPresence('online');
        });

        api.addListener('videoConferenceLeft', () => {
            sendPresence('offline', { force: true });
        });

        api.addListener('readyToClose', () => {
            sendPresence('offline', { force: true });
        });

        function resizeJitsi() {
            const width = container.offsetWidth;
            const height = container.offsetHeight || initialHeight;
            api?.resize(width, height);
        }

        window.addEventListener('resize', resizeJitsi);
        resizeJitsi();
        window.addEventListener('beforeunload', () => {
            sendPresenceBeacon('offline');
        });
        window.addEventListener('pagehide', () => {
            sendPresenceBeacon('offline');
        });

        @if ($embedConfig['passcode'])
        api.addListener('passwordRequired', () => {
            api.executeCommand('password', @json($embedConfig['passcode']));
        });
        @endif

    });
</script>
@endpush
