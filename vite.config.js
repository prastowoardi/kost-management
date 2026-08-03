import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/dashboard.js',
                'resources/js/pages/payments-index.js',
                'resources/js/pages/payments-form.js',
                'resources/js/pages/rooms-show.js',
                'resources/js/pages/rooms-form.js',
                'resources/js/pages/complaints-form.js',
                'resources/js/pages/public-register.js',
                'resources/js/pages/admin-logs.js',
                'resources/js/pages/tenants-form.js',
                'resources/js/welcome.js',
                'resources/js/admin-receipt.js',
            ],
            refresh: true,
        }),
    ],
});
