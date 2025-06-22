// tests/tailwindMobileMenuTest.js (Reescrito)
const puppeteer = require('puppeteer');
const { exec } = require('child_process');
const path = require('path');

const APP_URL = 'http://localhost:8080/tailwind_index.php'; // Target tailwind_index.php
const DELAY_MS = 600;
let phpServerProcess;

// Simple expect
function expect(actual, message) {
    return {
        toBe: (expected) => {
            if (actual !== expected) {
                const errorMsg = `TAILWIND_TEST_FALLO: ${message}. Se esperaba ${expected}, se obtuvo ${actual}`;
                console.error(errorMsg);
                throw new Error(errorMsg);
            }
        }
    };
}

async function runTailwindTest() {
    let browser;
    console.log("TAILWIND_TEST_INFO: Iniciando servidor PHP para prueba de tailwind_index.php...");
    const projectRoot = path.resolve(__dirname, '..');
    phpServerProcess = exec(`php -S localhost:8080 -t ${projectRoot}`);
    phpServerProcess.stderr.on('data', (data) => {
        const errorString = data.toString();
        // Ignorar mensajes comunes de inicio o cierre del servidor PHP que no son errores de la app.
        if (!errorString.includes('Development Server') &&
            !errorString.includes('Failed to listen') &&
            !errorString.includes('Closing')) {
            // console.error(`TAILWIND_PHP_STDERR: ${errorString}`);
        }
    });
    await new Promise(resolve => setTimeout(resolve, 2500));

    try {
        console.log("TAILWIND_TEST_INFO: Lanzando navegador...");
        browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'] });
        const page = await browser.newPage();

        page.on('console', msg => {
            const type = msg.type().toUpperCase();
            const text = msg.text();
            if (type === 'ERROR' && !(text.includes('favicon.ico') || text.includes('manifest.json') || text.includes('logo.svg'))) {
                // console.error(`TAILWIND_PAGE_CONSOLE_${type}: ${text}`);
            }
        });

        const delay = ms => new Promise(res => setTimeout(res, ms));
        const hasClass = (sel, cls) => page.evaluate((s, c) => document.querySelector(s)?.classList.contains(c) || false, sel, cls);

        console.log("TAILWIND_TEST_INFO: Iniciando prueba en viewport móvil (375x800) para tailwind_index.php");
        await page.setViewport({ width: 375, height: 800 });

        console.log(`TAILWIND_TEST_INFO: Navegando a ${APP_URL}...`);
        try {
            await page.goto(APP_URL, { waitUntil: 'networkidle0', timeout: 20000 });
        } catch (e) {
            console.error(`TAILWIND_TEST_ERROR: No se pudo cargar ${APP_URL}. Detalle: ${e.message}`);
            throw e;
        }

        console.log("TAILWIND_TEST: Sidebar (#sidebar) debe abrir con #consolidated-menu-button en tailwind_index.php");

        const menuToggleExists = await page.evaluate(() => document.getElementById('menu-toggle') !== null);
        // El script local que usaba #menu-toggle fue eliminado de tailwind_index.php, así que este ID no debería existir.
        expect(menuToggleExists, "#menu-toggle (del script local anterior) no debería existir").toBe(false);

        // Probar el menú global
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar visible al inicio en tailwind_index.php").toBe(false);

        const globalMenuButtonExists = await page.evaluate(() => document.getElementById('consolidated-menu-button') !== null);
        expect(globalMenuButtonExists, "#consolidated-menu-button no encontrado en tailwind_index.php").toBe(true);

        await page.click('#consolidated-menu-button');
        await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no visible tras click en tailwind_index.php").toBe(true);

        await page.click('#consolidated-menu-button');
        await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no oculto tras 2do click en tailwind_index.php").toBe(false);

        console.log("TAILWIND_TEST_SUCCESS: Prueba de menú global en tailwind_index.php completada.");

    } catch (error) {
        console.error("\nTAILWIND_TEST_ERROR: Falló la prueba para tailwind_index.php.");
        // error.message ya se imprime por el throw en expect()
        process.exitCode = 1;
    } finally {
        if (browser) await browser.close();
        if (phpServerProcess) {
            console.log("TAILWIND_TEST_INFO: Deteniendo servidor PHP...");
            const killed = phpServerProcess.kill();
            if (!killed) {
                // console.warn("TAILWIND_TEST_WARN: No se pudo detener el servidor PHP automáticamente.");
            }
        }
    }
}

runTailwindTest();
