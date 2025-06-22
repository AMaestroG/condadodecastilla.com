const { launchBrowser, closeBrowser } = require('../helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await page.waitForSelector('#language-panel.active');
  await page.waitForSelector('#language-panel #google_translate_element', {visible: true});
  const top = await page.$eval('#language-panel', el => getComputedStyle(el).top);
  console.log('Panel top offset:', top);
  await page.click('#flag-toggle');
  await page.waitForFunction(() => !document.getElementById('language-panel').classList.contains('active'));
  const isActive = await page.evaluate(() => document.getElementById('language-panel').classList.contains('active'));
  if (isActive) {
    console.error('Panel did not close');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Panel toggled correctly');
  await closeBrowser(browser);
})();
