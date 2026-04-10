import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'
import path from 'path'

const __dirname = new URL('.', import.meta.url).pathname

export default defineConfig({
  root: 'resources',
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources'),
    },
  },
  envDir: path.resolve(__dirname, '../../'),
  base: '/company-panel/',
  plugins: [
    vue(),
    vuetify({ autoImport: true }),
  ],
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks: {
          vue: ['vue', 'vue-router', 'pinia'],
          vuetify: ['vuetify'],
        },
      },
    },
  },
})
