import { defineConfig } from 'vite';
import path from 'path';
import tailwindcss from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
  root: path.resolve(__dirname, 'src'),
  build: {
    outDir: path.resolve(__dirname, 'dist'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/js/menu.js'),
        style: path.resolve(__dirname, 'src/css/style.css'),
      },
      output: {
        assetFileNames: 'assets/[name][extname]',
        entryFileNames: 'assets/[name].js',
      },
    },
  },
  css: {
    postcss: {
      plugins: [tailwindcss(), autoprefixer()],
    },
  },
});
