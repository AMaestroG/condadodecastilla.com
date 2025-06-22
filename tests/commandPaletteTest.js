const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1024, height: 800 });
  await page.goto('http://localhost:8080/index.php');
  await page.keyboard.down('Control');
  await page.keyboard.press('k');
  await page.keyboard.up('Control');
  await page.waitForSelector('#commandPaletteOverlay.is-open', { timeout: 3000 });
  const isOpen = await page.evaluate(() => document.querySelector('#commandPaletteOverlay').classList.contains('is-open'));
  if (!isOpen) {
    console.error('Command palette did not open');
    await browser.close();
    process.exit(1);
  }
  console.log('Command palette opens with Ctrl+K');
  await browser.close();
})();
