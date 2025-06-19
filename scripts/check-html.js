const fs = require('fs');
const { JSDOM } = require('jsdom');

const files = process.argv.slice(2);
if (files.length === 0) {
  console.error('Usage: node scripts/check-html.js <file1> [file2 ...]');
  process.exit(1);
}

let hasErrors = false;

files.forEach((file) => {
  const html = fs.readFileSync(file, 'utf8');
  const dom = new JSDOM(html);
  const { document } = dom.window;

  const missingLang = !document.documentElement.lang;
  const h1Count = document.querySelectorAll('h1').length;

  if (missingLang) {
    console.error(`${file}: <html> tag is missing a lang attribute`);
    hasErrors = true;
  }

  if (h1Count > 1) {
    console.error(`${file}: found ${h1Count} <h1> tags (expected 1)`);
    hasErrors = true;
  }
});

process.exit(hasErrors ? 1 : 0);
