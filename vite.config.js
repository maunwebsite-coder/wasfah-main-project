import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';


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
            ],
            refresh: true,
        }),
    ],
});