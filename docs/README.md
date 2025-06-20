# Documentación

Este directorio reúne todas las guías del proyecto **Condado de Castilla**.
La misión principal es *promocionar el turismo en Cerezo de Río Tirón y*
*proteger su patrimonio arqueológico y cultural*.

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

## Añadir nuevas páginas

1. Crea el archivo dentro de la carpeta adecuada (`historia/`, `lugares/`, etc.).
2. Añade la ruta en `config/main_menu.php` para que aparezca en el menú principal.
3. Opcionalmente crea una hoja de estilos en `assets/css/pages/` y asegúrate
   de que `includes/load_page_css.php` la cargue.

## Archivos relevantes en `docs/`

- [Historia](historia.md)
- [Arqueología](arqueologia.md)
- [Tradición](tradicion.md)
- [Guía de index.php y menús](index-guide.md)
- [Resumen de módulos JavaScript](js-modules-overview.md)
- [Guía de Estilo](style-guide.md)
- [Documentación del crawler](crawler.md)
- [Interfaz de la base de datos de grafo](graph_db.md)
- [Guía de Testing](testing.md)

La guía de estilo recoge la paleta en tonos morado y oro viejo con fondos de
alabastro.
