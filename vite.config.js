import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
  css: {
    postcss: {
      plugins: [tailwindcss('./tailwind.config.js'), autoprefixer()]
    }
  },
  build: {
    cssCodeSplit: false,
    rollupOptions: {
      input: 'tailwind_entry.js',
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') return 'css/tailwind.min.css';
          return assetInfo.name;
        }
      }
    },
    outDir: 'assets/vendor',
    emptyOutDir: false
  }
});
