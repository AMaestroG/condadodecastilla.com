const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

function getPhpRoutes() {
  const xml = fs.readFileSync(path.join(__dirname, '../sitemap.xml'), 'utf8');
  const regex = /<loc>https?:\/\/[^<]+(\/[^<]+\.php)<\/loc>/g;
  const routes = [];
  let match;
  while ((match = regex.exec(xml)) !== null) {
    routes.push(match[1]);
  }
  return routes;
}

const routes = getPhpRoutes();

test.describe('PHP routes return 200', () => {
  for (const route of routes) {
    test(`GET ${route}`, async ({ page }) => {
      const response = await page.goto(route);
      expect(response.status()).toBe(200);
    });
  }
});
