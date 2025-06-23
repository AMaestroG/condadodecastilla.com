const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');
const awaitTranslateOrSkip = require('./utils/skipIfNoTranslate');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await awaitTranslateOrSkip(page);
  const hasClassAfterOpen = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (hasClassAfterOpen) {
    console.error('menu-open-right should not be added');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#flag-toggle');
  const hasClassAfterClose = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (hasClassAfterClose) {
    console.error('menu-open-right should remain absent after closing');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Language panel no longer toggles body class');
  await closeBrowser(browser);
})();
