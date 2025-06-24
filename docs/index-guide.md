# Guía de index.php y el menú deslizante

## Secciones principales de `index.php`

El archivo `index.php` actúa como portada del sitio y se organiza en varias secciones. Asignarles un identificador facilita enlazarlas desde el menú o desde otras páginas.

| Sección (clase)                              | Contenido                                    | ID sugerido  |
| -------------------------------------------- | -------------------------------------------- | ------------ |
| `<header class="hero">`                      | Escudo, texto editable y botón de historia   | `hero`       |
| `<section class="video-section">`            | Vídeo promocional de la zona                 | `video`      |
| `<section class="detailed-intro-section">`   | Introducción a la memoria de la Hispanidad   | `memoria`    |
| `<section class="alternate-bg">`             | Tarjetas "Explora Nuestro Legado"            | `legado`     |
| `<section>` (Personajes)                     | Tarjetas de personajes históricos            | `personajes` |
| `<section class="timeline-section-summary">` | Resumen cronológico                          | `timeline`   |
| `<section class="immersion-section">`        | Invitación final a profundizar en la cultura | `cultura`    |

Para usar anclas basta con añadir `id="..."` a cada elemento. Por ejemplo:

```html
<section id="video" class="video-section section spotlight-active">...</section>
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

- **Estilos**: modifica `assets/css/menus/consolidated-menu.css` para cambiar colores morado y oro viejo, anchura u otros efectos del panel `.menu-panel`.
- **Comportamiento**: `assets/js/sliding-menu.js` gestiona la apertura y cierre con el atributo `data-menu-target`.
- **Añadir páginas**: edita `fragments/menus/main-menu.php` para crear nuevos enlaces y añade el archivo correspondiente en el directorio del proyecto.

### Apertura del panel y superposición

Al pulsar un botón con `data-menu-target="id-del-panel"`,
`assets/js/sliding-menu.js` abre el panel correspondiente y añade al elemento
`<body>` la clase `menu-open-left` o `menu-open-right`, según el
lateral del menú. Estas clases aplican un `transform` que desplaza el
contenido de la página para dejar espacio al panel, generando un efecto
de deslizamiento fluido. El panel `.menu-panel` mantiene la clase
`active` para mostrarse u ocultarse.

`assets/js/sliding-menu.js` sigue actualizando los atributos `aria-expanded` y
`aria-hidden` de los botones y paneles para mantener la accesibilidad.

Tras cualquier modificacion consulta la [Guia de Testing](testing.md) para ejecutar las pruebas.

## Contenedor `#fixed-header-elements`

Este bloque fijo aparece al inicio de cada página y mantiene visibles los controles principales.

### Botones de control rápido

Estos son los accesos que incorpora por defecto:

- `#consolidated-menu-button` abre el panel lateral con toda la navegación.
- `#open-unified-panel-button` despliega el nuevo panel IA y la barra de idiomas.
- `#ai-chat-trigger` abre directamente el antiguo cajón IA.
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

El valor de `--language-bar-offset` se actualiza automáticamente cuando el
cuerpo tiene la clase `lang-bar-visible`. Por defecto equivale a `0px`.

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
`name`, `bio`, `expertise`, `avatar` y `role_icon`.

```php
return [
    'historian' => [
        'name' => 'Alicia la Historiadora',
        'bio' => 'Con años de investigación tras ella, Alicia relata... ',
        'expertise' => 'Historia medieval y orígenes de Castilla',
        'avatar' => 'https://placehold.co/1024x1536.webp?text=Gonzalo+Tellez',
        'role_icon' => 'fas fa-scroll'
    ],
    // ...
];
```

Edita sus valores o añade nuevas claves para ampliar el listado de agentes.

Los agentes deben respaldar la misión descrita en `docs/README.md`:
_promocionar el turismo en Cerezo de Río Tirón y proteger su patrimonio arqueológico y cultural_. Cada experto contribuye a este objetivo desde su área:

- **historian** contextualiza la historia local para turistas y residentes.
- **archaeologist** vela por la preservación de hallazgos.
- **guide** diseña rutas y comparte anécdotas.
- **manager** impulsa proyectos culturales.
- **technologist** difunde el legado en línea.

Para añadir un nuevo agente basta con incorporar otra clave al array:

```php
// config/forum_agents.php
return [
    // ... agentes existentes ...
    'event_planner' => [
        'name' => 'Fernando el Planificador',
        'bio' => 'Organiza ferias y actividades que atraen visitantes y celebran la tradición local.',
        'expertise' => 'Coordinación de eventos y turismo cultural'
    ],
];
```

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

`assets/js/sliding-menu.js` controla la apertura y cierre de este panel utilizando
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

## Administración de agentes del foro

Existe una página protegida para modificar el archivo `config/forum_agents.php` de forma sencilla. Se encuentra en `backend/php/admin/forum_agents_admin.php` y solo es accesible tras iniciar sesión como administrador.

1. Abre `/backend/php/admin/forum_agents_admin.php` en tu navegador.
2. El formulario lista cada agente actual con campos para nombre, biografía y experiencia.
3. Cambia los datos que necesites y pulsa **Guardar** para actualizar el archivo.
4. Para añadir un nuevo agente rellena los campos del apartado _Añadir Nuevo Agente_ indicando un identificador único.

Al enviar el formulario se regenerará `config/forum_agents.php` con la nueva información.

## Nuevo panel IA

El botón `#open-unified-panel-button` (con `data-menu-target="unified-panel"`) abre `#unified-panel`, un cajón lateral que integra navegación, herramientas del sitio y el asistente de inteligencia artificial. Dentro de este panel el chat se incrusta desde `fragments/header/ai-drawer.html` sin su cabecera original. Los eventos de apertura y cierre se gestionan en `assets/js/ui-drawers.js`.

Este nuevo panel, junto al de traducción superior, facilita el acceso inmediato a contenido multilingüe y al soporte conversacional. Su objetivo es reforzar nuestra misión de **promocionar el turismo en Cerezo de Río Tirón y proteger su patrimonio arqueológico y cultural**.

## Panel superior de traducción

El archivo `fragments/header/language-flags.html` define `#language-panel` con los botones de idioma. Se puede abrir con `#flag-toggle` o desde el propio `#unified-panel`. Cuando está activo, `js/lang-bar.js` carga el widget de Google Translate y aplica la selección.

Para invocarlo desde cualquier página basta con un botón con `data-menu-target="language-panel"`. `assets/js/sliding-menu.js` se ocupa de desplegarlo y de añadir la clase `menu-open-top` al `<body>`.

Para que los menús se posicionen correctamente, ajusta `--language-bar-offset` según la altura de la barra y usa los estilos de `assets/css/language-panel.css`.
