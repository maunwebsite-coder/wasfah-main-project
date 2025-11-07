@extends('layouts.app')

@section('title', 'تشغيل ورشة: ' . $workshop->title)

@push('styles')
<style>
    :root {
        --jitsi-viewport-height: 100vh;
        --jitsi-viewport-width: 100vw;
    }

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

    body.mobile-fullscreen-active {
        overflow: hidden;
    }

    .mobile-fullscreen-target.mobile-fullscreen-active {
        position: fixed !important;
        inset: 0 !important;
        width: var(--jitsi-viewport-width, 100vw) !important;
        height: var(--jitsi-viewport-height, 100vh) !important;
        border-radius: 0 !important;
        z-index: 999 !important;
        background-color: #000 !important;
    }

    .jitsi-safe-area {
        position: relative;
        width: 100%;
        height: 100%;
        padding-top: env(safe-area-inset-top);
        padding-bottom: env(safe-area-inset-bottom);
        padding-left: env(safe-area-inset-left);
        padding-right: env(safe-area-inset-right);
        box-sizing: border-box;
        background: inherit;
    }

    #jitsi-container {
        width: 100%;
        height: 100%;
    }

    footer {
        display: none !important;
    }

    @media (max-width: 768px) {
        header.sticky.top-0 {
            display: none !important;
        }
    }

</style>
@endpush

@section('content')
@php
    $currentUser = auth()->user();
    $canViewRawMeetingLink = $currentUser && method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin();
