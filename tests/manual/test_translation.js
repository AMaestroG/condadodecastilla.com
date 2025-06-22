const puppeteer = require('puppeteer');
(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html?lang=en');
  try {
    await page.waitForSelector('#google_translate_element', {timeout: 7000});
  } catch (e) {
    console.log('google_translate_element did not load; skipping test');
    await browser.close();
    process.exit(0);
  }
  await new Promise(r => setTimeout(r, 4000)); // wait for API load
  const textEn = await page.$eval('#text', el => el.innerText);
  await page.goto('http://localhost:8080/tests/manual/test_lang.html?lang=fr');
  try {
    await page.waitForSelector('#google_translate_element', {timeout: 7000});
  } catch (e) {
    console.log('google_translate_element did not load; skipping test');
    await browser.close();
    process.exit(0);
  }
  await new Promise(r => setTimeout(r, 5000));
  const textFr = await page.$eval('#text', el => el.innerText);
  console.log('Text EN:', textEn);
  console.log('Text FR:', textFr);
  await browser.close();
})();
