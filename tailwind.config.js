/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')

module.exports = {
  mode: 'jit',
  // Include tailwind_base.css so @layer directives inside it are processed
  purge: [
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
      fontFamily: {
        headings: ['var(--font-headings)'],
        body: ['var(--font-primary)'],
      },
    },
  },
  plugins: [
    plugin(function ({ addUtilities, theme }) {
      addUtilities({
        '.font-headings': { fontFamily: theme('fontFamily.headings') },
        '.font-body': { fontFamily: theme('fontFamily.body') },
      })
    }),
  ],
}

