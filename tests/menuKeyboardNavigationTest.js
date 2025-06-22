const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.focus('#consolidated-menu-button');
  await page.keyboard.press('Enter');
  await page.waitForFunction(() => {
    const menu = document.getElementById('consolidated-menu-items');
    const btn = document.getElementById('consolidated-menu-button');
    return menu.classList.contains('active') &&
      menu.getAttribute('aria-hidden') === 'false' &&
      btn.getAttribute('aria-expanded') === 'true' &&
      menu.contains(document.activeElement);
  });
  const open = await page.$eval('#consolidated-menu-items', el => el.classList.contains('active'));
  const ariaOpen = await page.$eval('#consolidated-menu-items', el => el.getAttribute('aria-hidden') === 'false');
  const btnExpanded = await page.$eval('#consolidated-menu-button', el => el.getAttribute('aria-expanded') === 'true');
  const focusedInside = await page.evaluate(() => {
    const menu = document.getElementById('consolidated-menu-items');
    return menu.contains(document.activeElement);
  });
  if (!open || !ariaOpen || !btnExpanded || !focusedInside) {
    console.error('Menu did not open via keyboard');
    await browser.close();
    process.exit(1);
  }
  await page.keyboard.press('Escape');
  await page.waitForFunction(() => {
    const menu = document.getElementById('consolidated-menu-items');
    const btn = document.getElementById('consolidated-menu-button');
    return !menu.classList.contains('active') &&
      menu.getAttribute('aria-hidden') === 'true' &&
      btn.getAttribute('aria-expanded') === 'false';
  });
  const closed = await page.$eval('#consolidated-menu-items', el => !el.classList.contains('active'));
  const ariaClosed = await page.$eval('#consolidated-menu-items', el => el.getAttribute('aria-hidden') === 'true');
  const btnCollapsed = await page.$eval('#consolidated-menu-button', el => el.getAttribute('aria-expanded') === 'false');
  if (!closed || !ariaClosed || !btnCollapsed) {
    console.error('Menu did not close via Escape');
    await browser.close();
    process.exit(1);
  }
  console.log('Keyboard navigation for menu works');
  await browser.close();
})();
