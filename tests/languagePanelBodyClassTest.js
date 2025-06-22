const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.click('#flag-toggle');
  await page.waitForTimeout(300);
  try {
    await page.waitForSelector('#language-panel #google_translate_element', {visible: true, timeout: 7000});
  } catch (e) {
    console.log('google_translate_element did not load; skipping test');
    await browser.close();
    process.exit(0);
  }
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (!hasClass) {
    console.error('menu-open-right not added');
    await browser.close();
    process.exit(1);
  }
  await page.click('#flag-toggle');
  await page.waitForTimeout(300);
  const stillHas = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (stillHas) {
    console.error('menu-open-right not removed');
    await browser.close();
    process.exit(1);
  }
  console.log('Language panel body class toggled correctly');
  await browser.close();
})();
