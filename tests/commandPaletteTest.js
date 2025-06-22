const puppeteer = require('puppeteer');
const path = require('path');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1024, height: 800 });
  const filePath = path.resolve(__dirname, '../index.html');
  await page.goto('file://' + filePath);
  await page.waitForSelector('#commandPaletteOverlay');

  await page.keyboard.down('Control');
  await page.keyboard.press('k');
  await page.keyboard.up('Control');
  await page.waitForTimeout(300);

  const openClass = await page.$eval('#commandPaletteOverlay', el => el.classList.contains('is-open'));
  const ariaOpen = await page.$eval('#commandPaletteOverlay', el => el.getAttribute('aria-hidden') === 'false');
  if (!openClass || !ariaOpen) {
    console.error('Command palette did not open with Ctrl+K');
    await browser.close();
    process.exit(1);
  }

  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  const closedClass = await page.$eval('#commandPaletteOverlay', el => !el.classList.contains('is-open'));
  const ariaClosed = await page.$eval('#commandPaletteOverlay', el => el.getAttribute('aria-hidden') === 'true');
  if (!closedClass || !ariaClosed) {
    console.error('Command palette did not close with Escape');
    await browser.close();
    process.exit(1);
  }

  console.log('Command palette toggles correctly with keyboard');
  await browser.close();
})();
