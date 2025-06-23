// @ts-check
const { test, expect } = require('@playwright/test');

test.describe('Nueva Web - Página de Inicio (nuevaweb/static/index.html)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/nuevaweb/static/index.html');
  });

  test('Debe mostrar el título correcto', async ({ page }) => {
    await expect(page).toHaveTitle(/Bienvenido a Cerezo de Río Tirón - Nueva Web/);
  });

  test('Debe cargar el CSS moderno', async ({ page }) => {
    const modernCssLink = page.locator('link[href="assets/css/modern.css"]');
    await expect(modernCssLink).toHaveCount(1);
    // Para verificar que realmente se aplica, podríamos comprobar un estilo específico
    // Por ejemplo, el color de fondo del body
    const body = page.locator('body');
    await expect(body).toHaveCSS('background-color', 'rgb(244, 240, 230)'); // --background-color: #f4f0e6;
  });

  test('El header debe ser visible y contener el logo y la navegación', async ({ page }) => {
    const header = page.locator('header');
    await expect(header).toBeVisible();
    const logo = header.locator('.logo a');
    await expect(logo).toHaveText('Cerezo de Río Tirón');
    await expect(logo).toHaveAttribute('href', '/');

    const navLinks = header.locator('ul#main-menu li a');
    await expect(navLinks.first()).toBeVisible(); // Chequeamos que al menos el primero sea visible en desktop
  });

  test('Los enlaces de navegación principal deben apuntar a las URLs correctas', async ({ page }) => {
    const navLinks = [
      { text: 'Inicio (Antigua)', href: '/index.php' },
      { text: 'Historia', href: '/historia/historia.php' },
      { text: 'Lugares de Interés', href: '/lugares/lugares.php' },
      { text: 'Cultura y Tradiciones', href: '/cultura/cultura.php' },
      { text: 'Galería', href: '/galeria/galeria_colaborativa.php' },
      { text: 'Foro', href: '/foro/index.php' },
      { text: 'Contacto', href: '/contacto/contacto.php' },
    ];

    for (const link of navLinks) {
      const navLink = page.locator(`ul#main-menu li a:has-text("${link.text}")`);
      await expect(navLink).toHaveAttribute('href', link.href);
    }
  });

  test('La sección Hero debe ser visible con título, descripción y CTA', async ({ page }) => {
    const heroSection = page.locator('#hero');
    await expect(heroSection).toBeVisible();
    await expect(heroSection.locator('h1')).toHaveText('Descubre Cerezo de Río Tirón');
    await expect(heroSection.locator('p').first()).toHaveText('Un lugar donde la historia y la naturaleza se encuentran.');
    const ctaButton = heroSection.locator('.cta-button');
    await expect(ctaButton).toHaveText('Explora Nuestra Historia');
    await expect(ctaButton).toHaveAttribute('href', '/historia/historia.php');
  });

  test('La sección de Destacados debe ser visible con 3 items', async ({ page }) => {
    const highlightsSection = page.locator('#highlights');
    await expect(highlightsSection).toBeVisible();
    await expect(highlightsSection.locator('h2')).toHaveText('Destacados');
    const highlightItems = highlightsSection.locator('.highlight-item');
    await expect(highlightItems).toHaveCount(3);

    // Verificar contenido del primer item como ejemplo
    const firstItem = highlightItems.first();
    await expect(firstItem.locator('h3')).toHaveText('Patrimonio Romano');
    await expect(firstItem.locator('a')).toHaveAttribute('href', '/historia/influencia_romana.php');
  });

  test('El footer debe ser visible y contener el copyright', async ({ page }) => {
    const footer = page.locator('footer');
    await expect(footer).toBeVisible();
    await expect(footer.locator('p').first()).toContainText('© 2024 Cerezo de Río Tirón.');
  });

  test('Menú hamburguesa debe funcionar en vista móvil', async ({ page, isMobile }) => {
    if (!isMobile) {
      console.log('Skipping mobile menu test on desktop viewport.');
      return;
    }

    await page.setViewportSize({ width: 375, height: 667 }); // Simular un iPhone
    await page.goto('/nuevaweb/static/index.html'); // Recargar para asegurar que se aplica el viewport

    const menuToggle = page.locator('.menu-toggle');
    const mainMenu = page.locator('ul#main-menu');

    await expect(menuToggle).toBeVisible();
    await expect(mainMenu).not.toBeVisible(); // El menú debe estar oculto inicialmente

    await menuToggle.click();
    await expect(mainMenu).toBeVisible(); // El menú debe mostrarse después del clic

    await menuToggle.click();
    await expect(mainMenu).not.toBeVisible(); // El menú debe ocultarse de nuevo
  });

  test('No debe haber superposición de elementos importantes en vista móvil', async ({ page, isMobile }) => {
    if (!isMobile) {
      console.log('Skipping overlap test on desktop viewport.');
      return;
    }
    await page.setViewportSize({ width: 375, height: 800 }); // Alto suficiente para contenido
    await page.goto('/nuevaweb/static/index.html');

    // Función para verificar si dos elementos se superponen
    async function checkOverlap(selector1, selector2) {
      const box1 = await page.locator(selector1).boundingBox();
      const box2 = await page.locator(selector2).boundingBox();

      if (!box1 || !box2) return false; // Si alguno no existe, no hay superposición

      return !(
        box1.x + box1.width < box2.x ||
        box2.x + box2.width < box1.x ||
        box1.y + box1.height < box2.y ||
        box2.y + box2.height < box1.y
      );
    }

    // Elementos clave a verificar
    const headerSelector = 'header';
    const heroH1Selector = '#hero h1';
    const firstHighlightItemSelector = '.highlight-item >> nth=0';
    const footerSelector = 'footer';

    // Verificar que el h1 del hero no se superponga con el header
    let isOverlapping = await checkOverlap(headerSelector, heroH1Selector);
    expect(isOverlapping, `Header y Hero H1 no deben superponerse`).toBe(false);

    // Verificar que el primer item destacado no se superponga con el H1 del hero (si están cerca)
    // Esta prueba es más situacional, depende del layout
    // isOverlapping = await checkOverlap(heroH1Selector, firstHighlightItemSelector);
    // expect(isOverlapping, `Hero H1 y primer Highlight no deben superponerse`).toBe(false);

    // Podríamos añadir más comprobaciones, por ejemplo, entre los items destacados o con el footer
    // Verificar que el último item destacado no se superponga con el footer
    const lastHighlightItemSelector = '.highlight-item >> nth=-1';
    isOverlapping = await checkOverlap(lastHighlightItemSelector, footerSelector);
     expect(isOverlapping, `Último Highlight y Footer no deben superponerse`).toBe(false);


    // Tomar un screenshot para inspección visual si es necesario
    // await page.screenshot({ path: 'tests/playwright/screenshots/nuevaweb_static_mobile_layout.png' });
  });

  test('Las imágenes deben tener atributos alt (si las hubiera)', async ({ page }) => {
    // Por ahora no hay imágenes directamente en el HTML, pero la sección hero tiene un background.
    // Si se añaden <img> tags, esta prueba sería relevante.
    const images = page.locator('img');
    const count = await images.count();
    if (count > 0) {
      for (let i = 0; i < count; i++) {
        await expect(images.nth(i)).toHaveAttribute('alt', /.+/); // alt no debe estar vacío
      }
    } else {
      test.skip(true, 'No hay etiquetas <img> en la página para probar atributos alt.');
    }
  });

});
