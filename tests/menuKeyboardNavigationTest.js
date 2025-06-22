const { launchBrowser, closeBrowser } = require('./helpers/puppeteerSetup');

(async () => {
  const browser = await launchBrowser();
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('#consolidated-menu-button');
  await page.focus('#consolidated-menu-button');
  await page.keyboard.press('Enter');
  await page.waitForTimeout(300);
  const open = await page.$eval('#consolidated-menu-items', el => el.classList.contains('active'));
  const ariaOpen = await page.$eval('#consolidated-menu-items', el => el.getAttribute('aria-hidden') === 'false');
  const btnExpanded = await page.$eval('#consolidated-menu-button', el => el.getAttribute('aria-expanded') === 'true');
  const focusedInside = await page.evaluate(() => {
    const menu = document.getElementById('consolidated-menu-items');
    return menu.contains(document.activeElement);
  });
  if (!open || !ariaOpen || !btnExpanded || !focusedInside) {
    console.error('Menu did not open via keyboard');
    await closeBrowser(browser);
    process.exit(1);
  }
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  const closed = await page.$eval('#consolidated-menu-items', el => !el.classList.contains('active'));
  const ariaClosed = await page.$eval('#consolidated-menu-items', el => el.getAttribute('aria-hidden') === 'true');
  const btnCollapsed = await page.$eval('#consolidated-menu-button', el => el.getAttribute('aria-expanded') === 'false');
  if (!closed || !ariaClosed || !btnCollapsed) {
    console.error('Menu did not close via Escape');
    await closeBrowser(browser);
    process.exit(1);
  }
  console.log('Keyboard navigation for menu works');
  await closeBrowser(browser);
})();
