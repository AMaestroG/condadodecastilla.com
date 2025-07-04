# Estructura del menú principal

La siguiente tabla se genera a partir de [`config/main_menu.php`](../config/main_menu.php). Ahora el archivo organiza las entradas por **grupos** utilizando claves de la forma `group_<nombre>`.
Cada clave de grupo contiene un array con sus elementos:

```php
return [
    'group_historia_cultura' => [
        ['label' => 'menu_historia', 'url' => 'historia/historia.php'],
        // ...
    ],
    // más grupos
];
```

El texto visible para cada grupo se obtiene con `t('group_<nombre>')`.
Debe regenerarse la tabla cuando cambien los enlaces del menú principal.

| URL                                          | Etiqueta               |
| -------------------------------------------- | ---------------------- |
| index.php                                    | Inicio                 |
| historia/historia.php                        | Nuestra Historia       |
| historia/historia_ampliada.php               | Historia Ampliada      |
| historia_cerezo/index.php                    | Historia de Cerezo     |
| historia/subpaginas/obispado_auca_cerezo.php | Obispado de Auca       |
| historia/influencia_romana.php               | Influencia Romana      |
| alfoz/alfoz.php                              | El Alfoz               |
| lugares/lugares.php                          | Lugares Emblemáticos   |
| ruinas/index.php                             | Ruinas y Vestigios     |
| camino_santiago/camino_santiago.php          | Camino de Santiago     |
| museo/galeria.php                            | Museo Colaborativo     |
| museo/museo_3d.php                           | Museo 3D               |
| museo/subir_pieza.php                        | Subir Pieza            |
| galeria/galeria_colaborativa.php             | Galería Colaborativa   |
| tienda/index.php                             | Tienda                 |
| visitas/visitas.php                          | Planifica Tu Visita    |
| citas/agenda.php                             | Programa de Citas      |
| cultura/cultura.php                          | Cultura y Legado       |
| personajes/indice_personajes.html            | Personajes             |
| empresa/index.php                            | Gestión de Yacimientos |
| foro/index.php                               | Foro                   |
| blog.php                                     | Blog                   |
| contacto/contacto.php                        | Contacto               |

## Etiquetas de grupo

Para los menús que agrupan varias entradas se usa un identificador de grupo. Cada grupo requiere una clave de traducción `group_<nombre>` que debe definirse en `i18n/es.json` y en los demás archivos de idioma. Por ejemplo, el grupo Historia y Cultura se traduce mediante la clave `group_historia_cultura`.
