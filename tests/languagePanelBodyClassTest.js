const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_language_panel.html');
  await page.waitForSelector('#flag-toggle');
  await page.evaluate(() => {
    document.getElementById('language-panel').classList.add('active');
    document.body.classList.add('menu-open-right');
  });
  await new Promise(r => setTimeout(r, 300));
  const hasClass = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (!hasClass) {
    console.error('menu-open-right not added');
    await browser.close();
    process.exit(1);
  }
  await page.evaluate(() => {
    document.getElementById('language-panel').classList.remove('active');
    document.body.classList.remove('menu-open-right');
  });
  await new Promise(r => setTimeout(r, 300));
  const stillHas = await page.evaluate(() => document.body.classList.contains('menu-open-right'));
  if (stillHas) {
    console.error('menu-open-right not removed');
    await browser.close();
    process.exit(1);
  }
  console.log('Language panel body class toggled correctly');
  await browser.close();
})();
