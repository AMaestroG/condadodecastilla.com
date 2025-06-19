const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#lang-panel-toggle');
  await page.click('#lang-panel-toggle');
  await page.waitForTimeout(500);
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (!hasClass) {
    console.error('menu-open-right class not added');
    await browser.close();
    process.exit(1);
  }
  await page.click('#close-language-panel');
  await page.waitForTimeout(500);
  const stillHasClass = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (stillHasClass) {
    console.error('menu-open-right class not removed');
    await browser.close();
    process.exit(1);
  }
  console.log('Language panel body class toggled correctly');
  await browser.close();
})();
