const fs = require('fs');
const {JSDOM} = require('jsdom');

const headerHtml = fs.readFileSync('fragments/header.php', 'utf8');
const scriptJs = fs.readFileSync('assets/js/main.js', 'utf8');

const dom = new JSDOM(`<body>${headerHtml}</body>`, {
  runScripts: "dangerously",
  resources: "usable",
  url: "https://example.com"
});

const {window} = dom;
window.localStorage.setItem('theme', 'dark');

const scriptEl = window.document.createElement('script');
scriptEl.textContent = scriptJs;
window.document.body.appendChild(scriptEl);

window.addEventListener('DOMContentLoaded', () => {
  const themeToggle = window.document.getElementById('theme-toggle');
  const icon = themeToggle.querySelector('i');
  console.log('initial', icon.className, window.document.body.classList.contains('dark-mode'));
  themeToggle.click();
  console.log('afterClick', icon.className, window.document.body.classList.contains('dark-mode'));
  themeToggle.click();
  console.log('afterSecond', icon.className, window.document.body.classList.contains('dark-mode'));
});
