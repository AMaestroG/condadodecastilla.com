const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (hasClass) {
    console.error('menu-compressed class should not be present');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const stillHasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (stillHasClass) {
    console.error('menu-compressed class still present after closing');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Menu opens without adding menu-compressed');
  await closeBrowser(browser);
})();
