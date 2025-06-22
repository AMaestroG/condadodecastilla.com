const { launchBrowser, closeBrowser } = require('../helpers/puppeteerSetup');
const awaitTranslateOrSkip = require('../utils/skipIfNoTranslate');
(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html?lang=en');
  await awaitTranslateOrSkip(page);
  await new Promise(r => setTimeout(r, 4000)); // wait for API load
  const textEn = await page.$eval('#text', el => el.innerText);
  await page.goto('http://localhost:8080/tests/manual/test_lang.html?lang=fr');
  await awaitTranslateOrSkip(page);
  await new Promise(r => setTimeout(r, 5000));
  const textFr = await page.$eval('#text', el => el.innerText);
  console.log('Text EN:', textEn);
  console.log('Text FR:', textFr);
  await closeBrowser(browser);
})();
