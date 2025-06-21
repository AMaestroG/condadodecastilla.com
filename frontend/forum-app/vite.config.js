import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  base: '/assets/forum-app/',
  build: {
    outDir: '../../assets/forum-app',
    emptyOutDir: false,
    rollupOptions: {
      output: {
        entryFileNames: 'forum.js',
        chunkFileNames: 'forum-[name].js',
        assetFileNames: 'forum-[name][extname]'
      }
    }
  },
})
