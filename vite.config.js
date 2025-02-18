import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "resources/js"),
      "@api": path.resolve(__dirname, "resources/js/api"),
    },
  },
  server: {
    hmr: {},
  },
  plugins: [
    vue(),
    laravel({
      input: ['resources/css/app.scss', 'resources/js/app.ts'],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
