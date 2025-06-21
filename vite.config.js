import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';
import { resolve } from 'path';

export default defineConfig({
  css: {
    postcss: {
      plugins: [tailwindcss('./tailwind.config.js'), autoprefixer()]
    }
  },
  build: {
    cssCodeSplit: false,
    rollupOptions: {
      input: {
        tailwind: resolve(__dirname, 'tailwind_entry.js'),
        main: resolve(__dirname, 'assets/js/bundle-entry.js')
      },
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') return 'css/tailwind.min.css';
          return assetInfo.name;
        },
        entryFileNames: (chunkInfo) => {
          if (chunkInfo.name === 'main') return 'js/main.min.js';
          return 'assets/[name].js';
        }
      }
    },
    outDir: 'assets/vendor',
    emptyOutDir: false
  }
});
