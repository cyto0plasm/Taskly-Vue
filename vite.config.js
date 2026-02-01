import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue' // 👈 مهم جداً

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/spa/main.js' // 👈 ال SPA entry
            ],
            refresh: true,
        }),
        vue(), // 👈 لازم عشان Vite يعرف يتعامل مع .vue
    ],
    resolve: {
        alias: {
            '@': '/resources/js', // اختصار للوصول لملفات js
        },
    },
})
