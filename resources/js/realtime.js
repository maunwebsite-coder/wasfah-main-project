const resolveScheme = (candidate) => {
    if (candidate === 'http' || candidate === 'https') {
        return candidate;
    }
    return window.location.protocol === 'https:' ? 'https' : 'http';
};

const resolvePort = (scheme, candidate) => {
    const numeric = Number(candidate);
    if (Number.isFinite(numeric) && numeric > 0) {
        return numeric;
    }
    return scheme === 'https' ? 443 : 80;
};

export async function bootstrapRealtime(config) {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return;
    }

    const [{ default: Echo }, { default: Pusher }] = await Promise.all([
        import('laravel-echo'),
        import('pusher-js/dist/web/pusher.min.js'),
    ]);

    window.Pusher = Pusher;

    const scheme = resolveScheme(config?.scheme);
    const port = resolvePort(scheme, config?.port);

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: config?.key,
        wsHost: config?.host ?? window.location.hostname,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    document.dispatchEvent(
        new CustomEvent('echo:initialized', {
            detail: { driver: 'reverb' },
        }),
    );
}
