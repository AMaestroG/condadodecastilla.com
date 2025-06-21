const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_page_transitions.html');
  await page.waitForSelector('#nav-link');
  await page.click('#nav-link');
  await page.waitForSelector('#page-transition-overlay.active');
  console.log('Overlay activated');
  await browser.close();
})();
