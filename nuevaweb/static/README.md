# Web Nueva - Versión HTML Estática Moderna

Esta carpeta contiene una versión renovada de la página de inicio para "Cerezo de Río Tirón".
Se ha optado por un diseño moderno, limpio y responsive, implementado con HTML estático, CSS y una pequeña cantidad de JavaScript para funcionalidades interactivas como el menú móvil.

## Estructura de Archivos

- **`index.html`**: Es el punto de entrada principal de esta nueva versión. Contiene la estructura semántica de la página, incluyendo:
    - Un encabezado con el logo y la navegación principal.
    - Una sección "hero" destacada.
    - Una sección de "puntos de interés" o "destacados".
    - Un pie de página con información de copyright.
    - Los enlaces de navegación apuntan a las secciones correspondientes de la web antigua (PHP).

- **`assets/css/modern.css`**: Hoja de estilos principal que define la apariencia visual de `index.html`. Incluye:
    - Variables CSS para una fácil personalización de colores y fuentes.
    - Estilos para el layout general, header, footer, secciones hero y destacados.
    - Diseño responsive para adaptarse a diferentes tamaños de pantalla, incluyendo un menú hamburguesa para móviles.
    - Estética moderna y equilibrada, utilizando una paleta de colores inspirada en la identidad del proyecto (morados, oro viejo, alabastro).

- **`assets/js/main.js`**: Archivo JavaScript que maneja la interactividad del sitio. Actualmente incluye:
    - Lógica para el funcionamiento del menú hamburguesa en dispositivos móviles (mostrar/ocultar navegación).
    - (Opcional) Funcionalidad de scroll suave para enlaces internos de la página (anclas).

## Pruebas

Se han desarrollado pruebas automatizadas utilizando Playwright, ubicadas en `tests/playwright/nuevaweb_static_index.spec.js`. Estas pruebas verifican:
- La correcta visualización del título y la carga del CSS.
- La presencia y funcionalidad de elementos clave como el header, logo, navegación, sección hero y footer.
- La correctitud de los enlaces de navegación.
- El funcionamiento del menú hamburguesa en simulación de vista móvil.
- La no superposición de elementos importantes en diferentes resoluciones.

## Visualización

Para ver esta página, simplemente abre el archivo `nuevaweb/static/index.html` en un navegador web. No requiere un servidor PHP para su visualización directa, aunque las pruebas de Playwright se ejecutan con un servidor PHP para simular el entorno del proyecto completo.

## Diferencias con la Versión Anterior en esta Carpeta

Esta implementación reemplaza a la anterior (`index.php`, `simple.css`, `menu.js`) que existía en la antigua carpeta `webnueva/`. Los objetivos de esta nueva versión son:
- Modernizar la estética.
- Asegurar un diseño responsive robusto.
- Utilizar HTML estático para la página de aterrizaje principal, simplificando su mantenimiento y despliegue, mientras se mantienen los enlaces a la funcionalidad PHP existente del sitio principal.
