const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const countAfterOpen = await page.evaluate(() => document.body.classList.length);
  if (countAfterOpen !== 0) {
    console.error('Body classes should remain unchanged after opening');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const countAfterClose = await page.evaluate(() => document.body.classList.length);
  if (countAfterClose !== 0) {
    console.error('Body classes should remain unchanged after closing');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Menu opens without modifying body classes');
  await closeBrowser(browser);
})();
