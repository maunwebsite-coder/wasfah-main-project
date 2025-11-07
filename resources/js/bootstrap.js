import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;

    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
    const hasEchoInstance = typeof window.Echo !== 'undefined';

    if (reverbKey && !hasEchoInstance) {
        const scheme =
            import.meta.env.VITE_REVERB_SCHEME ??
            (window.location.protocol === 'https:' ? 'https' : 'http');
        const port =
            Number(import.meta.env.VITE_REVERB_PORT) ||
            (scheme === 'https' ? 443 : 80);

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
            wsPort: port,
            wssPort: port,
            forceTLS: scheme === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        if (typeof document !== 'undefined') {
            document.dispatchEvent(
                new CustomEvent('echo:initialized', {
                    detail: { driver: 'reverb' },
                }),
            );
        }
    }
}
