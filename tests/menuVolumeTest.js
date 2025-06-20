const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.waitForSelector('audio, video');
  const initialVolume = await page.$eval('audio, video', el => el.volume);
  if (initialVolume <= 0) {
    console.error('Initial volume not greater than 0');
    await browser.close();
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const loweredVolume = await page.$eval('audio, video', el => el.volume);
  if (loweredVolume >= initialVolume) {
    console.error('Volume did not decrease when menu opened');
    await browser.close();
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const restoredVolume = await page.$eval('audio, video', el => el.volume);
  if (Math.abs(restoredVolume - initialVolume) > 0.01) {
    console.error('Volume did not restore after closing menu');
    await browser.close();
    process.exit(1);
  }
  console.log('Menu volume attenuation works');
  await browser.close();
})();
