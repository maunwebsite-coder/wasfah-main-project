@extends('layouts.app')

@section('title', 'غرفة ورشة: ' . $workshop->title)

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
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">جلسة مباشرة</p>
                <h1 class="mt-2 text-3xl font-bold">{{ $workshop->title }}</h1>
                <p class="mt-2 text-sm text-slate-300">
                    تأكد من تشغيل الميكروفون والكاميرا ثم اضغط زر الانضمام داخل الغرفة.
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

        @if ($workshop->meeting_started_at)
            <div class="jitsi-shell" id="jitsi-shell">
                <button type="button" class="fullscreen-btn" id="fullscreenToggle">
                    <i class="fas fa-expand"></i>
                    ملء الشاشة
                </button>
                <div class="jitsi-wrapper bg-black relative" id="jitsi-container">
                    <div class="absolute inset-x-0 top-5 mx-auto max-w-md rounded-2xl bg-slate-900/70 px-4 py-3 text-center text-sm text-slate-100 backdrop-blur transition-opacity duration-300 ease-out" id="lobbyHint">
                        سيتم فتح الغرفة بعد موافقة الشيف. يرجى البقاء في الصفحة.
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-indigo-200 bg-white/95 px-6 py-10 text-center text-slate-700 shadow-xl">
                <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-500">
                    <i class="fas fa-lock text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">ننتظر دخول الشيف</h2>
                <p class="text-sm text-slate-500">
                    لا نسمح للمشتركين بالدخول قبل أن يؤكد المضيف "أنا المضيف" ويبدأ الاجتماع. سيتم فتح الغرفة تلقائياً بعد موافقة الشيف، يرجى البقاء في هذه الصفحة.
                </p>
                <div class="mt-6 text-xs text-slate-400" id="pollStatusHint">يتم التحقق من حالة الغرفة كل بضع ثوانٍ...</div>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-300">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">
                    <i class="fas fa-user"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">اسمك في الغرفة</p>
                    <p class="font-semibold text-white">{{ $user->name }}</p>
                </div>
            </div>
            <a href="{{ route('bookings.show', $booking) }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-700 px-4 py-2 text-slate-200 transition hover:border-slate-500 hover:text-white">
                <i class="fas fa-arrow-right"></i>
                العودة إلى تفاصيل الحجز
            </a>
        </div>
    </div>
</div>
@endsection

@if ($workshop->meeting_started_at)
@push('scripts')
<script src="{{ $embedConfig['external_api_url'] }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
                lobbyEnabled: true,
                disableLobbyP2P: true,
                disableReactions: true,
                disableInviteFunctions: true,
                toolbarButtons: [
                    'microphone',
                    'camera',
                    'tileview',
                    'hangup',
                ],
            },
            interfaceConfigOverwrite: {
                SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                LANG_DETECTION: false,
                DEFAULT_REMOTE_DISPLAY_NAME: 'مشارك',
                DEFAULT_LOCAL_DISPLAY_NAME: 'أنا',
                FILM_STRIP_MAX_HEIGHT: 120,
                SETTINGS_SECTIONS: [],
                TOOLBAR_BUTTONS: [
                    'microphone',
                    'camera',
                    'tileview',
                    'hangup',
                ],
            },
            userInfo: {
                displayName: @json($user->name),
                email: @json($user->email),
            },
        };

        const api = new JitsiMeetExternalAPI(domain, options);
        const lobbyHint = document.getElementById('lobbyHint');
        let localParticipantId = null;

        const hideLobbyHint = () => {
            if (!lobbyHint || lobbyHint.dataset.dismissed === 'true') {
                return;
            }

            lobbyHint.dataset.dismissed = 'true';
            lobbyHint.classList.add('opacity-0', 'pointer-events-none', 'translate-y-1');

            const removeHint = () => lobbyHint.remove();
            lobbyHint.addEventListener('transitionend', removeHint, { once: true });
            setTimeout(removeHint, 600);
        };

        function resizeJitsi() {
            const width = container.offsetWidth;
            const height = container.offsetHeight || initialHeight;
            api?.resize(width, height);
        }

        window.addEventListener('resize', resizeJitsi);
        resizeJitsi();

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

        api.addListener('videoConferenceJoined', (event = {}) => {
            if (event.id) {
                localParticipantId = event.id;
            }
            hideLobbyHint();
        });

        api.addListener('participantJoined', (participant = {}) => {
            if (participant.local === true || participant.id === 'local') {
                localParticipantId = participant.id;
                hideLobbyHint();
            }
        });

        api.addListener('participantRoleChanged', (event = {}) => {
            const isLocal =
                event.id === localParticipantId ||
                event.id === 'local' ||
                (localParticipantId === null && event.participant?.isLocal);

            if (isLocal && event.role && event.role !== 'none') {
                hideLobbyHint();
            }
        });

        const joinedCheck = setInterval(() => {
            if (typeof api.isJoined === 'function' && api.isJoined()) {
                hideLobbyHint();
                clearInterval(joinedCheck);
            }
        }, 2500);

        const clearJoinedCheck = () => {
            clearInterval(joinedCheck);
        };

        api.addListener('videoConferenceLeft', clearJoinedCheck);
        api.addListener('readyToClose', clearJoinedCheck);
        window.addEventListener('beforeunload', clearJoinedCheck);

        if (lobbyHint?.dataset.dismissed !== 'true') {
            setTimeout(() => {
                if (typeof api.isJoined === 'function' && api.isJoined()) {
                    hideLobbyHint();
                }
            }, 5000);
        });

        const tryEnterFullscreen = () => {
            return requestFullscreen(shell)
                .catch(() => requestFullscreen(container))
                .catch(() => requestFullscreen(document.documentElement));
        };

        fullscreenBtn?.addEventListener('click', () => {
            if (getFullscreenElement() === shell || getFullscreenElement() === container || getFullscreenElement() === document.documentElement) {
                exitFullscreen().catch(() => {});
            } else {
                tryEnterFullscreen().catch(() => {
                    alert('المتصفح منع وضع ملء الشاشة. يرجى السماح بالطلب أو استخدام متصفح آخر.');
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
@else
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statusUrl = @json(route('bookings.status', $booking));
        const hint = document.getElementById('pollStatusHint');

        const pollStatus = () => {
            fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    if (data.meeting_started) {
                        hint.textContent = 'يتم فتح الغرفة الآن...';
                        window.location.reload();
                    } else {
                        setTimeout(pollStatus, 8000);
                    }
                })
                .catch(() => setTimeout(pollStatus, 10000));
        };

        setTimeout(pollStatus, 5000);
    });
</script>
@endpush
@endif
