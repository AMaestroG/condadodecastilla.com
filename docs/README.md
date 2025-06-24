# Documentación

Este directorio reúne todas las guías del proyecto **Condado de Castilla**.

La misión principal es _promocionar el turismo en Cerezo de Río Tirón y_
_proteger su patrimonio arqueológico y cultural_.

## Estructura principal

- `assets/` – imágenes, estilos y scripts. Los módulos JavaScript se agrupan en
  `assets/js` y se describen brevemente en
  [js-modules-overview.md](js-modules-overview.md). Los antiguos `escudo-reveal.js`,
  `escudo-drag.js` y `cave_mask.js` se retiraron por falta de uso; si se
  necesita un efecto similar se puede emplear `torch_cursor.js` o máscaras CSS.
- `includes/` – fragmentos PHP y utilidades comunes.
- `museo/` – páginas del museo y fichas de piezas.
- `foro/` – área gestionada por agentes expertos.
- `backend/` – API en Flask e integración de IA.
- `dashboard/` – panel de administración y estadísticas (ver [dashboard/README.html](../dashboard/README.html) para configuración y uso).
- `docs/` – documentación completa.
- `legacy/` – versiones anteriores que se conservan solo para referencia histórica.

La portada actual del sitio se sirve desde **`index.php`** en la raíz del proyecto.
El antiguo `index.html` se movió a `legacy/`.

## Árbol de directorios

```text
.
├── ajax_actions
├── alfoz
├── assets
│   ├── css
│   ├── data
│   ├── flags
│   ├── icons
│   ├── img
│   ├── js
│   ├── scss
│   └── vendor
├── camino_santiago
├── citas
├── config
├── contacto
├── contenido
│   ├── blog
│   ├── historia
│   └── lugares
├── contexto
├── cultura
├── dashboard
├── data
│   └── historia
├── database_setup
├── datos
├── docs
├── empresa
├── foro
├── fragments
│   ├── header
│   └── menus
├── galeria
├── historia
│   ├── documentos
│   ├── galeria_historica
│   ├── subpaginas
│   └── timeline
├── historia_cerezo
│   ├── capitulo1
│   ├── capitulo2
│   ├── capitulo3
│   ├── capitulo4
│   ├── entidades
│   └── indices
├── imagenes
├── includes
├── js
│   └── museum-3d
├── lugares
│   └── alfozcerezolantaron
├── museo
├── personajes
│   ├── Condes_de_Castilla_Alava_y_Lantaron
│   ├── Emperadores_Romanos_Hispanos_Auca
│   ├── Leyendas_y_Cuentos
│   ├── Militares_y_Gobernantes
│   ├── Ordenes_y_Legados
│   └── Santos_y_Martires
├── ruinas
│   ├── edificaciones_civiles_publicas
│   ├── estructuras_defensivas
│   ├── estructuras_funerarias_religiosas
│   ├── estructuras_residenciales_elite
│   ├── hallazgos_representaciones
│   └── infraestructura_general
├── scripts
│   └── temp_output
├── secciones_index
├── tests
│   ├── fixtures
│   └── manual
├── tienda
├── uploads
│   ├── galeria
│   └── museo_piezas
├── uploads_storage
└── visitas

79 directories
```

## Organización de templates y fragmentos

Los encabezados y pies de página se extraen a ficheros reutilizables para
simplificar el mantenimiento:
Los archivos de entrada son `fragments/header.php` y `fragments/footer.php`, que deben incluirse directamente.

- `fragments/header.php` incluye los menús deslizantes y los fragmentos situados en
  `fragments/header/` y `fragments/menus/`.
- `fragments/footer.php` carga los scripts comunes y el menú social.

Para insertar estas partes en una página basta con:

```php
<?php require_once __DIR__ . '/fragments/header.php'; ?>
<!-- contenido de la página -->
<?php require_once __DIR__ . '/fragments/footer.php'; ?>
```

Los fragmentos del directorio `fragments/` pueden copiarse o modificarse para
crear nuevas secciones del menú.

### Escudo y Banner

La cabecera de la web muestra un **banner centrado** con el escudo de
Cerezo de Río Tirón.  Sirve como punto focal y enlaza siempre a la
página principal.  El marcado se genera en
`fragments/header.php` y se apoya en las clases
`.hero-escudo` y `#header-escudo-overlay` definidas en
`assets/css/epic_theme.css` y `assets/css/header/topbar.css`.

Para utilizarlo simplemente carga el fragmento de cabecera al inicio de
cada plantilla:

```php
<?php require_once __DIR__ . '/fragments/header.php'; ?>
```

Esto añade el banner superior junto con los menús deslizantes y
aplica los colores morado y oro viejo sobre fondo de alabastro
descritos en la guía de estilo.

### Breadcrumbs

El fragmento `fragments/breadcrumbs.php` genera automáticamente una lista de enlaces según la URL actual. Se usa así:

```php
<?php
require_once __DIR__ . '/fragments/breadcrumbs.php';
render_breadcrumbs([
    'historia' => 'Nuestra Historia',
    'subpaginas' => 'Índice Detallado',
]);
?>
```

Los estilos coinciden con `assets/css/pages/historia_subpaginas_auca_patricia_ubicacion.css`.

## Añadir nuevas páginas

1. Crea el archivo dentro de la carpeta adecuada (`historia/`, `lugares/`, etc.).
2. Añade la ruta en `config/main_menu.php` para que aparezca en el menú principal.
3. Opcionalmente crea una hoja de estilos en `assets/css/pages/` y asegúrate
   de que `includes/load_page_css.php` la cargue.

## Archivos relevantes en `docs/`

- [Historia](historia.md)
- [Historia ampliada](historia_ampliada_nuevo4.md)
- [Arqueología](arqueologia.md)
- [Tradición](tradicion.md)
- [Guía de index.php y menús](index-guide.md)
- [Estructura del menú](menu-structure.md)
- [Resumen de módulos JavaScript](js-modules-overview.md)
- [Guía de Estilo](style-guide.md)
- [Documentación del crawler](crawler.md)
- [Interfaz de la base de datos de grafo](graph_db.md)
- [API de consulta de grafo](../api/graph)
- [API de la Galería Colaborativa](galeria_api.md)
- [API de agentes del foro](forum_agents_api.md)
- [Guía de Testing](testing.md)
- [Gráfica de Influencia Romana](roman_influence_graph.md)
- [Relaciones de Parentesco](parent_child_pairs.md)
- [Visualizador del Árbol Genealógico](../personajes/genealogia/index.html)
- [Guía Fullstack 2025](fullstack-tools-2025.md)
- [Agentes del Foro](forum_agents.md) – incluye la sección [Cómo actualizar la lista](forum_agents.md#como-actualizar-la-lista-de-agentes)
- [Guía de mensajes de commit](commit-style.md)
- [Seguridad de Markdown](security.md)

## Demos

- [Museo en Astro](../README.md#museo-en-astro)
- [Tailwind demo](../tailwind_index.php) – muestra los estilos de Tailwind y los menús deslizantes.

La guía de estilo recoge la paleta en tonos morado y oro viejo con fondos de
alabastro.

La síntesis extendida de la historia de Cerezo de Río Tirón se encuentra en
[historia_ampliada_nuevo4.md](historia_ampliada_nuevo4.md).
