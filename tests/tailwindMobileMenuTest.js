const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  for (const width of [1200, 375]) {
    const page = await browser.newPage();
    await page.setViewport({ width, height: 800 });
    await page.goto('http://localhost:8080/tailwind_index.php');
    await page.waitForSelector('#menu-toggle');
    await page.evaluate(() => document.getElementById('menu-toggle').click());
    await page.waitForFunction(() => document.body.classList.contains('menu-open-left'));
    const hasClass = await page.evaluate(() => document.body.classList.contains('menu-open-left'));
    if (!hasClass) {
      console.error(`menu-open-left not added for viewport ${width}`);
      await browser.close();
      process.exit(1);
    }
    await page.close();
  }
  console.log('Tailwind mobile menu adds menu-open-left correctly');
  await browser.close();
})();
