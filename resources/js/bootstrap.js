if (typeof window !== 'undefined') {
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
    const hasEchoInstance = typeof window.Echo !== 'undefined';

    if (reverbKey && !hasEchoInstance) {
        const realtimeConfig = {
            key: reverbKey,
            host: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
            scheme: import.meta.env.VITE_REVERB_SCHEME,
            port: import.meta.env.VITE_REVERB_PORT,
        };

        import('./realtime')
            .then(({ bootstrapRealtime }) => bootstrapRealtime(realtimeConfig))
            .catch((error) => {
                console.warn(
                    '[wasfah] Realtime services could not be initialized and were skipped.',
                    error,
                );
            });
    }
}
