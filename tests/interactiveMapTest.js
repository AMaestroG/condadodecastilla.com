const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/lugares/mapa_interactivo.php');
  await page.waitForSelector('#interactive-map');
  await page.waitForFunction(() => window.L && document.querySelectorAll('#interactive-map .leaflet-marker-icon').length > 0, { timeout: 10000 });
  const count = await page.evaluate(() => document.querySelectorAll('#interactive-map .leaflet-marker-icon').length);
  if (count === 0) {
    console.error('No markers found on interactive map');
    await browser.close();
    process.exit(1);
  }
  console.log('Interactive map loaded with markers');
  await browser.close();
})();
