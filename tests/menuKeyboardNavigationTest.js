const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.focus('#consolidated-menu-button');
  await page.keyboard.press('Enter');
  await page.waitForTimeout(300);
  const open = await page.$eval('#consolidated-menu-items', el => el.classList.contains('active'));
  const focusedInside = await page.evaluate(() => {
    const menu = document.getElementById('consolidated-menu-items');
    return menu.contains(document.activeElement);
  });
  if (!open || !focusedInside) {
    console.error('Menu did not open via keyboard');
    await browser.close();
    process.exit(1);
  }
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  const closed = await page.$eval('#consolidated-menu-items', el => !el.classList.contains('active'));
  if (!closed) {
    console.error('Menu did not close via Escape');
    await browser.close();
    process.exit(1);
  }
  console.log('Keyboard navigation for menu works');
  await browser.close();
})();
