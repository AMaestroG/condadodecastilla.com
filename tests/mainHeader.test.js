// tests/mainHeader.test.js
const puppeteer = require('puppeteer');
const { exec } = require('child_process');
const path = require('path');

const APP_URL = 'http://localhost:8080/index.php';
const DELAY_MS = 600; // Aumentado ligeramente para asegurar transiciones

let phpServerProcess;

// Simple expect para pruebas standalone
function expect(actual, message) {
    return {
        toBe: (expected) => {
            if (actual !== expected) {
                const errorMsg = `FALLO: ${message}. Se esperaba ${expected}, se obtuvo ${actual}`;
                console.error(errorMsg);
                throw new Error(errorMsg);
            }
            // console.log(`PASO: ${message}`); // Comentado para reducir verbosidad
        }
    };
}

async function runTests() {
    let browser;
    console.log("INFO: Iniciando servidor PHP...");
    const projectRoot = path.resolve(__dirname, '..');
    // Asegurarse que el servidor no genere output excesivo que pueda ser malinterpretado como error.
    phpServerProcess = exec(`php -S localhost:8080 -t ${projectRoot}`);
    phpServerProcess.stderr.on('data', (data) => {
        // Filtrar mensajes comunes de desarrollo que no son errores fatales del servidor.
        if (!data.toString().includes('Development Server') && !data.toString().includes('Failed to listen')) {
            console.error(`PHP_SERVER_STDERR: ${data}`);
        }
    });
    await new Promise(resolve => setTimeout(resolve, 2500)); // Dar tiempo al servidor para iniciar

    try {
        console.log("INFO: Lanzando navegador...");
        browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'] });
        const page = await browser.newPage();

        page.on('console', msg => {
            const type = msg.type().toUpperCase();
            const text = msg.text();
            // Filtrar errores no críticos
            if (type === 'ERROR' && !(text.includes('favicon.ico') || text.includes('manifest.json') || text.includes('CSRF') || text.includes('logo.svg'))) {
                // console.error(`PAGE_CONSOLE_${type}: ${text}`);
            }
        });
         page.on('pageerror', error => {
            console.error(`PAGE_ERROR: ${error.message}`);
         });

        const delay = ms => new Promise(res => setTimeout(res, ms));
        const hasClass = (sel, cls) => page.evaluate((s, c) => document.querySelector(s)?.classList.contains(c) || false, sel, cls);
        const getAttribute = (sel, attr) => page.evaluate((s, a) => document.querySelector(s)?.getAttribute(a), sel, attr);
        const isElementVisible = async (sel) => { // Chequea visibilidad por computed style
            return await page.evaluate(selector => {
                const elem = document.querySelector(selector);
                if (!elem) return false;
                const style = window.getComputedStyle(elem);
                return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
            }, sel);
        };


        // --- Mobile Tests ---
        console.log("\n--- INFO: Iniciando Pruebas Móviles (375x800) ---");
        await page.setViewport({ width: 375, height: 800 });
        console.log("INFO: Navegando a la página...");
        try {
            await page.goto(APP_URL, { waitUntil: 'networkidle0', timeout: 20000 });
        } catch (e) {
            console.error(`ERROR: No se pudo cargar la página ${APP_URL}. Asegúrese que el servidor PHP esté corriendo en localhost:8080. Detalle: ${e.message}`);
            throw e;
        }


        console.log("TEST: Sidebar (#sidebar) debe abrir y cerrar con #consolidated-menu-button");
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar visible al inicio").toBe(false);
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no visible tras 1er click").toBe(true);
        expect(await getAttribute('#sidebar', 'aria-hidden'), "aria-hidden de sidebar incorrecto tras 1er click").toBe('false');
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel consolidado activo en móvil").toBe(false);
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no oculto tras 2do click").toBe(false);

        console.log("TEST: Sidebar debe cerrar con botón interno #close-sidebar-button");
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS); // Abrir
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no abierto para prueba de cierre interno").toBe(true);
        await page.click('#close-sidebar-button'); await delay(DELAY_MS); // Cerrar
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no oculto por botón interno").toBe(false);

        console.log("TEST: Sidebar debe cerrar con tecla Escape");
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS); // Abrir
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no abierto para prueba de Escape").toBe(true);
        await page.keyboard.press('Escape'); await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no oculto por Escape").toBe(false);

        console.log("TEST: Sidebar debe cerrar al hacer click fuera (en body)");
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS); // Abrir
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no abierto para prueba de click fuera").toBe(true);
        await page.click('body'); await delay(DELAY_MS); // Click fuera
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no oculto por click fuera").toBe(false);
        console.log("--- INFO: Pruebas Móviles Completadas ---");

        // --- Desktop Tests ---
        console.log("\n--- INFO: Iniciando Pruebas de Escritorio (1200x800) ---");
        await page.setViewport({ width: 1200, height: 800 });
        await page.goto(APP_URL, { waitUntil: 'networkidle0', timeout: 15000 });

        console.log("TEST: Panel consolidado (#consolidated-menu-items) debe abrir y cerrar con #consolidated-menu-button");
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. activo al inicio (desktop)").toBe(false);
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no activo tras 1er click (desktop)").toBe(true);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar visible en desktop").toBe(false);
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no oculto tras 2do click (desktop)").toBe(false);

        console.log("TEST: Panel AI Chat (#ai-chat-panel) debe abrirse y cerrarse");
        const aiChatTriggerSel = '#ai-chat-trigger';
        await page.waitForSelector(aiChatTriggerSel, { visible: true });
        expect(await isElementVisible(aiChatTriggerSel), "Botón AI Chat no visible").toBe(true);
        await page.click(aiChatTriggerSel); await delay(DELAY_MS);
        expect(await hasClass('#ai-chat-panel', 'active'), "Panel AI no activo").toBe(true);
        expect(await hasClass('body', 'menu-open-left'), "Body class menu-open-left no aplicado").toBe(true);
        await page.click('#ai-chat-panel #close-ai-drawer'); await delay(DELAY_MS);
        expect(await hasClass('#ai-chat-panel', 'active'), "Panel AI no cerrado por botón interno").toBe(false);

        console.log("TEST: Panel Idioma (#language-panel) debe abrir con #flag-toggle y cerrar");
        expect(await hasClass('#language-panel', 'active'), "Panel idioma activo al inicio").toBe(false);
        await page.click('#flag-toggle'); await delay(DELAY_MS);
        expect(await hasClass('#language-panel', 'active'), "Panel idioma no activo tras click").toBe(true);
        expect(await hasClass('body', 'menu-open-top'), "Body class toggled for language panel").toBe(true);
        await page.click('body'); await delay(DELAY_MS); // Click fuera
        expect(await hasClass('#language-panel', 'active'), "Panel idioma no cerrado por click fuera").toBe(false);

        console.log("TEST: Menús de escritorio deben cerrar con tecla Escape");
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS); // Abrir panel consol.
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no abierto para prueba Escape").toBe(true);
        await page.keyboard.press('Escape'); await delay(DELAY_MS);
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no cerrado por Escape").toBe(false);

        await page.click('#flag-toggle'); await delay(DELAY_MS); // Abrir panel idioma
        expect(await hasClass('#language-panel', 'active'), "Panel idioma no abierto para prueba Escape").toBe(true);
        await page.keyboard.press('Escape'); await delay(DELAY_MS);
        expect(await hasClass('#language-panel', 'active'), "Panel idioma no cerrado por Escape").toBe(false);
        console.log("--- INFO: Pruebas de Escritorio Completadas ---");

        // --- Resize Tests ---
        console.log("\n--- INFO: Iniciando Pruebas de Redimensionamiento ---");
        console.log("TEST: Sidebar (abierto en móvil) debe cerrar al redimensionar a escritorio");
        await page.setViewport({ width: 375, height: 800 });
        await page.goto(APP_URL, { waitUntil: 'networkidle0', timeout: 15000 });
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no abierto para prueba resize (móvil)").toBe(true);
        await page.setViewport({ width: 1200, height: 800 }); await delay(DELAY_MS * 2);
        expect(await hasClass('#sidebar', 'sidebar-visible'), "Sidebar no cerrado tras resize a desktop").toBe(false);

        console.log("TEST: Panel consolidado (abierto en escritorio) debe cerrar al redimensionar a móvil");
        await page.goto(APP_URL, { waitUntil: 'networkidle0', timeout: 15000 }); // Recargar en desktop
        await page.click('#consolidated-menu-button'); await delay(DELAY_MS);
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no abierto para prueba resize (desktop)").toBe(true);
        await page.setViewport({ width: 375, height: 800 }); await delay(DELAY_MS * 2);
        expect(await hasClass('#consolidated-menu-items', 'active'), "Panel cons. no cerrado tras resize a móvil").toBe(false);
        console.log("--- INFO: Pruebas de Redimensionamiento Completadas ---");

        console.log("\nSUCCESS: TODAS LAS PRUEBAS DEL HEADER PRINCIPAL COMPLETADAS CON ÉXITO!");

    } catch (error) {
        console.error("\nERROR: Al menos una prueba falló o ocurrió un error irrecuperable.");
        console.error(error.message); // Muestra el mensaje de la aserción fallida
        process.exitCode = 1;
    } finally {
        if (browser) await browser.close();
        if (phpServerProcess) {
            console.log("INFO: Deteniendo servidor PHP...");
            const killed = phpServerProcess.kill();
            if (!killed) {
                console.warn("WARN: No se pudo detener el servidor PHP automáticamente. Por favor, deténgalo manualmente si es necesario.");
            }
        }
    }
}

runTests();
