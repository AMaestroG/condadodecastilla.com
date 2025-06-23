const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#menu-toggle');
  await page.click('#menu-toggle');
  await page.waitForTimeout(500);
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (!hasClass) {
    console.error('menu-compressed class should be present when menu opens');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#menu-toggle');
  await page.waitForTimeout(500);
  const stillHasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (stillHasClass) {
    console.error('menu-compressed class still present after closing');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Menu toggles menu-compressed correctly');
  await closeBrowser(browser);
})();
