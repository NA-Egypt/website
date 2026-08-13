import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/frontend-app.css',
                'resources/js/app.js',
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
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('@fullcalendar')) {
                            return 'vendor-fullcalendar';
                        }
                        if (id.includes('datatables.net')) {
                            return 'vendor-datatables';
                        }
                        if (id.includes('sweetalert2')) {
                            return 'vendor-sweetalert';
                        }
                        if (id.includes('select2')) {
                            return 'vendor-select2';
                        }
                        if (id.includes('vue') || id.includes('pinia') || id.includes('axios')) {
                            return 'core-vendor';
                        }
                    }
                }
            },
        },
    },
});