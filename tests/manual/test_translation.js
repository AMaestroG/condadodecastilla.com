const puppeteer = require('puppeteer');
(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html?lang=en');
  await page.waitForSelector('#google_translate_element');
  await new Promise(r => setTimeout(r, 4000)); // wait for API load
  const textBefore = await page.$eval('#text', el => el.innerText);
  const flags = await page.$$('.lang-flag');
  await flags[0].click();
  await new Promise(r => setTimeout(r, 5000));
  const textAfter = await page.$eval('#text', el => el.innerText);
  console.log('Text before:', textBefore);
  console.log('Text after:', textAfter);
  await browser.close();
})();
