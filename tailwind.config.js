/** @type {import('tailwindcss').Config} */
module.exports = {
  // Enable Tailwind for PHP templates and JS-driven components
  content: [
    './**/*.{php,html}',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        'imperial-purple': '#4A0D67',
        'old-gold': '#CFB53B',
        alabaster: '#fdfaf6',
      },
      fontFamily: {
        'epic-title': ['Cinzel', 'serif'],
        'epic-body': ['Lora', 'serif'],
      },
    },
  },
  plugins: [
    function({ addUtilities }) {
      addUtilities({
        '.texture-alabaster': {
          'background-image': "url('/assets/img/alabastro.jpg')",
          'background-size': 'cover',
          'background-blend-mode': 'multiply',
        },
      });
    },
  ],
}

