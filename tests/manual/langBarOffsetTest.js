const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html');
  await page.waitForSelector('#lang-bar-toggle');
  await page.click('#lang-bar-toggle');
  await page.waitForSelector('#google_translate_element', {visible: true});
  // give time for height calculation
  await page.waitForTimeout(500);

  const barHeight = await page.$eval('#google_translate_element', el => el.offsetHeight);
  const bodyStyles = await page.evaluate(() => ({
    marginTop: parseInt(getComputedStyle(document.body).marginTop) || 0,
    paddingTop: parseInt(getComputedStyle(document.body).paddingTop) || 0,
  }));

  const offset = bodyStyles.marginTop || bodyStyles.paddingTop;
  if (offset !== barHeight) {
    console.error(`Expected body offset ${offset} to equal bar height ${barHeight}`);
    process.exit(1);
  } else {
    console.log('Body offset matches language bar height:', barHeight);
  }
  await browser.close();
})();
