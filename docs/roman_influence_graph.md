# Roman Influence Graph

Este documento explica la gráfica interactiva implementada en `historia/influencia_romana.php`.

La visualización usa **D3.js** para mostrar la evolución histórica de la huella romana en la región de Cerezo de Río Tirón. Los datos se encuentran en `assets/js/influencia_romana.js` y se representan como una línea con degradado morado‑oro y puntos resaltados.

## Características

- **Escalado Responsivo**: el SVG utiliza `viewBox` para adaptarse a móviles y sobremesa.
- **Modo oscuro**: los colores de ejes y herramienta se ajustan mediante media queries a `prefers-color-scheme`.
- **Tooltip**: al situar el cursor sobre cada punto se muestra una nota descriptiva.
- **Gradientes y filtros**: se definen en SVG para aplicar brillo y degradado siguiendo la paleta de morado y oro viejo.

Para ampliar la gráfica modifica el array `data` o los estilos en `influencia_romana.js`. La página se integra en el sitio mediante `fragments/header.php` y carga automática de scripts con `layout.js`.
