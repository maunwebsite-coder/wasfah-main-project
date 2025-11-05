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
        box-shadow: 0 25px 50px -12px rgba(249, 115, 22, 0.25);
    }

    @media (max-width: 640px) {
        .jitsi-wrapper {
            min-height: 65vh;
            border-radius: 1rem;
        }
    }

    .session-lock-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 2.5rem 1.5rem;
        background: linear-gradient(135deg, rgba(255, 247, 237, 0.94), rgba(254, 215, 170, 0.94));
        z-index: 40;
        text-align: center;
        backdrop-filter: blur(14px);
        color: #7c2d12;
    }

    .session-lock-overlay .lock-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 9999px;
        background: rgba(234, 88, 12, 0.12);
        color: #ea580c;
        font-size: 1.75rem;
        box-shadow: 0 20px 45px -15px rgba(234, 88, 12, 0.45);
    }

    .session-lock-overlay h2 {
        font-size: 1.35rem;
        font-weight: 700;
    }

    .session-lock-overlay p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(120, 53, 15, 0.85);
        max-width: 28rem;
    }

    .session-lock-overlay .lock-since {
        font-size: 0.75rem;
        color: rgba(124, 45, 18, 0.7);
    }

    .mobile-meeting-toolbar {
        display: none;
        margin-top: 0.75rem;
        justify-content: center;
        gap: 0.75rem;
    }

    .mobile-meeting-toolbar button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(15, 23, 42, 0.92);
        color: #f1f5f9;
        border-radius: 9999px;
        padding: 0.6rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid rgba(15, 23, 42, 0.2);
        box-shadow: 0 8px 20px -10px rgba(30, 41, 59, 0.6);
    }

    .mobile-meeting-toolbar button.is-active {
        background: rgba(234, 88, 12, 0.95);
        border-color: rgba(234, 88, 12, 0.65);
    }

    .mobile-meeting-toolbar button:focus-visible {
        outline: 2px solid rgba(234, 88, 12, 0.7);
        outline-offset: 3px;
    }

    @media (max-width: 768px) {
        .mobile-meeting-toolbar[aria-hidden="false"] {
            display: flex;
        }
    }

    @media (min-width: 769px) {
        .mobile-meeting-toolbar {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 py-10 text-slate-800">
    <div class="mx-auto max-w-7xl px-4">
        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-orange-200 bg-orange-500/10 text-orange-700' : 'border-rose-200 bg-rose-50 text-rose-700' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="mb-8 grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,0.9fr)]">
            <div class="space-y-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-orange-500">جلسة مباشرة</p>
                    <h1 class="mt-2 text-3xl font-bold sm:text-4xl">{{ $workshop->title }}</h1>
                </div>
                <p class="text-sm leading-relaxed text-slate-600">
                    تأكد من اتصالك بالإنترنت، ثم اسمح للمتصفح بالوصول إلى الميكروفون والكاميرا عند فتح الغرفة. ستظهر لك عناصر التحكم داخل البث عند الضغط على زر الانضمام.
                </p>
                <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-600">
                    @if ($hostName)
                        <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 text-slate-700 shadow-sm">
                            <i class="fas fa-chalkboard-teacher text-orange-500"></i>
                            مع المضيف: {{ $hostName }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 text-slate-700 shadow-sm">
                        <i class="fas fa-clock text-orange-500"></i>
                        المدة: {{ $workshop->duration }} دقيقة تقريباً
                    </span>
                    @if ($workshop->confirmed_bookings_count)
                        <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 text-slate-700 shadow-sm">
                            <i class="fas fa-users text-orange-500"></i>
                            {{ number_format($workshop->confirmed_bookings_count) }} مشارك مؤكد
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-1 text-slate-700 shadow-sm">
                        <i class="fas fa-shield-alt text-orange-500"></i>
                        دخول آمن عبر وصفة
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-orange-200 bg-white p-5 shadow-xl" id="countdownCard" @if ($startsAtIso) data-starts-at="{{ $startsAtIso }}" @endif>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-orange-500">موعد الورشة</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">
                            {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') ?: 'سيتم تحديده من قبل الشيف' }}
                        </p>
                    </div>
                    <span id="countdownBadge" class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 transition-colors">
                        {{ $startsAtIso ? 'جاهزون تقريباً' : 'موعد مرن' }}
                    </span>
                </div>
                <p id="countdownLabel" class="mt-4 text-sm leading-relaxed text-slate-600">
                    {{ $startsAtIso ? 'يتم تحديث الوقت المتبقي تلقائياً.' : 'سيقوم الشيف بفتح الغرفة عندما يحين الوقت، ترقّب إشعار الانضمام.' }}
                </p>
                @if ($workshop->meeting_started_at)
                    <p class="mt-4 flex items-center gap-2 rounded-2xl border border-orange-200/70 bg-orange-50 px-4 py-3 text-xs text-orange-700">
                        <i class="fas fa-broadcast-tower text-orange-500"></i>
                        تم فتح الغرفة قبل {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}. انقر زر الانضمام داخل البث للمشاركة مباشرة.
                    </p>
                @endif
            </div>
        </div>

        @if ($workshop->meeting_started_at)
            <div class="mb-6 rounded-3xl border border-orange-200 bg-orange-500/10 px-6 py-4 text-sm text-orange-700 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-orange-600">
                        <i class="fas fa-check-circle text-orange-500"></i>
                        الغرفة مفتوحة الآن – يمكنك الانضمام متى ما شئت.
                    </span>
                    <span class="text-xs text-orange-600/80">
                        إذا انقطع الاتصال، فقط أعد تحديث الصفحة وسيستمر البث تلقائياً.
                    </span>
                </div>
            </div>

            <div class="jitsi-shell" id="jitsi-shell">
                <div class="jitsi-wrapper bg-black relative" id="jitsi-container">
                    <livewire:bookings.meeting-lock-overlay
                        :booking-code="$booking->public_code"
                        :workshop-id="$workshop->id"
                        :initial-started-at="$meetingStartedAtIso"
                        :initial-locked-at="$meetingLockedAtIso"
                        :initial-locked="$isMeetingLocked"
                    />
                </div>
                @if ($workshop->meeting_started_at)
                    <div class="mobile-meeting-toolbar" id="mobileMeetingToolbar" hidden aria-hidden="true">
                        <button
                            type="button"
                            id="mobileFullscreenToggle"
                            class="mobile-meeting-toolbar__btn"
                            aria-pressed="false"
                        >
                            <i class="fas fa-expand" aria-hidden="true"></i>
                            <span id="mobileFullscreenToggleLabel">شاشة كاملة</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="mt-6 rounded-3xl border border-orange-100 bg-orange-50 px-5 py-4 text-xs text-orange-700 shadow">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-orange-700">
                    <i class="fas fa-info-circle text-orange-500"></i>
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
                <div id="waitingCard" class="rounded-3xl border border-orange-200 bg-white px-6 py-10 text-center text-slate-700 shadow-xl">
                    <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 text-orange-500">
                        <i class="fas fa-door-closed text-2xl"></i>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-slate-900">ننتظر دخول الشيف</h2>
                    <p class="text-sm text-slate-500">
                        نغلق الغرفة إلى أن يؤكد الشيف بدء الورشة للحفاظ على خصوصية البث. أبقِ هذه الصفحة مفتوحة وسنحدثها بمجرد فتح الغرفة لك.
                    </p>
                    <div class="mt-6 flex flex-col items-center gap-4">
                        <div class="flex flex-col items-center gap-2 text-sm">
                            <span id="waitingCountdownBadge" class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 transition-colors">
                                {{ $startsAtIso ? 'سيبدأ قريباً' : 'بانتظار إشارة الشيف' }}
                            </span>
                            <span id="waitingCountdownLabel" class="text-xs text-slate-500">
                                {{ $startsAtIso ? 'يتم احتساب الوقت المتبقي.' : 'سنرسل تحديثاً فور فتح الغرفة.' }}
                            </span>
                        </div>
                        <button
                            type="button"
                            id="manualRefreshButton"
                            class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-600 transition hover:border-orange-300 hover:bg-orange-50"
                        >
                            <i class="fas fa-sync"></i>
                            تحديث الحالة الآن
                        </button>
                        <div class="text-xs text-slate-500" id="pollStatusHint">
                            يتم التحقق من حالة الغرفة كل بضع ثوانٍ...
                        </div>
                    </div>
                </div>

                <div class="space-y-4 text-xs text-slate-600">
                    <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-orange-600">
                            <i class="fas fa-check-square text-orange-500"></i>
                            تأكد من جهوزيتك الآن
                        </h3>
                        <ul class="mt-3 space-y-2 leading-relaxed">
                            <li>• أغلق التطبيقات التي تستهلك الإنترنت (تحميلات، بث فيديو، ...).</li>
                            <li>• جرّب سماعات أو ميكروفوناً خارجياً إذا توفّر لتحصل على صوت أوضح.</li>
                            <li>• افتح الإضاءة أمامك إذا كنت ستشارك الكاميرا لتظهر بوضوح.</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-orange-100 bg-white p-5 shadow">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-orange-600">
                            <i class="fas fa-life-ring text-orange-500"></i>
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

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-600">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-user"></i>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500">اسمك في الغرفة</p>
                        <p class="font-semibold text-slate-900" id="participantNameLabel">{{ $participantName }}</p>
                        @if ($shouldPromptForDisplayName)
                            <button
                                type="button"
                                id="editDisplayNameBtn"
                                class="mt-1 inline-flex items-center gap-1 rounded-full border border-orange-200 px-3 py-1 text-xs text-orange-600 transition hover:border-orange-300 hover:bg-orange-50"
                            >
                                <i class="fas fa-pen"></i>
                                تعديل الاسم
                            </button>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-orange-50 text-orange-500">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500">هل تحتاج تذكيراً؟</p>
                        @auth
                            <p class="text-xs text-slate-500">ستجد رابط الورشة دائماً داخل صفحة حجوزاتك في وصفة.</p>
                        @else
                            <p class="text-xs text-slate-500">
                                احفظ هذا الرابط في ملاحظاتك للانضمام بسرعة عند بدء الورشة.
                            </p>
                        @endauth
                    </div>
                </div>
            </div>
            @auth
                <a
                    href="{{ route('bookings.show', $booking) }}"
                    class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-orange-600 transition hover:border-orange-300 hover:bg-orange-50"
                >
                    <i class="fas fa-arrow-right"></i>
                    العودة إلى تفاصيل الحجز
                </a>
            @else
                <span class="text-xs text-slate-500">
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
                        class="mt-3 w-full rounded-2xl border border-slate-300 bg-white/95 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200"
                        placeholder="مثال: ضيف وصفة"
                        autocomplete="off"
                    >
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300"
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
        const statusUrl = @json(route('bookings.status', ['booking' => $booking->public_code]));
        const meetingStartedAtIso = @json($meetingStartedAtIso);
        const hint = document.getElementById('pollStatusHint');
        const refreshButton = document.getElementById('manualRefreshButton');
        let nextPollTimeout = null;
        const meetingStarted = Boolean(meetingStartedAtIso);

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
                    'bg-orange-100', 'text-orange-700',
                    'bg-amber-100', 'text-amber-700',
                    'bg-rose-100', 'text-rose-600'
                );

                if (state === 'upcoming') {
                    badgeElement.classList.add('bg-orange-100', 'text-orange-700');
                } else if (state === 'soon') {
                    badgeElement.classList.add('bg-amber-100', 'text-amber-700');
                } else {
                    badgeElement.classList.add('bg-rose-100', 'text-rose-600');
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

        const clearScheduledPoll = () => {
            if (nextPollTimeout) {
                clearTimeout(nextPollTimeout);
                nextPollTimeout = null;
            }
        };

        const schedulePoll = (delay = 8000) => {
            clearScheduledPoll();

            if (!statusUrl) {
                return;
            }

            nextPollTimeout = setTimeout(() => pollStatus(), delay);
        };

        const setHint = (message) => {
            if (hint) {
                hint.textContent = message;
            }
        };

        const pollStatus = (manual = false) => {
            if (!statusUrl) {
                return;
            }

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
                    const remoteStarted = Boolean(data.meeting_started);

                    if (!remoteStarted) {
                        if (meetingStarted) {
                            window.location.reload();
                            return;
                        }

                        setHint('لم يبدأ البث بعد. سنحاول مجدداً خلال لحظات.');
                        schedulePoll(8000);
                        return;
                    }

                    window.location.reload();
                })
                .catch(() => {
                    if (hint) {
                        setHint('تعذر التحقق مؤقتاً، ستتم إعادة المحاولة تلقائياً.');
                    }
                    schedulePoll(12000);
                });
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
                const confirmationMessage = 'هل تريد الدخول إلى الاجتماع؟ ستكون في الجلسة مباشرة عند الضغط على نعم.';
                if (!window.confirm(confirmationMessage)) {
                    alert('لن يتم الانضمام للاجتماع الآن. يمكنك إعادة المحاولة من ملفك الشخصي متى شئت.');
                    return;
                }

                const container = document.getElementById('jitsi-container');
                const mobileToolbar = document.getElementById('mobileMeetingToolbar');
                const mobileFullscreenToggle = document.getElementById('mobileFullscreenToggle');
                const mobileFullscreenLabel = document.getElementById('mobileFullscreenToggleLabel');

                if (typeof JitsiMeetExternalAPI === 'undefined' || !container) {
                    alert('تعذر تحميل غرفة الاجتماع. يرجى إعادة تحديث الصفحة أو التحقق من الاتصال.');
                    return;
                }

                participantName = (await promptForDisplayName()) || fallbackGuestName;
                updateParticipantNameLabel(participantName);

                const domain = @json($embedConfig['domain']);
                const jaasJwt = @json($embedConfig['jwt'] ?? null);
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
                        startWithAudioMuted: true,
                        startWithVideoMuted: true,
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

                if (jaasJwt) {
                    options.jwt = jaasJwt;
                }

                apiInstance = new JitsiMeetExternalAPI(domain, options);
                setupMobileFullscreenControl(apiInstance, mobileToolbar, mobileFullscreenToggle, mobileFullscreenLabel);

                const resizeJitsi = () => {
                    const width = container.offsetWidth;
                    const height = container.offsetHeight || initialHeight;
                    apiInstance?.resize(width, height);
                };

                window.addEventListener('resize', resizeJitsi);
                resizeJitsi();
            };

            initializeMeeting();
        @endif

        refreshButton?.addEventListener('click', () => {
            clearScheduledPoll();
            pollStatus(true);
        });

        if (!meetingStarted && statusUrl) {
            schedulePoll(5000);
        }

        function setupMobileFullscreenControl(api, toolbar, toggleButton, toggleLabel) {
            if (!toolbar || !toggleButton) {
                return;
            }

            const mobileQuery = window.matchMedia('(max-width: 768px)');

            const updateVisibility = () => {
                const isMobile = mobileQuery.matches;
                if (isMobile) {
                    toolbar.removeAttribute('hidden');
                    toolbar.setAttribute('aria-hidden', 'false');
                } else {
                    toolbar.setAttribute('hidden', '');
                    toolbar.setAttribute('aria-hidden', 'true');
                }
            };

            const getFullscreenElement = () => (
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            );

            const updateFullscreenState = () => {
                const isFullscreen = Boolean(getFullscreenElement());
                toggleButton.classList.toggle('is-active', isFullscreen);
                toggleButton.setAttribute('aria-pressed', isFullscreen ? 'true' : 'false');
                if (toggleLabel) {
                    toggleLabel.textContent = isFullscreen ? 'إغلاق الشاشة الكاملة' : 'شاشة كاملة';
                }
            };

            toggleButton.addEventListener('click', () => {
                api.executeCommand('toggleFullScreen');
            });

            if (typeof api.addListener === 'function') {
                api.addListener('videoConferenceJoined', updateFullscreenState);
                api.addListener('videoConferenceLeft', updateFullscreenState);
            }

            ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(eventName => {
                document.addEventListener(eventName, updateFullscreenState);
            });

            updateVisibility();
            if (typeof mobileQuery.addEventListener === 'function') {
                mobileQuery.addEventListener('change', updateVisibility);
            } else if (typeof mobileQuery.addListener === 'function') {
                mobileQuery.addListener(updateVisibility);
            }

            updateFullscreenState();
        }
    });
</script>
@endpush
