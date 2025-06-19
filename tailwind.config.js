/** @type {import('tailwindcss').Config} */
module.exports = {
  // Include tailwind_base.css so @layer directives inside it are processed
  content: [
    './**/*.{php,html}',
    './assets/js/**/*.js',
    './assets/css/tailwind_base.css',
  ],
  theme: {
    extend: {
      colors: {
        purple: '#800080',
        'old-gold': '#cfb53b',
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
      },
    },
  },
  plugins: [],
}

