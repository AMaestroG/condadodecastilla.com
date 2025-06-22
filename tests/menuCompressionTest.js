const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.click('#consolidated-menu-button');
  await page.waitForFunction(() => document.body.classList.contains('menu-compressed'));
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (!hasClass) {
    console.error('menu-compressed class not added');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForFunction(() => !document.body.classList.contains('menu-compressed'));
  const stillHasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (stillHasClass) {
    console.error('menu-compressed class not removed');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Menu compression class toggled correctly');
  await closeBrowser(browser);
})();
