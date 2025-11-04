import { readFileSync } from 'node:fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

const pwaManifest = JSON.parse(
    readFileSync(new URL('./public/manifest.webmanifest', import.meta.url), 'utf-8'),
);


export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/header.js',
                'resources/js/mobile-menu.js',
                'resources/js/save-recipe.js',
                'resources/js/made-recipe.js',
                'resources/js/share-recipe.js',
                'resources/js/workshops.js',
                'resources/js/search.js',
                'resources/js/script.js',
                'resources/js/recipe.js',
                'resources/js/recipe-save-button.js',
                'resources/js/rating.js',
                'resources/js/notification-manager.js',
                'resources/js/confirmation-modal.js',
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
    ],
});
