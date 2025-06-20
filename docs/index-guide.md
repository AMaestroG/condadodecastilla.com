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

## Cómo `fragments/header.php` carga los menús
El archivo `fragments/header.php` genera el panel deslizante derecho e inserta las diferentes secciones de menú leyendo los archivos de `fragments/menus/`:
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

### Clase `menu-compressed` y transformación de la página

Al pulsar un botón con el atributo `data-menu-target="id-del-panel"`,
`assets/js/main.js` abre el panel de menú indicado y añade la clase
`menu-compressed` al elemento `<body>`. También se aplica
`menu-open-left` o `menu-open-right` según el lado del que se despliegue
el panel. Estas clases están definidas en
`assets/css/sliding_menu.css`:

```css
body.menu-compressed {
    transition: transform 0.3s ease;
    transform: scaleX(0.95);
}
body.menu-open-left {
    transform: translateX(260px) scaleX(0.95);
}
body.menu-open-right {
    transform: translateX(-260px) scaleX(0.95);
}
```

El contenido de la página se desplaza y se escala horizontalmente,
comprimiéndose hacia el lado opuesto al menú abierto. Al cerrar todos
los paneles, el script elimina estas clases y la vista vuelve a su
posición original.

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

## Gráfica de Influencia Romana

El índice incorpora una gráfica interactiva que muestra la huella de Roma en
la región. Esta visualización se implementa con **D3.js** y cambia de paleta de
colores automáticamente cuando el usuario activa el modo oscuro mediante el
botón `#theme-toggle`.


## Configuración de los agentes del foro

El archivo `config/forum_agents.php` define los expertos que responden en el foro. Cada entrada del array contiene estos campos:

```php
return [
    'historian' => [
        'name' => 'Alicia la Historiadora',
        'bio' => 'Con años de investigación tras ella, Alicia relata... ',
        'expertise' => 'Historia medieval y orígenes de Castilla'
    ],
    // ...
];
```

Edita sus valores o añade nuevas claves para ampliar el listado de agentes.

## Cajón IA y acciones personalizables

El panel de chat con inteligencia artificial se carga desde
`fragments/header/ai-drawer.html` dentro del contenedor
`<div id="ai-chat-panel" class="menu-panel right-panel">`.
Su estructura principal es la siguiente:

- `<div id="ai-drawer" class="ai-drawer">` envuelve todo el contenido.
- `<div class="ai-drawer-header">` muestra el título `#ai-chat-title` y
  el botón `#close-ai-drawer`.
- `<div id="ia-tools-menu">` agrupa los botones de acción
  (`#ia-summary-btn`, `#ia-translate-btn`, `#ia-research-btn`,
  `#ia-websearch-btn`).
- `<div id="gemini-chat-area">` despliega los mensajes de la conversación.
- `<div id="gemini-chat-input-container">` contiene el campo de texto
  `#gemini-chat-input` y el botón `#gemini-chat-submit`.
- `<dialog id="ai-dialog">` y el área `#ai-response-box` sirven para
  mostrar la respuesta completa.

`assets/js/main.js` controla la apertura y cierre de este panel utilizando
el atributo `data-menu-target="ai-chat-panel"`. Cuando el panel se abre,
el script enfoca `#gemini-chat-area` y permite cerrarlo al pulsar
`#close-ai-drawer`. Las funciones concretas de cada botón se encuentran en
`js/ia-tools.js`.

### Modificar etiquetas o añadir nuevas acciones

1. Abre `fragments/header/ai-drawer.html` y cambia el texto de los botones
   que necesites dentro de `#ia-tools-menu`.
2. Si deseas una acción adicional, duplica un botón con un nuevo `id` y
   escribe su manejador en `js/ia-tools.js` siguiendo el estilo de
   `handleSummary()` o `handleResearch()`.
3. Recarga los archivos JavaScript y CSS tras realizar los cambios para que
   tengan efecto en la web.

