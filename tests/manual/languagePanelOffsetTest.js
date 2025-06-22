const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.evaluate(() => document.getElementById('language-panel').classList.add('active'));
  await page.waitForSelector('#language-panel.active');
  const top = await page.$eval('#language-panel', el => getComputedStyle(el).top);
  console.log('Panel top offset:', top);
  await page.evaluate(() => document.getElementById('language-panel').classList.remove('active'));
  await new Promise(r => setTimeout(r, 300));
  const isActive = await page.evaluate(() => document.getElementById('language-panel').classList.contains('active'));
  if (isActive) {
    console.error('Panel did not close');
    await browser.close();
    process.exit(1);
  }
  console.log('Panel toggled correctly');
  await browser.close();
})();
