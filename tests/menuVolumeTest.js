const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/tests/manual/test_audio_volume.html');
  await page.waitForSelector('#consolidated-menu-button');
  await page.evaluate(() => {
    const a = document.getElementById('test-audio');
    a.volume = 1;
    a.muted = false;
  });
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const volAfterOpen = await page.$eval('#test-audio', el => el.volume);
  if (volAfterOpen >= 1) {
    console.error('Volume not reduced on menu open');
    await browser.close();
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const volAfterClose = await page.$eval('#test-audio', el => el.volume);
  if (Math.abs(volAfterClose - 1) > 0.01) {
    console.error('Volume not restored on menu close');
    await browser.close();
    process.exit(1);
  }
  console.log('Audio volume lowered and restored correctly');
  await browser.close();
})();
