import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fg from 'fast-glob';
import vue from '@vitejs/plugin-vue'; 

const tsFiles = fg.sync('resources/js/**/*.js', { cwd: process.cwd() });
const sassFiles = fg.sync('resources/sass/**/*.scss', { cwd: process.cwd() });

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...tsFiles,
                ...sassFiles,
            ],
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
    ],
});
