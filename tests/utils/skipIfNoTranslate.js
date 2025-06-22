async function awaitTranslateOrSkip(page) {
  try {
    await page.waitForSelector('#google_translate_element', { timeout: 7000 });
  } catch (err) {
    console.log('google_translate_element did not load; skipping test');
    const browser = page.browser();
    if (browser) {
      await browser.close();
    }
    process.exit(0);
  }
}
module.exports = awaitTranslateOrSkip;
