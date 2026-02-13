import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts', 'resources/css/app.css'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        // Wayfinder tylko w dev (nie w Docker build)
        // W Docker build PHP nie jest dostępne
        ...(process.env.SKIP_WAYFINDER !== 'true' ? [
            (await import('@laravel/vite-plugin-wayfinder')).default({
                formVariants: true,
            })
        ] : []),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
