@extends('layouts.app')

@section('title', 'غرفة الإدارة - ' . $workshop->title)

@push('styles')
<style>
    body {
        background: radial-gradient(circle at top left, rgba(15,23,42,0.98), rgba(30,41,59,0.92));
        min-height: 100vh;
    }

    footer {
        display: none !important;
    }

    .admin-meeting-shell {
        display: grid;
        gap: 2rem;
        grid-template-columns: minmax(0, 1fr);
    }

    .admin-meeting-card {
        border-radius: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: linear-gradient(140deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.86));
        box-shadow: 0 30px 60px -40px rgba(15, 23, 42, 0.8);
        color: #e2e8f0;
    }

    .admin-meeting-info {
        backdrop-filter: blur(12px);
        background: rgba(248, 250, 252, 0.06);
        border-radius: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .jitsi-host-wrapper {
        min-height: clamp(640px, 82vh, 1100px);
        border-radius: 1.75rem;
        overflow: hidden;
        position: relative;
    }

    .jitsi-loading-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.9);
        z-index: 10;
    }

    .jitsi-loading-overlay.hidden {
        display: none;
    }

</style>
@endpush

@section('content')
<div class="min-h-screen py-10">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4">
        <div class="admin-meeting-card p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-sky-300/80">وحدة تحكم الإدمن</p>
                    <h1 class="mt-3 text-3xl font-bold text-white sm:text-4xl">{{ $workshop->title }}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-slate-300">
                        هذه الغرفة تمنحك جميع أدوات إدارة اجتماع Jitsi، بما في ذلك التحكم بالمشاركين، التسجيل، البث المباشر، وتغيير الإعدادات المتقدمة.
                    </p>
                </div>
                <div class="admin-meeting-info p-5 text-sm text-slate-200">
                    <div class="flex items-center gap-2 text-emerald-300">
                        <i class="fas fa-check-circle"></i>
                        <span>الورشة جاهزة للبث كمضيف إداري</span>
                    </div>
                    <div class="mt-3 flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-sky-300"></i>
                            <span>
                                {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') ?? 'غير محدد' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-link text-sky-300"></i>
                            <code class="truncate rounded-xl bg-slate-900/70 px-3 py-1.5 text-xs text-slate-200">{{ $workshop->meeting_link }}</code>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user-shield text-sky-300"></i>
                            <span>{{ $user->name }}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-400 to-sky-500 px-4 py-2 text-xs font-semibold text-slate-900 shadow-lg transition hover:shadow-xl"
                        data-copy-target="{{ $workshop->meeting_link }}"
                        id="copyAdminMeetingLink">
                        <i class="fas fa-copy"></i>
        نسخ الرابط الإداري
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-meeting-shell">
            <div class="admin-meeting-card p-2">
                <div class="relative jitsi-host-wrapper" id="adminJitsiContainer">
                    <div class="jitsi-loading-overlay" id="jitsiLoadingOverlay">
                        <div class="flex flex-col items-center gap-3">
                            <span class="h-14 w-14 animate-spin rounded-full border-4 border-slate-700 border-t-emerald-400"></span>
                            <p class="text-sm text-slate-200">يتم تحميل غرفة Jitsi مع كامل الأدوات...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const copyButton = document.getElementById('copyAdminMeetingLink');
        copyButton?.addEventListener('click', () => {
            const target = copyButton.dataset.copyTarget;
            navigator.clipboard.writeText(target).then(() => {
                copyButton.classList.add('ring-4', 'ring-emerald-300/60');
                copyButton.innerHTML = '<i class="fas fa-check-circle"></i> تم النسخ';
                setTimeout(() => {
                    copyButton.classList.remove('ring-4', 'ring-emerald-300/60');
                    copyButton.innerHTML = '<i class="fas fa-copy"></i> نسخ الرابط الإداري';
                }, 2200);
            });
        });

        const externalApiUrl = @json($embedConfig['external_api_url']);
        const container = document.getElementById('adminJitsiContainer');
        const overlay = document.getElementById('jitsiLoadingOverlay');
        const initialHeight = container.offsetHeight || 720;

        function initializeJitsi() {
            const domain = @json($embedConfig['domain']);
            const jaasJwt = @json($embedConfig['jwt'] ?? null);
            const embedProvider = @json($embedConfig['provider'] ?? null);
            const toolbarButtons = [
                'camera',
                'microphone',
                'chat',
                'desktop',
                'hangup',
                'tileview',
                'raisehand',
                'participants-pane',
                'settings',
                'filmstrip',
                'select-background',
                'shortcuts',
                'toggle-camera',
                'fullscreen',
            ];
            const options = {
                roomName: @json($embedConfig['room']),
                parentNode: container,
                width: '100%',
                height: initialHeight,
                lang: 'ar',
                userInfo: {
                    displayName: @json($user->name),
                    email: @json($user->email),
                },
                configOverwrite: {
                    prejoinPageEnabled: false,
                    disableDeepLinking: true,
                    startWithAudioMuted: false,
                    startWithVideoMuted: false,
                    enableClosePage: true,
                    toolbarButtons,
                },
                interfaceConfigOverwrite: {
                    SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                    SETTINGS_SECTIONS: ['devices', 'language', 'moderation', 'profile', 'calendar', 'more'],
                    DISABLE_JOIN_LEAVE_NOTIFICATIONS: false,
                },
            };

            if (embedProvider === 'jaas') {
                options.jwt = jaasJwt ? jaasJwt : 'your_token_here';
            }

            const api = new JitsiMeetExternalAPI(domain, options);

            overlay?.classList.add('hidden');

            const resizeJitsi = () => {
                const width = container.offsetWidth;
                const height = container.offsetHeight || initialHeight;
                api?.resize(width, height);
            };

            window.addEventListener('resize', resizeJitsi);
            resizeJitsi();

            @if ($embedConfig['passcode'])
            api.addListener('passwordRequired', () => {
                api.executeCommand('password', @json($embedConfig['passcode']));
            });
            @endif
        }

        if (typeof JitsiMeetExternalAPI !== 'undefined') {
            initializeJitsi();
        } else {
            const script = document.createElement('script');
            script.src = externalApiUrl;
            script.async = true;
            script.onload = initializeJitsi;
            script.onerror = () => {
                overlay?.classList.remove('hidden');
                overlay.innerHTML = '<p class="text-sm text-rose-200">تعذّر تحميل مكتبة Jitsi. يرجى تحديث الصفحة أو التحقق من الاتصال.</p>';
            };
            document.head.appendChild(script);
        }
    });
</script>
@endpush
