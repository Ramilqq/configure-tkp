import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            publicDirectory: 'public_html'
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            //'@': '/resources/js',
        },
    },
    build: {
        outDir: 'public_html/build',
    },
});
