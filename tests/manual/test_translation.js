const puppeteer = require('puppeteer');
(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_lang.html');
  const textBefore = await page.$eval('#text', el => el.innerText);
  await page.click('#translate-btn');
  await page.waitForFunction(() => document.getElementById('text').innerText !== 'Hola Mundo', { timeout: 10000 });
  const textAfter = await page.$eval('#text', el => el.innerText);
  console.log('Text before:', textBefore);
  console.log('Text after:', textAfter);
  await browser.close();
})();
