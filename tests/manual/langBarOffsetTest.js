const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  try {
    await page.waitForSelector('#google_translate_element', {visible: true, timeout: 7000});
  } catch (e) {
    console.log('google_translate_element did not load; skipping test');
    await browser.close();
    process.exit(0);
  }
  // give time for height calculation
  await page.waitForTimeout(500);

  const barHeight = await page.$eval('#google_translate_element', el => el.offsetHeight);
  const bodyStyles = await page.evaluate(() => ({
    marginTop: parseInt(getComputedStyle(document.body).marginTop) || 0,
    paddingTop: parseInt(getComputedStyle(document.body).paddingTop) || 0,
  }));

  const offset = bodyStyles.marginTop || bodyStyles.paddingTop;
  if (offset !== 0) {
    console.error(`Expected no body offset but got ${offset}`);
    await browser.close();
    process.exit(1);
  } else {
    console.log('Body offset is zero as expected');
  }
  await browser.close();
})();
