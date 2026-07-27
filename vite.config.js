import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            
        }),
        visualizer({
            open: true, // Automatically open the report in your browser
        }),
        vue(),
    ],
    optimizeDeps: {
        include: [
            'jquery',
            
            'datatables.net',

            'datatables.net-dt',

            'datatables.net-buttons',

            'datatables.net-buttons-dt'
        ], 
    },    
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core framework vendor chunk
                    'core-vendor': ['vue', 'pinia', 'axios', 'lodash'],
                    // Heavy fullcalendar vendor chunk
                    'fullcalendar-vendor': [
                        '@fullcalendar/core',
                        '@fullcalendar/daygrid',
                        '@fullcalendar/timegrid',
                        '@fullcalendar/interaction',
                        '@fullcalendar/multimonth'
                    ],
                    // Heavy datatables vendor chunk
                    'datatables-vendor': [
                        'jquery',
                        'datatables.net',
                        'datatables.net-dt'
                    ],
                },
            },
        },
    },
});