import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; 
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            outDir: 'public',
            base: '/',
            scope: '/',
            buildBase: '/build/',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            strategies: 'injectManifest',
            srcDir: 'resources/js',
            injectRegister: false,
            registerType: 'prompt',
            includeAssets: [
                'offline.html',
                'icons/campusflow-icon-192.png',
                'icons/campusflow-icon-512.png',
                'icons/campusflow-maskable-512.png',
            ],
            manifest: {
                name: 'CampusFlow',
                short_name: 'CampusFlow',
                description: 'Gestión inteligente de espacios académicos y activos institucionales',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                orientation: 'any',
                lang: 'es',
                theme_color: '#0B1F3A',
                background_color: '#F5F7FB',
                categories: ['education', 'productivity'],
                icons: [
                    {
                        src: '/icons/campusflow-icon-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/campusflow-icon-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/campusflow-maskable-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
            injectManifest: {
                globDirectory: 'public',
                globPatterns: [
                    'build/assets/**/*.{js,css,woff,woff2,ttf,eot,png,jpg,jpeg,svg,webp}',
                    'icons/**/*.{png,svg}',
                    'offline.html',
                ],
                maximumFileSizeToCacheInBytes: 4 * 1024 * 1024,
            },
            devOptions: {
                enabled: false,
            },
        }),
    ],
});
