const puppeteer = require('puppeteer');

function launchBrowser() {
  return puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
}

function closeBrowser(browser) {
  return browser ? browser.close() : Promise.resolve();
}

module.exports = { launchBrowser, closeBrowser };
