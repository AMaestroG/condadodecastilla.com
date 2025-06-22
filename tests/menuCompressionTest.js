const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.click('#consolidated-menu-button');
  await page.waitForFunction(() => document.body.classList.contains('menu-compressed'));
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (!hasClass) {
    console.error('menu-compressed class not added');
    await browser.close();
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForFunction(() => !document.body.classList.contains('menu-compressed'));
  const stillHasClass = await page.evaluate(() => document.body.classList.contains('menu-compressed'));
  if (stillHasClass) {
    console.error('menu-compressed class not removed');
    await browser.close();
    process.exit(1);
  }
  console.log('Menu compression class toggled correctly');
  await browser.close();
})();
