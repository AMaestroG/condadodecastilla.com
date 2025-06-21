const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#mute-toggle');
  const initial = await page.$eval('#mute-toggle', el => el.getAttribute('aria-pressed'));
  await page.click('#mute-toggle');
  await page.waitForTimeout(300);
  const afterClick = await page.$eval('#mute-toggle', el => el.getAttribute('aria-pressed'));
  if (initial === afterClick) {
    console.error('aria-pressed did not toggle');
    await browser.close();
    process.exit(1);
  }
  console.log('Mute button toggled correctly');
  await browser.close();
})();
