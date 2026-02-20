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
                'resources/js/SPA/main.js',
                'resources/js/utils/toolTip.js',
                'resources/js/nav/MenuHelper.js',
                'resources/js/nav/Menu.js',
                'resources/js/utils/formatData.js',
                'resources/js/utils/timeAgo.js',
                'resources/js/utils/apiHelpers.js',
                'resources/js/dashboard/chart.js',
                'resources/js/dashboard/scrollable-Cards.js'
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
        rollupOptions: {
            output: {
                //  Manual chunk splitting for better caching
                manualChunks: (id) => {
                    // Vue ecosystem
                    if (id.includes('node_modules/vue') ||
                        id.includes('node_modules/@vue') ||
                        id.includes('node_modules/pinia')) {
                        return 'vendor-vue'
                    }

                    // Utilities
                    if (id.includes('node_modules/axios') ||
                        id.includes('node_modules/sortablejs')) {
                        return 'vendor-utils'
                    }

                    // Remove alpinejs entirely from manual chunks - we're removing it
                    // if (id.includes('node_modules/alpinejs')) {
                    //     return 'vendor-alpine'
                    // }
                }
            }
        },
        //  Aggressive minification

        terserOptions: {
            compress: {
                drop_console: true,      // Remove console.log
                drop_debugger: true,     // Remove debugger
                pure_funcs: ['console.log', 'console.info', 'console.debug'], // Remove specific console methods
            },
            format: {
                comments: false,         // Remove comments
            },
        },
        //  Target modern browsers for smaller code
        target: 'es2015',
        //  Generate source maps only in dev
        sourcemap: process.env.NODE_ENV !== 'production',
        //  Chunk size warning limit
        chunkSizeWarningLimit: 500,
    },
    //  Optimize deps pre-bundling
    optimizeDeps: {
        include: [
            'vue',
            'pinia',
            'axios',
            'sortablejs',
            'fabric'
        ],
        exclude: [
            // Add any deps that shouldn't be pre-bundled
        ],
    },
})
