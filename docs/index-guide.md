# Guía de index.php y el menú deslizante

## Secciones principales de `index.php`
El archivo `index.php` actúa como portada del sitio y se organiza en varias secciones. Asignarles un identificador facilita enlazarlas desde el menú o desde otras páginas.

| Sección (clase)                         | Contenido                                  | ID sugerido |
|-----------------------------------------|---------------------------------------------|-------------|
| `<header class="hero">`                | Escudo, texto editable y botón de historia | `hero`      |
| `<section class="video-section">`      | Vídeo promocional de la zona                | `video`     |
| `<section class="detailed-intro-section">` | Introducción a la memoria de la Hispanidad | `memoria`   |
| `<section class="alternate-bg">`       | Tarjetas "Explora Nuestro Legado"           | `legado`    |
| `<section>` (Personajes)                | Tarjetas de personajes históricos           | `personajes`|
| `<section class="timeline-section-summary">` | Resumen cronológico                         | `timeline`  |
| `<section class="immersion-section">`  | Invitación final a profundizar en la cultura| `cultura`   |

Para usar anclas basta con añadir `id="..."` a cada elemento. Por ejemplo:
```html
<section id="video" class="video-section section spotlight-active">...
```

## Cómo `_header.php` carga los menús
El archivo `_header.php` genera el panel deslizante derecho e inserta las diferentes secciones de menú leyendo los archivos de `fragments/menus/`:
```php
<?php
if (file_exists(__DIR__ . '/fragments/menus/main-menu.php')) {
    include __DIR__ . '/fragments/menus/main-menu.php';
}
?>
```
El panel también incluye `admin-menu.php` y `social-menu.html` dentro de bloques `<div class="menu-section">`.

## Personalización del menú deslizante y nuevas páginas
* **Estilos**: modifica `assets/css/menus/consolidated-menu.css` para cambiar colores morado y oro viejo, anchura u otros efectos del panel `.menu-panel`.
* **Comportamiento**: `assets/js/main.js` gestiona la apertura y cierre con el atributo `data-menu-target`.
* **Añadir páginas**: edita `fragments/menus/main-menu.php` para crear nuevos enlaces y añade el archivo correspondiente en el directorio del proyecto.

Tras cualquier modificación ejecuta las pruebas de PHP y Python si las dependencias están instaladas:
```bash
vendor/bin/phpunit
python -m unittest tests/test_flask_api.py
```

## Contenedor `#fixed-header-elements`
Este bloque fijo aparece al inicio de cada página y mantiene visibles los controles principales.

### Botones de control rápido
Estos son los accesos que incorpora por defecto:
- `#consolidated-menu-button` abre el panel lateral con toda la navegación.
- `#ai-chat-trigger` para iniciar el chat desde el menú.
- `#theme-toggle` alterna entre modo claro y oscuro.
- `#lang-bar-toggle` despliega la barra de traducción de Google.

Al añadir más elementos al contenedor puede ser necesario ajustar la posición de los paneles deslizantes. Para ello define la variable `--menu-extra-offset` con la altura del contenedor y úsala junto a `--language-bar-offset` en `assets/css/menus/consolidated-menu.css`:
```css
:root {
    --menu-extra-offset: 48px;
}
.menu-panel {
    top: calc(var(--language-bar-offset) + var(--menu-extra-offset));
}
```
Esto evitará que los menús se oculten tras los botones fijos.

### Modificar la altura del contenedor

La variable `--menu-extra-offset` puede definirse también en `assets/css/epic_theme.css` o en tu propia hoja de estilos. Ajusta su valor al número de píxeles que ocupa `#fixed-header-elements` para que los paneles deslizantes queden perfectamente alineados bajo los botones.
