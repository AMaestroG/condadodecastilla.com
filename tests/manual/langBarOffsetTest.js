const { launchBrowser, closeBrowser } = require('../helpers/puppeteerSetup');
const awaitTranslateOrSkip = require('../utils/skipIfNoTranslate');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await awaitTranslateOrSkip(page);
  await page.waitForFunction(() => {
    const el = document.getElementById('google_translate_element');
    return el && el.offsetHeight > 0;
  });

  const barHeight = await page.$eval('#google_translate_element', el => el.offsetHeight);
  const bodyStyles = await page.evaluate(() => ({
    marginTop: parseInt(getComputedStyle(document.body).marginTop) || 0,
    paddingTop: parseInt(getComputedStyle(document.body).paddingTop) || 0,
  }));

  const offset = bodyStyles.marginTop || bodyStyles.paddingTop;
  if (offset !== 0) {
    console.error(`Expected no body offset but got ${offset}`);
    await closeBrowser(browser);
    process.exit(1);
  } else {
    console.log('Body offset is zero as expected');
  }
  await closeBrowser(browser);
})();
