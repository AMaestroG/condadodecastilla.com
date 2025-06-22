const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await page.waitForSelector('script[src*="translate_a/element.js"]');
  console.log('Google translate script inserted');
  await browser.close();
})();
