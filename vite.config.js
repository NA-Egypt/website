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
                    // Group vendor libraries into separate chunks
                    vendor: ['lodash', 'axios', 'vue'],
                    // Group specific features or routes into separate chunks
                    //dashboard: ['./resources/js/views/Dashboard.js'],
                    //settings: ['./resources/js/views/Settings.js'],
                },
            },
        },
    },
});