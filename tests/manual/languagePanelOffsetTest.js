const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await page.waitForSelector('#language-panel.active');
  await page.waitForSelector('#language-panel #google_translate_element', {visible: true});
  const top = await page.$eval('#language-panel', el => getComputedStyle(el).top);
  console.log('Panel top offset:', top);
  await page.click('#flag-toggle');
  await page.waitForTimeout(300);
  const isActive = await page.evaluate(() => document.getElementById('language-panel').classList.contains('active'));
  if (isActive) {
    console.error('Panel did not close');
    await browser.close();
    process.exit(1);
  }
  console.log('Panel toggled correctly');
  await browser.close();
})();
