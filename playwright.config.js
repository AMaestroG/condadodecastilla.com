const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  webServer: {
    command: 'php -S localhost:8000 -t .', // Comando para iniciar el servidor PHP en el directorio raíz del proyecto
    url: 'http://localhost:8000',       // URL base de la aplicación. Playwright esperará a que esta URL esté disponible.
    // port: 8000,                      // 'port' es redundante si 'url' está bien definida y el servidor escucha en ese puerto.
                                        // Dejarlo comentado o eliminarlo si 'url' es suficiente.
    reuseExistingServer: !process.env.CI, // Reutilizar el servidor si no es CI
    timeout: 120 * 1000,                // Tiempo de espera para que el servidor inicie
  },
  use: {
    baseURL: 'http://localhost:8000', // Asegurarse que baseURL coincida
  },
  projects: [
    { name: 'chromium', use: { browserName: 'chromium' } },
    { name: 'firefox', use: { browserName: 'firefox' } },
    { name: 'webkit', use: { browserName: 'webkit' } }
  ]
});
