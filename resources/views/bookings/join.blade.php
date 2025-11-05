@extends('layouts.app')

@section('title', 'غرفة ورشة: ' . $workshop->title)

@push('styles')
<style>
    .jitsi-shell {
        position: relative;
    }

    .jitsi-wrapper {
        min-height: 72vh;
        height: clamp(620px, 78vh, 980px);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
    }

    @media (max-width: 640px) {
        .jitsi-wrapper {
            min-height: 65vh;
            border-radius: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 py-10 text-white">
    <div class="mx-auto max-w-7xl px-4">
        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-emerald-300 bg-emerald-500/10 text-emerald-100' : 'border-rose-300 bg-rose-500/10 text-rose-100' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,0.9fr)]">
            <div class="space-y-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">جلسة مباشرة</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $workshop->title }}</h1>
                </div>
                <p class="text-sm leading-relaxed text-slate-300">
                    تأكد من اتصالك بالإنترنت، ثم اسمح للمتصفح بالوصول إلى الميكروفون والكاميرا عند فتح الغرفة. ستظهر لك عناصر التحكم داخل البث عند الضغط على زر الانضمام.
                </p>
                <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-300">
                    @if ($hostName)
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                            <i class="fas fa-chalkboard-teacher text-emerald-300"></i>
                            مع المضيف: {{ $hostName }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                        <i class="fas fa-clock text-emerald-300"></i>
                        المدة: {{ $workshop->duration }} دقيقة تقريباً
                    </span>
                    @if ($workshop->confirmed_bookings_count)
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                            <i class="fas fa-users text-emerald-300"></i>
                            {{ number_format($workshop->confirmed_bookings_count) }} مشارك مؤكد
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-700/70 bg-slate-900/60 px-3 py-1">
                        <i class="fas fa-shield-alt text-emerald-300"></i>
                        دخول آمن عبر وصفة
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-indigo-400/40 bg-indigo-900/40 p-5 shadow-xl" id="countdownCard" @if ($startsAtIso) data-starts-at="{{ $startsAtIso }}" @endif>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-indigo-200">موعد الورشة</p>
                        <p class="mt-2 text-lg font-semibold text-white">
                            {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') ?: 'سيتم تحديده من قبل الشيف' }}
                        </p>
                    </div>
                    <span id="countdownBadge" class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200 transition-colors">
                        {{ $startsAtIso ? 'جاهزون تقريباً' : 'موعد مرن' }}
                    </span>
                </div>
                <p id="countdownLabel" class="mt-4 text-sm leading-relaxed text-slate-200">
                    {{ $startsAtIso ? 'يتم تحديث الوقت المتبقي تلقائياً.' : 'سيقوم الشيف بفتح الغرفة عندما يحين الوقت، ترقّب إشعار الانضمام.' }}
                </p>
                @if ($workshop->meeting_started_at)
                    <p class="mt-4 flex items-center gap-2 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-xs text-emerald-100">
                        <i class="fas fa-broadcast-tower"></i>
                        تم فتح الغرفة قبل {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}. انقر زر الانضمام داخل البث للمشاركة مباشرة.
                    </p>
                @endif
            </div>
        </div>

        @if ($workshop->meeting_started_at)
            <div class="mb-6 rounded-3xl border border-emerald-300/40 bg-emerald-500/10 px-6 py-4 text-sm text-emerald-100 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-emerald-200">
                        <i class="fas fa-check-circle"></i>
                        الغرفة مفتوحة الآن – يمكنك الانضمام متى ما شئت.
                    </span>
                    <span class="text-xs text-emerald-200/80">
                        إذا انقطع الاتصال، فقط أعد تحديث الصفحة وسيستمر البث تلقائياً.
                    </span>
                </div>
            </div>

            <div class="jitsi-shell" id="jitsi-shell">
                <div class="jitsi-wrapper bg-black relative" id="jitsi-container">
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-slate-700/60 bg-slate-900/40 px-5 py-4 text-xs text-slate-200 shadow">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-white">
                    <i class="fas fa-info-circle text-emerald-300"></i>
                    تذكير سريع بالضبط الصوتي
                </h3>
                <ul class="mt-3 space-y-2 leading-relaxed">
                    <li>• استخدم سماعات أو كتم الميكروفون عند عدم التحدث لتقليل الضوضاء.</li>
                    <li>• يمكنك تبديل العرض إلى طريقة الشبكة عبر زر <strong>عرض المربعات</strong> أسفل البث.</li>
                    <li>• إذا لم تسمع الصوت، افتح إعدادات Jitsi (رمز الترس) واختر الجهاز الصحيح.</li>
                </ul>
            </div>
        @else
            <div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
                <div id="waitingCard" class="rounded-3xl border border-indigo-200 bg-white/95 px-6 py-10 text-center text-slate-700 shadow-xl">
                    <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-500">
                        <i class="fas fa-door-closed text-2xl"></i>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-slate-900">ننتظر دخول الشيف</h2>
                    <p class="text-sm text-slate-500">
                        نغلق الغرفة إلى أن يؤكد الشيف بدء الورشة للحفاظ على خصوصية البث. أبقِ هذه الصفحة مفتوحة وسنحدثها بمجرد فتح الغرفة لك.
                    </p>
                    <div class="mt-6 flex flex-col items-center gap-4">
                        <div class="flex flex-col items-center gap-2 text-sm">
                            <span id="waitingCountdownBadge" class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 transition-colors">
                                {{ $startsAtIso ? 'سيبدأ قريباً' : 'بانتظار إشارة الشيف' }}
                            </span>
                            <span id="waitingCountdownLabel" class="text-xs text-slate-500">
                                {{ $startsAtIso ? 'يتم احتساب الوقت المتبقي.' : 'سنرسل تحديثاً فور فتح الغرفة.' }}
                            </span>
                        </div>
                        <button
                            type="button"
                            id="manualRefreshButton"
                            class="inline-flex items-center gap-2 rounded-full border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:border-indigo-300 hover:bg-indigo-50"
                        >
                            <i class="fas fa-sync"></i>
                            تحديث الحالة الآن
                        </button>
                        <div class="text-xs text-slate-400" id="pollStatusHint">
                            يتم التحقق من حالة الغرفة كل بضع ثوانٍ...
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-xs text-slate-200">
                    <div class="rounded-3xl border border-slate-700/60 bg-slate-900/40 p-5 shadow">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-white">
                            <i class="fas fa-check-square text-emerald-300"></i>
                            تأكد من جهوزيتك الآن
                        </h3>
                        <ul class="mt-3 space-y-2 leading-relaxed">
                            <li>• أغلق التطبيقات التي تستهلك الإنترنت (تحميلات، بث فيديو، ...).</li>
                            <li>• جرّب سماعات أو ميكروفوناً خارجياً إذا توفّر لتحصل على صوت أوضح.</li>
                            <li>• افتح الإضاءة أمامك إذا كنت ستشارك الكاميرا لتظهر بوضوح.</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-700/60 bg-slate-900/40 p-5 shadow">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-white">
                            <i class="fas fa-life-ring text-emerald-300"></i>
                            في حال تأخر فتح الغرفة
                        </h3>
                        <ul class="mt-3 space-y-2 leading-relaxed">
                            <li>• أعد تحديث الصفحة بعد مرور بضع دقائق.</li>
                            <li>• تحقق من بريدك الإلكتروني أو رسائلك من وصفة لأي تحديث من فريق الدعم.</li>
                            <li>• إن استمر الانتظار، تواصل مع الدعم من خلال صفحة المساعدة في حسابك.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-300">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">
                        <i class="fas fa-user"></i>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">اسمك في الغرفة</p>
                        <p class="font-semibold text-white" id="participantNameLabel">{{ $participantName }}</p>
                        @if ($shouldPromptForDisplayName)
                            <button
                                type="button"
                                id="editDisplayNameBtn"
                                class="mt-1 inline-flex items-center gap-1 rounded-full border border-emerald-400/40 px-3 py-1 text-xs text-emerald-200 transition hover:border-emerald-300 hover:text-white"
                            >
                                <i class="fas fa-pen"></i>
                                تعديل الاسم
                            </button>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-700/60 text-slate-200">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">هل تحتاج تذكيراً؟</p>
                        @auth
                            <p class="text-xs text-slate-400">ستجد رابط الورشة دائماً داخل صفحة حجوزاتك في وصفة.</p>
                        @else
                            <p class="text-xs text-slate-400">
                                احفظ هذا الرابط في ملاحظاتك للانضمام بسرعة عند بدء الورشة.
                            </p>
                        @endauth
                    </div>
                </div>
            </div>
            @auth
                <a
                    href="{{ route('bookings.show', $booking) }}"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-slate-200 transition hover:border-slate-500 hover:text-white"
                >
                    <i class="fas fa-arrow-right"></i>
                    العودة إلى تفاصيل الحجز
                </a>
            @else
                <span class="text-xs text-slate-400">
                    احتفظ بهذا الرابط لديك للعودة إلى الغرفة متى ما احتجت.
                </span>
            @endauth
        </div>
    </div>
</div>

@if ($shouldPromptForDisplayName)
    <div
        id="displayNameModal"
        class="fixed inset-0 z-40 hidden flex items-center justify-center bg-slate-950/70 px-4 backdrop-blur"
    >
        <div class="w-full max-w-md rounded-3xl bg-white/95 p-6 text-slate-800 shadow-2xl">
            <h3 class="text-lg font-semibold text-slate-900">اختر اسمك للغرفة</h3>
            <p class="mt-2 text-sm text-slate-600">
                سيظهر هذا الاسم للمشاركين الآخرين داخل الورشة. يمكنك تغييره لاحقاً في أي وقت.
            </p>
            <form id="displayNameForm" class="mt-5 space-y-4">
                <div>
                    <label
                        for="displayNameInput"
                        class="block text-xs font-semibold uppercase tracking-[0.35em] text-slate-500"
                    >
                        اسم العرض
                    </label>
                    <input
                        id="displayNameInput"
                        type="text"
                        name="display_name"
                        required
                        maxlength="60"
                        class="mt-3 w-full rounded-2xl border border-slate-300 bg-white/95 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        placeholder="مثال: ضيف وصفة"
                        autocomplete="off"
                    >
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                >
                    ابدأ الانضمام
                </button>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ $embedConfig['external_api_url'] }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startsAtIso = @json($startsAtIso);
        const countdownCard = document.getElementById('countdownCard');
        const countdownLabel = document.getElementById('countdownLabel');
        const countdownBadge = document.getElementById('countdownBadge');
        const shouldPromptForDisplayName = @json($shouldPromptForDisplayName);
        const fallbackGuestName = @json($guestDisplayName);
        const participantEmail = @json($participantEmail);
        const participantNameLabel = document.getElementById('participantNameLabel');
        const editDisplayNameBtn = document.getElementById('editDisplayNameBtn');
        const displayNameModal = document.getElementById('displayNameModal');
        const displayNameForm = document.getElementById('displayNameForm');
        const displayNameInput = document.getElementById('displayNameInput');
        let participantName = @json($participantName);
        let pendingDisplayNameResolve = null;
        let apiInstance = null;

        const updateParticipantNameLabel = (name) => {
            if (participantNameLabel) {
                participantNameLabel.textContent = name;
            }
        };

        const closeDisplayNameModal = () => {
            displayNameModal?.classList.add('hidden');
        };

        const openDisplayNameModal = () => {
            if (!displayNameModal) {
                return;
            }

            displayNameModal.classList.remove('hidden');

            if (displayNameInput) {
                const initialValue = participantName && participantName !== fallbackGuestName ? participantName : '';
                displayNameInput.value = initialValue;
                setTimeout(() => displayNameInput.focus(), 50);
            }
        };

        const resolveDisplayName = (name) => {
            participantName = name;
            updateParticipantNameLabel(participantName);

            if (apiInstance) {
                apiInstance.executeCommand('displayName', participantName);
            }

            if (pendingDisplayNameResolve) {
                pendingDisplayNameResolve(participantName);
                pendingDisplayNameResolve = null;
            }
        };

        displayNameForm?.addEventListener('submit', (event) => {
            event.preventDefault();

            const value = displayNameInput?.value?.trim() ?? '';
            if (!value) {
                displayNameInput?.focus();
                return;
            }

            closeDisplayNameModal();
            resolveDisplayName(value);
        });

        editDisplayNameBtn?.addEventListener('click', (event) => {
            event.preventDefault();
            pendingDisplayNameResolve = null;
            openDisplayNameModal();
        });

        const promptForDisplayName = () => {
            if (!shouldPromptForDisplayName) {
                return Promise.resolve(participantName);
            }

            if (participantName && participantName !== fallbackGuestName) {
                return Promise.resolve(participantName);
            }

            return new Promise((resolve) => {
                pendingDisplayNameResolve = resolve;

                if (displayNameModal?.classList.contains('hidden')) {
                    openDisplayNameModal();
                } else if (displayNameInput) {
                    setTimeout(() => displayNameInput.focus(), 50);
                }
            });
        };

        updateParticipantNameLabel(participantName || fallbackGuestName);

        if (shouldPromptForDisplayName && (!participantName || participantName === fallbackGuestName)) {
            openDisplayNameModal();
        }

        const setupCountdown = (targetIso, labelElement, badgeElement, options = {}) => {
            if (!targetIso || !labelElement || !badgeElement) {
                return;
            }

            const targetDate = new Date(targetIso);
            if (Number.isNaN(targetDate.getTime())) {
                return;
            }

            const { futureBadge = 'جاهزون تقريباً', lateBadge = 'تخطينا الموعد', soonBadge = 'اقترب الوقت' } = options;
            const rtf = new Intl.RelativeTimeFormat('ar', { numeric: 'auto' });

            const setBadgeState = (state) => {
                badgeElement.classList.remove(
                    'bg-emerald-500/20', 'text-emerald-200',
                    'bg-amber-500/20', 'text-amber-200',
                    'bg-rose-500/20', 'text-rose-100'
                );

                if (state === 'upcoming') {
                    badgeElement.classList.add('bg-emerald-500/20', 'text-emerald-200');
                } else if (state === 'soon') {
                    badgeElement.classList.add('bg-amber-500/20', 'text-amber-200');
                } else {
                    badgeElement.classList.add('bg-rose-500/20', 'text-rose-100');
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
                const diffMs = targetDate.getTime() - now.getTime();
                const diffSeconds = diffMs / 1000;

                if (Math.abs(diffSeconds) < 45) {
                    labelElement.textContent = diffMs >= 0
                        ? 'اقترب الوقت! استعد للانضمام فور فتح الغرفة.'
                        : 'تجاوزنا الموعد الأساسي. يرجى الاستمرار في المتابعة، فسيتم فتح الغرفة قريباً.';
                    badgeElement.textContent = diffMs >= 0 ? soonBadge : lateBadge;
                    setBadgeState(diffMs >= 0 ? 'soon' : 'late');
                    return;
                }

                const { value, unit } = getRelativeParts(diffSeconds);
                const relative = rtf.format(value, unit);

                if (diffSeconds > 0) {
                    labelElement.textContent = `تبدأ ${relative}`;
                    badgeElement.textContent = diffSeconds < 3600 ? soonBadge : futureBadge;
                    setBadgeState(diffSeconds < 3600 ? 'soon' : 'upcoming');
                } else {
                    labelElement.textContent = `بدأت ${relative}`;
                    badgeElement.textContent = lateBadge;
                    setBadgeState('late');
                }
            };

            updateCountdown();
            setInterval(updateCountdown, 60000);
        };

        setupCountdown(startsAtIso, countdownLabel, countdownBadge);

        const waitingCountdownLabel = document.getElementById('waitingCountdownLabel');
        const waitingCountdownBadge = document.getElementById('waitingCountdownBadge');
        setupCountdown(startsAtIso, waitingCountdownLabel, waitingCountdownBadge, {
            futureBadge: 'سيبدأ قريباً',
            soonBadge: 'اقترب الوقت',
            lateBadge: 'بانتظار الشيف',
        });

        @if ($workshop->meeting_started_at)
            const initializeMeeting = async () => {
                const container = document.getElementById('jitsi-container');

                if (typeof JitsiMeetExternalAPI === 'undefined' || !container) {
                    alert('تعذر تحميل غرفة الاجتماع. يرجى إعادة تحديث الصفحة أو التحقق من الاتصال.');
                    return;
                }

                participantName = (await promptForDisplayName()) || fallbackGuestName;
                updateParticipantNameLabel(participantName);

                const domain = @json($embedConfig['domain']);
                const initialHeight = container.offsetHeight || 640;

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
                        disableReactions: true,
                        disableInviteFunctions: true,
                        disableSelfViewSettings: true,
                        toolbarButtons: [
                            'microphone',
                            'camera',
                            'tileview',
                            'fullscreen',
                            'hangup',
                        ],
                    },
                    interfaceConfigOverwrite: {
                        SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                        LANG_DETECTION: false,
                        DEFAULT_REMOTE_DISPLAY_NAME: 'مشارك',
                        DEFAULT_LOCAL_DISPLAY_NAME: participantName || 'أنا',
                        FILM_STRIP_MAX_HEIGHT: 120,
                        SETTINGS_SECTIONS: [],
                        TOOLBAR_BUTTONS: [
                            'microphone',
                            'camera',
                            'tileview',
                            'fullscreen',
                            'hangup',
                        ],
                    },
                };

                const userInfo = {};
                if (participantName) {
                    userInfo.displayName = participantName;
                }
                if (participantEmail) {
                    userInfo.email = participantEmail;
                }
                if (Object.keys(userInfo).length > 0) {
                    options.userInfo = userInfo;
                }

                apiInstance = new JitsiMeetExternalAPI(domain, options);

                const resizeJitsi = () => {
                    const width = container.offsetWidth;
                    const height = container.offsetHeight || initialHeight;
                    apiInstance?.resize(width, height);
                };

                window.addEventListener('resize', resizeJitsi);
                resizeJitsi();
            };

            initializeMeeting();
        @else
            const statusUrl = @json(route('bookings.status', ['booking' => $booking->public_code]));
            const hint = document.getElementById('pollStatusHint');
            const refreshButton = document.getElementById('manualRefreshButton');
            let nextPollTimeout = null;

            const schedulePoll = (delay = 8000) => {
                nextPollTimeout = setTimeout(() => pollStatus(), delay);
            };

            const setHint = (message) => {
                if (hint) {
                    hint.textContent = message;
                }
            };

            const pollStatus = (manual = false) => {
                if (manual) {
                    setHint('جارٍ التحقق من حالة الغرفة...');
                }

                fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('status-check-failed');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.meeting_started) {
                            setHint('يتم فتح الغرفة الآن...');
                            window.location.reload();
                        } else {
                            setHint('لم يبدأ البث بعد. سنحاول مجدداً خلال لحظات.');
                            schedulePoll(8000);
                        }
                    })
                    .catch(() => {
                        setHint('تعذر التحقق مؤقتاً، ستتم إعادة المحاولة تلقائياً.');
                        schedulePoll(10000);
                    });
            };

            refreshButton?.addEventListener('click', () => {
                if (nextPollTimeout) {
                    clearTimeout(nextPollTimeout);
                }
                pollStatus(true);
            });

            schedulePoll(5000);
        @endif
    });
</script>
@endpush
