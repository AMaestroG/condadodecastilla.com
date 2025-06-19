const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new', args: ['--no-sandbox', '--autoplay-policy=no-user-gesture-required']});
  const page = await browser.newPage();
  await page.goto('http://localhost:8080/index.php');
  await page.evaluate(() => {
    const audio = document.createElement('audio');
    audio.id = 'test-audio';
    audio.src = 'https://samplelib.com/lib/preview/mp3/sample-3s.mp3';
    audio.autoplay = true;
    document.body.appendChild(audio);
  });
  await page.waitForFunction(() => {
    const el = document.getElementById('test-audio');
    return el && !el.paused;
  });
  const initialVolume = await page.$eval('#test-audio', el => el.volume);
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const loweredVolume = await page.$eval('#test-audio', el => el.volume);
  if (loweredVolume >= initialVolume) {
    console.error('volume not lowered');
    await browser.close();
    process.exit(1);
  }
  await page.click('#consolidated-menu-button');
  await page.waitForTimeout(500);
  const restoredVolume = await page.$eval('#test-audio', el => el.volume);
  if (Math.abs(restoredVolume - initialVolume) > 0.01) {
    console.error('volume not restored');
    await browser.close();
    process.exit(1);
  }
  console.log('Audio volume adjusted correctly');
  await browser.close();
})();
