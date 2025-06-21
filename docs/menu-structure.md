# Estructura del menú principal

El menú principal se define en [`config/main_menu.php`](../config/main_menu.php). A partir de la versión actual los enlaces se organizan en grupos anidados para reflejar mejor las áreas temáticas del sitio.

```
return [
    ['label' => 'menu_inicio', 'url' => 'index.php'],
    [
        'label' => 'group_historia_cultura',
        'items' => [/* ... */]
    ],
    // ...
];
```

Los grupos permiten mostrar submenús en cascada. Esta es la estructura por defecto:

- **Inicio** (`index.php`)
- **Historia y Cultura**
  - Nuestra Historia
  - Historia de Cerezo
  - Obispado de Auca
  - Influencia Romana
  - Cultura y Legado
  - Personajes
- **Lugares y Patrimonio**
  - El Alfoz
  - Lugares Emblemáticos
  - Ruinas y Vestigios
  - Camino de Santiago
  - Museo Colaborativo
  - Museo 3D
  - Subir Pieza
  - Galería Colaborativa
  - Gestión de Yacimientos
- **Servicios al Visitante**
  - Tienda
  - Planifica Tu Visita
  - Programa de Citas
- **Comunidad**
  - Foro
  - Blog
  - Contacto

Cada elemento utiliza la función `t()` para traducir su etiqueta. Al modificar la configuración recuerda regenerar este documento ejecutando el script correspondiente o editándolo a mano.