@endphp
<div class="min-h-screen bg-gradient-to-b from-amber-50 via-orange-50 to-white py-10 text-slate-900">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-green-200 bg-green-50 text-green-800' : 'border-rose-200 bg-rose-50 text-rose-700' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        @php
            $isMeetingLive = (bool) $workshop->meeting_started_at;
            $meetingLocked = (bool) $workshop->meeting_locked_at;
            $hostStatusTitle = $isMeetingLive
                ? ($meetingLocked ? 'البث مباشر (الانضمام مقفل)' : 'البث مباشر الآن')
                : ($workshop->is_online ? 'استعد لبدء الجلسة' : 'ورشة حضورية');
            $hostStatusDescription = $isMeetingLive
                ? ($meetingLocked
                    ? 'يمكنك المتابعة داخل الغرفة بينما يبقى الانضمام مغلقاً للمشاركين الجدد.'
                    : 'يمكنك إدارة الغرفة والتفاعل مع المشاركين في الوقت الحقيقي.')
                : ($workshop->is_online
                    ? 'ابدأ الاجتماع عندما تكون جاهزاً ليتم إخطار المشاركين وتفعيل رابطهم.'
                    : 'تابع تجهيز الموقع واستقبال المشاركين في الموعد المحدد.');
            $participantStatusTitle = $workshop->is_online
                ? ($isMeetingLive
                    ? ($meetingLocked ? 'البث نشط لكن مغلق' : 'يمكن للمشاركين الانضمام الآن')
                    : 'ينتظر المشاركون بدء الجلسة')
                : 'المشاركون سينضمون حضوريًا';
            $participantStatusDescription = $workshop->is_online
                ? ($isMeetingLive
                    ? ($meetingLocked
                        ? 'الرابط متاح لكن الانضمام الجديد متوقف حتى تعيد فتح الاجتماع من لوحة التحكم.'
                        : 'شارك الرابط أدناه مع جمهورك أو أعد إرساله لهم عند الحاجة.')
                    : 'سيتم تفعيل رابط الدخول تلقائياً فور بدء البث.')
                : ($workshop->location
                    ? 'ذكر المشاركين بموقع الورشة والوصول في الوقت المحدد.'
                    : 'أضف تفاصيل الموقع من صفحة تعديل الورشة لتسهيل وصول المشاركين.');
        @endphp



        <div class="jitsi-shell mb-10" id="jitsi-shell">
            <div class="jitsi-wrapper bg-white relative mobile-fullscreen-target">
                <div class="jitsi-safe-area">
                    <div class="jitsi-node" id="jitsi-container"></div>
                </div>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-white px-6 py-5 text-slate-900 shadow-lg shadow-orange-100/60">
                <livewire:chef.workshop-meeting-control
                    :workshop="$workshop"
                    :key="'chef-workshop-meeting-control-' . $workshop->id"
                />
            </div>
        </div>

        <div
            id="joinCancellationNotice"
            class="mt-6 hidden rounded-3xl border border-amber-200 bg-amber-50 px-6 py-4 text-sm text-amber-700 shadow-lg"
            role="alert"
            aria-live="polite"
            tabindex="-1"
        >
            تم إيقاف الانضمام للاجتماع. يمكنك إعادة المحاولة لاحقاً من لوحة الشيف.
        </div>

        <section class="mt-10 grid gap-6 lg:grid-cols-[minmax(0,2.15fr)_minmax(0,0.95fr)]">
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
        </section>

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

    <div
        id="joinConfirmationModal"
        class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-900/60 p-4 opacity-0 transition-opacity duration-200"
        role="dialog"
        aria-modal="true"
        aria-labelledby="joinConfirmationTitle"
        aria-hidden="true"
    >
        <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl">
            <button
                type="button"
                class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-300"
                data-action="close"
            >
                <span class="sr-only">إغلاق</span>
                <i class="fas fa-times text-lg"></i>
            </button>
            <div class="flex flex-col items-center gap-4 px-8 pt-10 pb-8 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 text-3xl text-orange-500">
                    <i class="fas fa-video"></i>
                </span>
                <div class="space-y-3">
                    <h2 id="joinConfirmationTitle" class="text-2xl font-semibold text-slate-900">جاهز للدخول إلى الجلسة؟</h2>
                    <p class="text-sm leading-relaxed text-slate-600">
                        سيتم تشغيل غرفة الاجتماع فوراً بعد المتابعة. تأكد من أن الصوت والكاميرا جاهزان قبل الاستمرار.
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-2 rounded-b-3xl border-t border-slate-100 bg-slate-50 px-8 py-6 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-400 hover:text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400 sm:w-auto"
                    data-action="cancel"
                >
                    <i class="fas fa-clock"></i>
                    ليس الآن
                </button>
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-orange-500 to-amber-400 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:from-orange-600 hover:to-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400 sm:w-auto"
                    data-action="confirm"
                >
                    <i class="fas fa-unlock"></i>
                    افتح الاجتماع للمشاركين
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ $embedConfig['external_api_url'] }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const joinModal = document.getElementById('joinConfirmationModal');
        const joinCancellationNotice = document.getElementById('joinCancellationNotice');
        const cancellationMessage = 'تم إيقاف الانضمام للاجتماع. يمكنك إعادة المحاولة لاحقاً من لوحة الشيف.';
        const countdownCard = document.getElementById('countdownCard');
        const countdownLabel = document.getElementById('countdownLabel');
        const countdownBadge = document.getElementById('countdownBadge');
        const startsAtIso = countdownCard?.dataset.startsAt || null;
        const presenceUrl = @json(route('chef.workshops.presence', $workshop));
        const startUrl = @json(route('chef.workshops.start', $workshop));
        const csrfToken = @json(csrf_token());
        const joinPageUrl = @json(route('chef.workshops.join', $workshop));
        const copyButtons = document.querySelectorAll('[data-copy-link]');

        copyButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const link = button.getAttribute('data-copy-link');
                const feedbackTarget = button.getAttribute('data-copy-feedback-target');
                const feedbackElement = feedbackTarget ? document.getElementById(feedbackTarget) : null;

                if (!link) {
                    return;
                }

                const showFeedback = (message, variant = 'success') => {
                    if (!feedbackElement) {
                        if (variant === 'error') {
                            alert(message);
                        }
                        return;
                    }

                    feedbackElement.textContent = message;
                    feedbackElement.classList.remove('hidden', 'text-emerald-600', 'text-amber-600');
                    feedbackElement.classList.add('inline-flex', variant === 'success' ? 'text-emerald-600' : 'text-amber-600');

                    setTimeout(() => {
                        feedbackElement.classList.add('hidden');
                        feedbackElement.classList.remove('inline-flex');
                    }, 2600);
                };

                try {
                    if (!navigator.clipboard?.writeText) {
                        throw new Error('Clipboard API غير مدعوم');
                    }

                    await navigator.clipboard.writeText(link);
                    button.classList.add('ring-2', 'ring-emerald-400', 'ring-offset-2');
                    showFeedback('تم نسخ رابط الدخول للمشاركين.', 'success');

                    setTimeout(() => {
                        button.classList.remove('ring-2', 'ring-emerald-400', 'ring-offset-2');
                    }, 1200);
                } catch (error) {
                    showFeedback('تعذّر النسخ تلقائياً، انسخ الرابط يدوياً.', 'error');
                }
            });
        });

        let lastPresenceState = null;
        let meetingStartTriggered = false;
        const enableMobileAutoFullscreen = false; // mobile screens should stay inline per product request
        const mobileViewportQuery = window.matchMedia('(max-width: 768px)');
        let shouldAutoFullscreen = enableMobileAutoFullscreen && mobileViewportQuery.matches;
        let hasRedirectedToJoinPage = false;
        let apiInstance = null;
        let onViewportChange = null;
        let jitsiContainer = null;
        let jitsiInitialHeight = 640;

        const viewportSizing = (() => {
            const root = document.documentElement;
            const update = () => {
                const viewport = window.visualViewport;
                const height = viewport ? viewport.height : window.innerHeight;
                const width = viewport ? viewport.width : window.innerWidth;
                root.style.setProperty('--jitsi-viewport-height', `${height}px`);
                root.style.setProperty('--jitsi-viewport-width', `${width}px`);
                if (typeof onViewportChange === 'function') {
                    try {
                        onViewportChange(width, height);
                    } catch (error) {
                        console.warn('Viewport sizing listener failed', error);
                    }
                }
            };
            const scheduleUpdate = (delay = 0) => {
                if (delay > 0) {
                    window.setTimeout(update, delay);
                    return;
                }
                if (typeof requestAnimationFrame === 'function') {
                    requestAnimationFrame(update);
                } else {
                    update();
                }
            };

            update();

            if (window.visualViewport) {
                window.visualViewport.addEventListener('resize', update);
                window.visualViewport.addEventListener('scroll', update);
            }

            window.addEventListener('orientationchange', () => scheduleUpdate(250));
            window.addEventListener('resize', () => scheduleUpdate(150));

            return { update };
        })();

        const bodyFullscreenClass = 'mobile-fullscreen-active';
        const targetFullscreenClass = 'mobile-fullscreen-active';
        const fullscreenController = (() => {
            let mode = 'none'; // none | css | native | pending-native

            const getContainer = () => document.querySelector('.mobile-fullscreen-target');

            const getIframe = () => {
                if (apiInstance?.getIFrame) {
                    try {
                        return apiInstance.getIFrame();
                    } catch {
                        // iframe not ready yet
                    }
                }
                return getContainer()?.querySelector('iframe') ?? null;
            };

            const addClasses = () => {
                document.body.classList.add(bodyFullscreenClass);
                getContainer()?.classList.add(targetFullscreenClass);
            };

            const removeClasses = () => {
                document.body.classList.remove(bodyFullscreenClass);
                getContainer()?.classList.remove(targetFullscreenClass);
            };

            const requestNative = () => {
                const element = getIframe() ?? getContainer();
                if (!element) {
                    return false;
                }

                const request =
                    element.requestFullscreen ||
                    element.webkitRequestFullscreen ||
                    element.mozRequestFullScreen ||
                    element.msRequestFullscreen;

                if (!request) {
                    return false;
                }

                try {
                    const result = request.call(element);
                    addClasses();
                    mode = 'pending-native';

                    if (result && typeof result.then === 'function') {
                        result.then(
                            () => {
                                mode = 'native';
                            },
                            () => {
                                mode = 'none';
                                applyFallback();
                            },
                        );
                    } else {
                        mode = 'native';
                    }

                    return true;
                } catch {
                    return false;
                }
            };

            const applyFallback = () => {
                addClasses();
                mode = 'css';
            };

            const ensure = (fromGesture = false) => {
                viewportSizing.update();
                if (!shouldAutoFullscreen) {
                    exit();
                    return;
                }

                if (mode === 'native' || mode === 'pending-native') {
                    addClasses();
                    return;
                }

                if (requestNative()) {
                    return;
                }

                if (fromGesture || mode !== 'css') {
                    applyFallback();
                }
            };

            const exitNative = () => {
                if (document.fullscreenElement) {
                    const exit =
                        document.exitFullscreen ||
                        document.webkitExitFullscreen ||
                        document.mozCancelFullScreen ||
                        document.msExitFullscreen;
                    if (exit) {
                        try {
                            exit.call(document);
                        } catch {
                            // ignore
                        }
                    }
                }
            };

            const exit = () => {
                if (mode === 'native' || mode === 'pending-native') {
                    exitNative();
                }
                removeClasses();
                mode = 'none';
            };

            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement && mode === 'native') {
                    if (shouldAutoFullscreen) {
                        applyFallback();
                    } else {
                        exit();
                    }
                }
            });

            return {
                ensureFromGesture: () => ensure(true),
                ensure: () => ensure(false),
                exit,
            };
        })();

        const handleViewportPreferenceChange = (matches) => {
            shouldAutoFullscreen = enableMobileAutoFullscreen && matches;
            if (shouldAutoFullscreen) {
                fullscreenController.ensure();
            } else {
                fullscreenController.exit();
            }
        };

        if (typeof mobileViewportQuery.addEventListener === 'function') {
            mobileViewportQuery.addEventListener('change', event => {
                handleViewportPreferenceChange(event.matches);
            });
        } else if (typeof mobileViewportQuery.addListener === 'function') {
            mobileViewportQuery.addListener(event => {
                handleViewportPreferenceChange(event.matches);
            });
        }

        window.addEventListener('beforeunload', fullscreenController.exit);
        window.addEventListener('pagehide', fullscreenController.exit);

        function resizeJitsi() {
            if (!jitsiContainer) {
                return;
            }

            const measuredWidth = jitsiContainer.offsetWidth || 0;
            const fallbackWidth = window.visualViewport?.width ?? window.innerWidth ?? 0;
            const width = measuredWidth || fallbackWidth || (typeof screen !== 'undefined' ? screen.width : 0);
            const height = jitsiContainer.offsetHeight || jitsiInitialHeight;

            if (apiInstance && typeof apiInstance.resize === 'function') {
                try {
                    apiInstance.resize(width, height);
                } catch (error) {
                    console.warn('Failed to resize Jitsi iframe', error);
                }
            }
        }

        onViewportChange = () => {
            resizeJitsi();
        };

        const redirectToJoinPage = () => {
            if (hasRedirectedToJoinPage || !joinPageUrl) {
                return;
            }

            hasRedirectedToJoinPage = true;
            fullscreenController.exit();
            window.location.assign(joinPageUrl);
        };

        const startMeetingForParticipants = () => {
            if (meetingStartTriggered) {
                return;
            }

            meetingStartTriggered = true;

            if (window.Livewire?.dispatch) {
                window.Livewire.dispatch('chef-start-meeting', { confirmHost: true });
            } else {
                document.addEventListener('livewire:load', () => {
                    if (window.Livewire?.dispatch) {
                        window.Livewire.dispatch('chef-start-meeting', { confirmHost: true });
                    }
                }, { once: true });
            }

            if (!startUrl || !csrfToken) {
                return;
            }

            const payload = new FormData();
            payload.append('_token', csrfToken);
            payload.append('confirm_host', '1');

            fetch(startUrl, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            }).catch(() => {
                // Non-blocking fallback; Livewire should handle opening the room.
            });
        };

        const requestJoinConfirmation = () => {
            if (joinCancellationNotice) {
                joinCancellationNotice.classList.add('hidden');
            }

            const fallbackConfirmation = 'هل تريد فتح الاجتماع للمشاركين الآن؟';

            if (!joinModal) {
                return Promise.resolve(window.confirm(fallbackConfirmation));
            }

            return new Promise((resolve) => {
                const previouslyFocused = (document.activeElement && typeof document.activeElement.focus === 'function')
                    ? document.activeElement
                    : null;
                const confirmButton = joinModal.querySelector('[data-action="confirm"]');
                const cancelButton = joinModal.querySelector('[data-action="cancel"]');
                const closeButton = joinModal.querySelector('[data-action="close"]');
                let resolved = false;

                const finish = (result) => {
                    if (resolved) {
                        return;
                    }
                    resolved = true;

                    joinModal.classList.remove('opacity-100');
                    joinModal.classList.add('opacity-0');
                    joinModal.setAttribute('aria-hidden', 'true');

                    joinModal.removeEventListener('click', handleBackdropClick);
                    window.removeEventListener('keydown', handleKeydown);
                    confirmButton?.removeEventListener('click', handleConfirm);
                    cancelButton?.removeEventListener('click', handleCancel);
                    closeButton?.removeEventListener('click', handleCancel);

                    setTimeout(() => {
                        joinModal.classList.add('hidden');
                        joinModal.classList.remove('flex');
                        previouslyFocused?.focus?.();
                    }, 220);

                    if (!result) {
                        fullscreenController.exit();
                    }

                    resolve(result);
                };

                const handleConfirm = () => {
                    fullscreenController.ensureFromGesture();
                    startMeetingForParticipants();
                    finish(true);
                };
                const handleCancel = () => {
                    fullscreenController.exit();
                    finish(false);
                };
                const handleBackdropClick = (event) => {
                    if (event.target === joinModal) {
                        finish(false);
                    }
                };
                const handleKeydown = (event) => {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        finish(false);
                    }
                };

                joinModal.classList.remove('hidden');
                joinModal.classList.add('flex');
                joinModal.setAttribute('aria-hidden', 'false');

                requestAnimationFrame(() => {
                    joinModal.classList.remove('opacity-0');
                    joinModal.classList.add('opacity-100');
                    (confirmButton || cancelButton || closeButton)?.focus();
                });

                confirmButton?.addEventListener('click', handleConfirm);
                cancelButton?.addEventListener('click', handleCancel);
                closeButton?.addEventListener('click', handleCancel);
                joinModal.addEventListener('click', handleBackdropClick);
                window.addEventListener('keydown', handleKeydown);
            });
        };

        const confirmed = await requestJoinConfirmation();
        if (!confirmed) {
            if (joinCancellationNotice) {
                joinCancellationNotice.classList.remove('hidden');
                joinCancellationNotice.focus?.();
            } else {
                alert(cancellationMessage);
            }
            return;
        }

        fullscreenController.ensure();

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

        jitsiContainer = document.getElementById('jitsi-container');

        if (typeof JitsiMeetExternalAPI === 'undefined' || !jitsiContainer) {
            alert('تعذر تحميل غرفة الاجتماع. يرجى إعادة تحديث الصفحة أو التحقق من الاتصال.');
            return;
        }

        const domain = @json($embedConfig['domain']);
        const jaasJwt = @json($embedConfig['jwt'] ?? null);
        jitsiInitialHeight = jitsiContainer.offsetHeight || jitsiInitialHeight || 640;
        const initialHeight = jitsiInitialHeight;

        const baseToolbarButtons = [
            'microphone',
            'camera',
            'raisehand',
            'fullscreen',
            'tileview',
            'subject',
            'settings',
            'chat',
            'desktop',
            'e2ee',
            'hangup',
        ];
        const mobileToolbarButtons = [
            'microphone',
            'camera',
            'raisehand',
            'fullscreen',
            'subject',
            'settings',
            'chat',
            'hangup',
        ];
        const isMobileToolbar = window.matchMedia('(max-width: 768px)').matches;
        const toolbarButtons = isMobileToolbar ? mobileToolbarButtons : baseToolbarButtons;

        const options = {
            roomName: @json($embedConfig['room']),
            parentNode: jitsiContainer,
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
                enableUserRolesBasedOnToken: @json(($embedConfig['provider'] ?? null) === 'jaas'),
                disableDeepLinking: true,
                startWithAudioMuted: true,
                startWithVideoMuted: true,
                disableInviteFunctions: true,
                disableSelfViewSettings: true,
                disableReactions: true,
                toolbarButtons,
            },
            interfaceConfigOverwrite: {
                SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                LANG_DETECTION: false,
                DEFAULT_REMOTE_DISPLAY_NAME: 'مشارك',
                DEFAULT_LOCAL_DISPLAY_NAME: 'أنا',
                FILM_STRIP_MAX_HEIGHT: 120,
                SETTINGS_SECTIONS: ['devices'],
                TOOLBAR_BUTTONS: toolbarButtons,
            },
            userInfo: {
                displayName: @json($user->name),
                email: @json($user->email),
            },
        };

        if (jaasJwt) {
            options.jwt = jaasJwt;
        }

        apiInstance = new JitsiMeetExternalAPI(domain, options);
        fullscreenController.ensure();
        viewportSizing.update();

        apiInstance.addListener('videoConferenceJoined', () => {
            sendPresence('online');
            fullscreenController.ensure();
            resizeJitsi();
        });

        apiInstance.addListener('videoConferenceLeft', () => {
            sendPresence('offline', { force: true });
            fullscreenController.exit();
            window.removeEventListener('resize', resizeJitsi);
            redirectToJoinPage();
        });

        apiInstance.addListener('readyToClose', () => {
            sendPresence('offline', { force: true });
            fullscreenController.exit();
            window.removeEventListener('resize', resizeJitsi);
            redirectToJoinPage();
        });

        window.addEventListener('resize', resizeJitsi);
        viewportSizing.update();
        resizeJitsi();
        window.addEventListener('beforeunload', () => {
            sendPresenceBeacon('offline');
        });
        window.addEventListener('pagehide', () => {
            sendPresenceBeacon('offline');
        });

        @if ($embedConfig['passcode'])
        apiInstance.addListener('passwordRequired', () => {
            apiInstance.executeCommand('password', @json($embedConfig['passcode']));
        });
        @endif

    });
</script>
@endpush
