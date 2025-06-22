const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await page.waitForFunction(() => document.body.classList.contains('menu-open-right'));
  await page.waitForSelector('#language-panel #google_translate_element', {visible: true});
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (!hasClass) {
    console.error('menu-open-right not added');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.click('#flag-toggle');
  await page.waitForFunction(() => !document.body.classList.contains('menu-open-right'));
  const stillHas = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (stillHas) {
    console.error('menu-open-right not removed');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Language panel body class toggled correctly');
  await closeBrowser(browser);
})();
