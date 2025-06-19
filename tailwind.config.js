/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.{php,html}',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
      },
    },
  },
  plugins: [],
}

