const {JSDOM} = require('jsdom');

const dom = new JSDOM(`<body><button id="theme-toggle"><i class="fa-moon"></i></button></body>`, {
  runScripts: 'dangerously',
  url: 'https://example.com'
});

const {window} = dom;
window.localStorage.setItem('theme', 'dark');

window.eval(`
  const themeToggle = document.getElementById('theme-toggle');
  const icon = themeToggle.querySelector('i');
  themeToggle.addEventListener('click', () => {
    const isDark = document.body.classList.toggle('dark-mode');
    icon.classList.toggle('fa-sun', isDark);
    icon.classList.toggle('fa-moon', !isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
`);
window.document.dispatchEvent(new window.Event('DOMContentLoaded'));

const themeToggle = window.document.getElementById('theme-toggle');
const icon = themeToggle.querySelector('i');
themeToggle.click();
const firstHas = window.document.body.classList.contains('dark-mode');
const firstIcon = icon.classList.contains('fa-sun');
const firstStore = window.localStorage.getItem('theme');
themeToggle.click();
const secondHas = window.document.body.classList.contains('dark-mode');
const secondIcon = icon.classList.contains('fa-moon');
const secondStore = window.localStorage.getItem('theme');

if (!firstHas || !firstIcon || firstStore !== 'dark') {
  console.error('Activation failed');
  process.exit(1);
}
if (secondHas || !secondIcon || secondStore !== 'light') {
  console.error('Deactivation failed');
  process.exit(1);
}
console.log('Theme toggle class and storage toggled correctly');

