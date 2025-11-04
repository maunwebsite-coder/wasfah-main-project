@extends('layouts.app')

@section('title', 'تشغيل ورشة: ' . $workshop->title)

@push('styles')
<style>
    .jitsi-shell {
        position: relative;
    }

    .jitsi-wrapper {
        min-height: 65vh;
        height: clamp(560px, 75vh, 900px);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
    }

    .fullscreen-btn {
        position: absolute;
        top: 1rem;
        inset-inline-end: 1rem;
        z-index: 20;
        background: rgba(15, 23, 42, 0.75);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(6px);
        border-radius: 999px;
        padding: 0.35rem 1rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: background 0.2s ease;
    }

    .fullscreen-btn:hover {
        background: rgba(15, 23, 42, 0.92);
    }

    @media (max-width: 640px) {
        .jitsi-wrapper {
            min-height: 60vh;
            border-radius: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 py-10 text-white">
    <div class="mx-auto max-w-5xl px-4">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">غرفة الشيف</p>
                <h1 class="mt-2 text-3xl font-bold">{{ $workshop->title }}</h1>
                <p class="mt-2 text-sm text-slate-300">
                    هذا العرض خاص بك كشيف. لن يظهر رابط Jitsi للمشاركين، استخدمه لبدء الجلسة أو مراقبتها.
                </p>
            </div>
            <div class="inline-flex flex-col items-end gap-2 text-sm text-slate-300">
                <span class="text-lg font-semibold text-white">
                    {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') }}
                </span>
                <span class="text-emerald-300">
                    المدة التقريبية: {{ $workshop->duration }} دقيقة
                </span>
            </div>
        </div>

        <div class="mb-6 rounded-3xl border border-indigo-400/40 bg-indigo-900/40 px-6 py-5 text-sm text-slate-100 shadow-lg">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-indigo-200">وضع الدخول</p>
                    <h2 class="mt-1 text-lg font-semibold text-white">المشاركون بانتظار تأكيدك كمضيف</h2>
                    <p class="text-slate-300 text-sm mt-1">
                        لن يتمكن المشتركون من دخول الغرفة حتى تضغط زر <strong>بدء الاجتماع</strong> أدناه. يمكنك الضغط عليه بمجرد أن تصبح جاهزاً.
                    </p>
                </div>
                <div class="flex flex-col items-start gap-2 text-xs text-slate-200" id="meetingStateLabel">
                    @if ($workshop->meeting_started_at)
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/20 px-3 py-1 font-semibold text-emerald-200">
                            <i class="fas fa-check-circle"></i>
                            تم بدء الاجتماع {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}
                        </span>
                    @else
                        <button type="button" id="startMeetingBtn"
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:from-emerald-500 hover:to-emerald-600">
                            <i class="fas fa-play"></i>
                            بدء الاجتماع الآن
                        </button>
                        <span class="mt-1 text-slate-400">سيظهر الزر كمؤشر للمشاركين بأن الغرفة مفتوحة.</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="jitsi-shell" id="jitsi-shell">
            <button type="button" class="fullscreen-btn" id="fullscreenToggle">
                <i class="fas fa-expand"></i>
                ملء الشاشة
            </button>
            <div class="jitsi-wrapper bg-black" id="jitsi-container"></div>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-300">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">
                    <i class="fas fa-user-tie"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">سيظهر اسمك للمشاركين كالتالي</p>
                    <p class="font-semibold text-white">{{ $user->name }}</p>
                </div>
            </div>
            <a href="{{ route('chef.workshops.index') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-slate-200 transition hover:border-slate-500 hover:text-white">
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
        const container = document.getElementById('jitsi-container');
        const shell = document.getElementById('jitsi-shell');
        const fullscreenBtn = document.getElementById('fullscreenToggle');
        const startBtn = document.getElementById('startMeetingBtn');
        const stateLabel = document.getElementById('meetingStateLabel');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (typeof JitsiMeetExternalAPI === 'undefined' || !container) {
            alert('تعذر تحميل غرفة الاجتماع. يرجى إعادة تحديث الصفحة أو التحقق من الاتصال.');
            return;
        }

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
                disableDeepLinking: true,
                startWithAudioMuted: false,
                startWithVideoMuted: false,
            },
            interfaceConfigOverwrite: {
                SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                LANG_DETECTION: false,
                DEFAULT_REMOTE_DISPLAY_NAME: 'مشارك',
                DEFAULT_LOCAL_DISPLAY_NAME: 'أنا',
                FILM_STRIP_MAX_HEIGHT: 120,
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

        const handleMeetingStart = async () => {
            if (!startBtn || !csrfToken) {
                return;
            }

            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> يتم البدء...';

            try {
                const response = await fetch(@json(route('chef.workshops.start', $workshop)), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'تعذر بدء الاجتماع، حاول مرة أخرى.');
                }

                if (stateLabel) {
                    stateLabel.innerHTML = `
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/20 px-3 py-1 font-semibold text-emerald-200">
                            <i class="fas fa-check-circle"></i>
                            تم بدء الاجتماع للتو
                        </span>
                    `;
                }
            } catch (error) {
                alert(error?.message || 'تعذر بدء الاجتماع، حاول مرة أخرى.');
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="fas fa-play"></i> بدء الاجتماع الآن';
            }
        };

        startBtn?.addEventListener('click', handleMeetingStart);
    });
</script>
@endpush
