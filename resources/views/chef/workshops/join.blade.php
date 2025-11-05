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
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
    }

    .jitsi-controls {
        display: flex;
        justify-content: flex-end;
    }

    .fullscreen-btn {
        position: static;
        background: rgba(15, 23, 42, 0.75);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(6px);
        border-radius: 999px;
        padding: 0.35rem 1rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: background 0.2s ease;
    }

    .fullscreen-btn:hover {
        background: rgba(15, 23, 42, 0.92);
    }

    @media (max-width: 640px) {
        .jitsi-shell {
            gap: 0.75rem;
        }

        .jitsi-wrapper {
            min-height: 60vh;
            border-radius: 1rem;
        }

        .jitsi-controls {
            justify-content: center;
        }

    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 py-10 text-white">
    <div class="mx-auto max-w-5xl px-4">
        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-emerald-300 bg-emerald-500/10 text-emerald-100' : 'border-rose-300 bg-rose-500/10 text-rose-100' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
            <div class="space-y-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">غرفة الشيف</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $workshop->title }}</h1>
                </div>
                <p class="text-sm leading-relaxed text-slate-300">
                    هذه الغرفة خاصة بك كمضيف. استخدم لوحة التحكم أدناه لبدء الجلسة ومشاركة رابط الدخول مع المشاركين ومتابعة حالة الانضمام في الوقت الحقيقي.
                </p>
                <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-300">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                        <i class="fas fa-video text-emerald-300"></i>
                        {{ $workshop->is_online ? 'جلسة مباشرة عبر الإنترنت' : 'جلسة حضورية' }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                        <i class="fas fa-chalkboard-teacher text-emerald-300"></i>
                        المضيف: {{ $user->name }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                        <i class="fas fa-signal text-emerald-300"></i>
                        البث عبر {{ $workshop->meeting_provider === 'jitsi' ? 'Jitsi' : 'مزود خارجي' }}
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-indigo-400/40 bg-indigo-900/40 p-5 shadow-xl" id="countdownCard" @if ($startsAtIso) data-starts-at="{{ $startsAtIso }}" @endif>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-indigo-200">موعد البدء</p>
                        <p class="mt-2 text-lg font-semibold text-white">
                            {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') ?: 'لم يتم تحديد موعد ثابت' }}
                        </p>
                    </div>
                    <span id="countdownBadge" class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200 transition-colors">
                        {{ $startsAtIso ? 'جاهزون للبدء' : 'موعد مرن' }}
                    </span>
                </div>
                <p id="countdownLabel" class="mt-4 text-sm leading-relaxed text-slate-200">
                    {{ $startsAtIso ? 'يتم حساب الوقت المتبقي تلقائياً.' : 'يمكنك فتح الغرفة حالما تكون جاهزاً لبدء الورشة.' }}
                </p>
                <p class="mt-6 flex items-center gap-2 rounded-2xl border border-slate-700/60 bg-slate-900/50 px-4 py-3 text-xs text-slate-300">
                    <i class="fas fa-info-circle text-indigo-200"></i>
                    بعد بدء الاجتماع سيتم إخطار المشاركين تلقائياً ويصبح بإمكانهم الدخول من صفحة الحجز الخاصة بهم.
                </p>
            </div>
        </div>

        <div class="jitsi-shell mb-10" id="jitsi-shell">
            <div class="jitsi-wrapper bg-black" id="jitsi-container"></div>
            <div class="jitsi-controls">
                <button type="button" class="fullscreen-btn" id="fullscreenToggle">
                    <i class="fas fa-expand"></i>
                    ملء الشاشة
                </button>
            </div>
        </div>

        <div class="mb-8">
            <div class="rounded-3xl border border-slate-700/60 bg-slate-900/50 p-6 shadow-xl">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-400">حالة الغرفة</p>
                        @if ($workshop->meeting_started_at)
                            <h2 class="mt-2 text-xl font-semibold text-white">الغرفة مفتوحة للمشاركين</h2>
                            <p class="mt-1 text-sm text-slate-300">
                                تم تفعيل الاجتماع {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}. يمكنك مراقبة المشتركين من داخل الغرفة أو إيقافها من لوحة التحكم إن لزم.
                            </p>
                        @else
                            <h2 class="mt-2 text-xl font-semibold text-white">المشاركون بانتظارك للانضمام</h2>
                            <p class="mt-1 text-sm text-slate-300">
                                بمجرد تأكيدك أنك المضيف والضغط على زر <strong>بدء الاجتماع الآن</strong> سيُفتح الرابط في صفحات المشاركين فوراً.
                            </p>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 px-4 py-3 text-sm text-slate-200">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <i class="fas fa-user-clock text-emerald-300"></i>
                            آخر تحديث
                        </div>
                        <p class="mt-1 font-semibold text-white">
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
                        <label for="confirmHostCheckbox" class="flex items-start gap-3 rounded-2xl border border-slate-700/60 bg-slate-900/40 px-4 py-3 text-xs text-slate-100 shadow">
                            <input
                                type="checkbox"
                                name="confirm_host"
                                id="confirmHostCheckbox"
                                value="1"
                                required
                                class="mt-0.5 h-4 w-4 rounded border-slate-600 bg-slate-800 text-emerald-400 focus:ring-emerald-400"
                            >
                            <span>
                                أؤكد أنني المضيف الرسمي لهذه الورشة وسأتأكد من جاهزية الغرفة والدعم للمشاركين أثناء البث المباشر.
                            </span>
                        </label>
                        @error('confirm_host')
                            <p class="text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    @endif

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-6 py-2.5 font-semibold text-slate-900 shadow hover:from-emerald-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:cursor-not-allowed disabled:opacity-60"
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
            <div class="mb-10 rounded-3xl border border-slate-700/60 bg-slate-900/40 p-6 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-white">آخر المشاركين الذين أكدوا حضورهم</h3>
                    <span class="text-xs text-slate-400">يمكنك رؤية القائمة الكاملة من صفحة الحجوزات.</span>
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach ($recentParticipants as $participantBooking)
                        @php
                            $participantName = $participantBooking->user?->name ?? 'مشارك';
                            $initial = mb_strtoupper(mb_substr($participantName, 0, 1));
                            $timestamp = $participantBooking->confirmed_at ?? $participantBooking->updated_at ?? $participantBooking->created_at;
                            $humanTime = $timestamp ? $timestamp->locale('ar')->diffForHumans() : 'حديثاً';
                        @endphp
                        <li class="flex items-center justify-between gap-3 rounded-2xl border border-slate-700/60 bg-slate-900/50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/15 text-sm font-semibold text-emerald-200">
                                    {{ $initial }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $participantName }}</p>
                                    <p class="text-xs text-slate-400">{{ $participantBooking->user?->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">{{ $humanTime }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-300">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">
                    <i class="fas fa-user-tie"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">سيظهر اسمك للمشاركين كالتالي</p>
                    <p class="font-semibold text-white">{{ $user->name }}</p>
                </div>
            </div>
            <a
                href="{{ route('chef.workshops.index') }}"
                class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-slate-200 transition hover:border-slate-500 hover:text-white"
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
                        'bg-emerald-500/20', 'text-emerald-200',
                        'bg-amber-500/20', 'text-amber-200',
                        'bg-rose-500/20', 'text-rose-100'
                    );

                    if (state === 'upcoming') {
                        countdownBadge.classList.add('bg-emerald-500/20', 'text-emerald-200');
                    } else if (state === 'soon') {
                        countdownBadge.classList.add('bg-amber-500/20', 'text-amber-200');
                    } else {
                        countdownBadge.classList.add('bg-rose-500/20', 'text-rose-100');
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
        const shell = document.getElementById('jitsi-shell');
        const fullscreenBtn = document.getElementById('fullscreenToggle');

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

        function resizeJitsi() {
            const width = container.offsetWidth;
            const height = container.offsetHeight || initialHeight;
            api?.resize(width, height);
        }

        window.addEventListener('resize', resizeJitsi);
        resizeJitsi();

        @if ($embedConfig['passcode'])
        api.addListener('passwordRequired', () => {
            api.executeCommand('password', @json($embedConfig['passcode']));
        });
        @endif

        const requestFullscreen = (element) => {
            if (element.requestFullscreen) return element.requestFullscreen();
            if (element.webkitRequestFullscreen) return element.webkitRequestFullscreen();
            if (element.mozRequestFullScreen) return element.mozRequestFullScreen();
            if (element.msRequestFullscreen) return element.msRequestFullscreen();
            return Promise.reject();
        };

        const exitFullscreen = () => {
            if (document.exitFullscreen) return document.exitFullscreen();
            if (document.webkitExitFullscreen) return document.webkitExitFullscreen();
            if (document.mozCancelFullScreen) return document.mozCancelFullScreen();
            if (document.msExitFullscreen) return document.msExitFullscreen();
            return Promise.reject();
        };

        const getFullscreenElement = () =>
            document.fullscreenElement
            || document.webkitFullscreenElement
            || document.mozFullScreenElement
            || document.msFullscreenElement;

        function updateFullscreenState() {
            const isFull = getFullscreenElement() === shell;
            fullscreenBtn.innerHTML = isFull
                ? '<i class="fas fa-compress"></i> إنهاء الملء'
                : '<i class="fas fa-expand"></i> ملء الشاشة';
        }

        const tryEnterFullscreen = () => {
            return requestFullscreen(shell)
                .catch(() => requestFullscreen(container))
                .catch(() => requestFullscreen(document.documentElement));
        };

        fullscreenBtn?.addEventListener('click', () => {
            const current = getFullscreenElement();
            if (current === shell || current === container || current === document.documentElement) {
                exitFullscreen().catch(() => {});
            } else {
                tryEnterFullscreen().catch(() => {
                    alert('لا يمكن تفعيل ملء الشاشة. تأكد من السماح للمتصفح أو جرّب مستعرضاً آخر.');
                });
            }
        });

        document.addEventListener('fullscreenchange', updateFullscreenState);
        document.addEventListener('webkitfullscreenchange', updateFullscreenState);
        document.addEventListener('mozfullscreenchange', updateFullscreenState);
        document.addEventListener('MSFullscreenChange', updateFullscreenState);
        updateFullscreenState();

    });
</script>
@endpush
