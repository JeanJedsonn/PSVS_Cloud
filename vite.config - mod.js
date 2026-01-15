import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// vite.config.js
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'axios'],
                    livewire: ['@alpinejs/persist', '@alpinejs/focus']
                }
            }
        }
    }
});