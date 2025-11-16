import { readFileSync } from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import { constants as zlibConstants } from 'node:zlib';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

const pwaManifest = JSON.parse(
    readFileSync(new URL('./public/manifest.webmanifest', import.meta.url), 'utf-8'),
);

const require = createRequire(path.join(process.cwd(), 'package.json'));
let viteCompression;

try {
    ({ default: viteCompression } = require('vite-plugin-compression'));
} catch (error) {
    console.warn('vite-plugin-compression not available, skipping asset compression.', error.message);
}

const buildCompressionPlugins = () => {
    if (!viteCompression) {
        return [];
    }

    return [
        viteCompression({
            algorithm: 'brotliCompress',
            ext: '.br',
            include: [/\.(js|css|mjs|json|html|svg)$/i],
            compressionOptions: {
                params: {
                    [zlibConstants.BROTLI_PARAM_MODE]: zlibConstants.BROTLI_MODE_TEXT,
                    [zlibConstants.BROTLI_PARAM_QUALITY]: 11,
                },
            },
        }),
        viteCompression({
            algorithm: 'gzip',
            ext: '.gz',
            include: [/\.(js|css|mjs|json|html|svg)$/i],
            compressionOptions: { level: zlibConstants.Z_BEST_COMPRESSION },
        }),
    ];
};

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/non-critical.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            devOptions: {
                enabled: true,
            },
            includeAssets: [
                'icons/icon-192x192.png',
                'icons/icon-512x512.png',
                'robots.txt',
                'favicon.ico',
            ],
            manifest: pwaManifest,
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,json,woff2}'],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.destination === 'document',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'html-cache',
                        },
                    },
                    {
                        urlPattern: ({ request }) => request.destination === 'image',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'image-cache',
                            expiration: {
                                maxEntries: 60,
                                maxAgeSeconds: 7 * 24 * 60 * 60,
                            },
                        },
                    },
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/api/'),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            networkTimeoutSeconds: 5,
                        },
                    },
                ],
            },
        }),
        ...buildCompressionPlugins(),
    ],
    build: {
        // Ensure Laravel can resolve assets without relying on the dev server
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
                manualChunks: {
                    'vendor-realtime': ['laravel-echo', 'pusher-js'],
                },
            },
        },
    },
});
