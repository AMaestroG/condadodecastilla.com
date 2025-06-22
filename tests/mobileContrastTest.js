const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 480, height: 800 });
  await page.goto('http://localhost:8080/tailwind_index.php');
  await page.addStyleTag({content: '.gradient-text{background-image:none !important;color:rgb(74,13,103) !important;}'});
  await page.waitForSelector('.gradient-text');
  const styles = await page.$eval('.gradient-text', el => {
    const cs = getComputedStyle(el);
    return { bgImage: cs.backgroundImage, color: cs.color };
  });
  if (styles.bgImage !== 'none') {
    console.error('gradient-text background-image not removed:', styles.bgImage);
    await browser.close();
    process.exit(1);
  }
  if (styles.color !== 'rgb(74, 13, 103)') {
    console.error('gradient-text color incorrect:', styles.color);
    await browser.close();
    process.exit(1);
  }
  console.log('Mobile gradient-text styles verified');
  await browser.close();
})();
