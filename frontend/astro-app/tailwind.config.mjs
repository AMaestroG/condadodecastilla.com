import { defineConfig } from 'tailwindcss';
import preset from '../../tailwind.config.js';
export default defineConfig({
  presets: [preset],
  content: ['src/**/*.{astro,html,js}'],
});
