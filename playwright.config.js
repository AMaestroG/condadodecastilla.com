const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  webServer: {
    command: 'php -S localhost:8080',
    port: 8080,
    reuseExistingServer: true,
  },
  use: {
    baseURL: 'http://localhost:8080',
  },
});
