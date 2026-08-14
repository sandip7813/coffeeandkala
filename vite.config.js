import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/gallery.css',
                'resources/js/gallery.js',
                'resources/css/studio.css',
                'resources/css/journal.css',
                'resources/css/features.css',
                'resources/css/features-themes.css',
                'resources/js/features.js',
                'resources/css/poetry.css',
                'resources/js/poetry.js',
                'resources/css/article.css',
                'resources/js/article.js',
                'resources/css/adminlte.css',
                'resources/js/adminlte.js',
                'resources/css/error.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
