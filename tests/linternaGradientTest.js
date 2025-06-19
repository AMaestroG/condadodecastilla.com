const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/galeria/galeria_colaborativa.php');
  await page.waitForSelector('#linterna-condado');
  const hasClass = await page.$eval('#linterna-condado', el => el.classList.contains('bg-linterna-gradient'));
  if (!hasClass) {
    console.error('bg-linterna-gradient class not found');
    await browser.close();
    process.exit(1);
  }
  console.log('Linterna gradient class present');
  await browser.close();
})();
