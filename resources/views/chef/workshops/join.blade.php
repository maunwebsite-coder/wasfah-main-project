@extends('layouts.app')

@section('title', 'تشغيل ورشة: ' . $workshop->title)

@push('styles')
<style>
    .jitsi-shell {
        position: relative;
    }

    .jitsi-wrapper {
        min-height: 75vh;
        height: clamp(640px, 85vh, 1100px);
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

        <div class="mb-6 flex flex-col gap-4 rounded-3xl border border-indigo-400/40 bg-indigo-900/40 px-6 py-5 text-sm text-slate-100 shadow-lg md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-indigo-200">حالة الجلسة</p>
                @if ($workshop->meeting_started_at)
                    <h2 class="mt-1 text-lg font-semibold text-white">الغرفة مفتوحة للمشاركين</h2>
                    <p class="mt-1 text-sm text-slate-300">
                        تم تفعيل الاجتماع {{ $workshop->meeting_started_at->locale('ar')->diffForHumans() }}. يمكنك إعادة تحميل الصفحة للتأكد من بقاء الغرفة نشطة.
                    </p>
                @else
                    <h2 class="mt-1 text-lg font-semibold text-white">المشاركون بانتظارك لبدء الاجتماع</h2>
                    <p class="mt-1 text-sm text-slate-300">
                        اضغط زر <strong>بدء الاجتماع الآن</strong> بمجرد أن تصبح جاهزاً لفتح الغرفة لهم.
                    </p>
                @endif
            </div>
            <form method="POST" action="{{ route('chef.workshops.start', $workshop) }}" class="flex flex-col items-end gap-3 text-start md:text-end">
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
                            أؤكد أنني المضيف الرسمي للورشة وأتحمل مسؤولية بدء الاجتماع ومتابعة المشاركين.
                        </span>
                    </label>
                    @error('confirm_host')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                @endif
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-5 py-2 font-semibold text-slate-900 shadow hover:from-emerald-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:cursor-not-allowed disabled:opacity-60"
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
                    {{ $workshop->meeting_started_at ? 'الاجتماع قيد التشغيل' : 'بدء الاجتماع الآن' }}
                </button>
            </form>
        </div>

        @if (session('success') || session('error'))
            <div class="mb-6 rounded-3xl border {{ session('success') ? 'border-emerald-300 bg-emerald-500/10 text-emerald-100' : 'border-rose-300 bg-rose-500/10 text-rose-100' }} px-6 py-4 text-sm shadow-lg">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

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
        const confirmHostCheckbox = document.getElementById('confirmHostCheckbox');
        const startMeetingButton = document.getElementById('startMeetingButton');

        if (startMeetingButton?.dataset.requiresConfirmation === 'true') {
            const toggleStartButton = () => {
                const confirmed = Boolean(confirmHostCheckbox?.checked);
                startMeetingButton.disabled = !confirmed;
            };

            toggleStartButton();
            confirmHostCheckbox?.addEventListener('change', toggleStartButton);
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

    });
</script>
@endpush
