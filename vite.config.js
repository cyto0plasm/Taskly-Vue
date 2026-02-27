import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/main.css',
                'resources/js/app.js',
                'resources/js/auth/authPage.js',
                'resources/js/SPA/main.js',
                'resources/js/utils/toolTip.js',
                'resources/js/nav/MenuHelper.js',
                'resources/js/nav/Menu.js',
                'resources/js/utils/formatData.js',
                'resources/js/utils/timeAgo.js',
                'resources/js/utils/apiHelpers.js',
                'resources/js/dashboard/chart.js',
                'resources/js/dashboard/scrollable-Cards.js',
            ],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },

    build: {
        // ✅ Required for terserOptions to actually be used
        minify: 'terser',

        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
            format: {
                comments: false,
            },
        },

        // ✅ es2020 is safer than es2015 and produces smaller output
        //    All modern browsers (and Laravel's typical audience) support it
        target: 'es2020',

        // ✅ Correct way to check env in Vite build context
        sourcemap: process.env.NODE_ENV !== 'production',

        chunkSizeWarningLimit: 500,

        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (
                        id.includes('node_modules/vue') ||
                        id.includes('node_modules/@vue') ||
                        id.includes('node_modules/pinia')
                    ) {
                        return 'vendor-vue'
                    }

                    if (
                        id.includes('node_modules/axios') ||
                        id.includes('node_modules/sortablejs')
                    ) {
                        return 'vendor-utils'
                    }

                    // ✅ Fabric is large (~2MB) — isolate it so other chunks
                    //    don't get bloated and it can be cached independently
                    if (id.includes('node_modules/fabric')) {
                        return 'vendor-fabric'
                    }
                },
            },
        },
    },

    optimizeDeps: {
        include: ['vue', 'pinia', 'axios', 'sortablejs', 'fabric'],
    },
})
