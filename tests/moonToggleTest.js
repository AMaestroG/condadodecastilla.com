const fs = require('fs');
const {JSDOM} = require('jsdom');

const headerHtml = fs.readFileSync('_header.php', 'utf8');
const scriptJs = fs.readFileSync('assets/js/main.js', 'utf8');

const dom = new JSDOM(`<body>${headerHtml}</body>`, {
  runScripts: "dangerously",
  resources: "usable",
  url: "https://example.com"
});

const {window} = dom;
window.localStorage.setItem('theme', 'moon');

const scriptEl = window.document.createElement('script');
scriptEl.textContent = scriptJs;
window.document.body.appendChild(scriptEl);

window.addEventListener('DOMContentLoaded', () => {
  const moonToggle = window.document.getElementById('moon-toggle');
  if (!moonToggle) {
    console.error('moon-toggle button not found');
    process.exit(1);
  }
  const icon = moonToggle.querySelector('i');
  moonToggle.click();
  const firstHas = window.document.body.classList.contains('luna');
  const firstIcon = icon.classList.contains('fa-sun');
  const firstStore = window.localStorage.getItem('theme');
  moonToggle.click();
  const secondHas = window.document.body.classList.contains('luna');
  const secondIcon = icon.classList.contains('fa-moon');
  const secondStore = window.localStorage.getItem('theme');

  if (!firstHas || !firstIcon || firstStore !== 'moon') {
    console.error('Activation failed');
    process.exit(1);
  }
  if (secondHas || !secondIcon || secondStore !== 'light') {
    console.error('Deactivation failed');
    process.exit(1);
  }
  console.log('Moon toggle class and storage toggled correctly');
});
