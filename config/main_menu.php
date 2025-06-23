<?php
return [
    ['label' => 'menu_inicio', 'url' => 'index.php'],
    'group_explora' => [
        ['label' => 'menu_historia', 'url' => 'historia/historia.php', 'children' => [
            ['label' => 'menu_historia_ampliada', 'url' => 'historia/historia_ampliada.php'],
            ['label' => 'menu_personajes', 'url' => 'personajes/indice_personajes.html'],
            ['label' => 'menu_influencia_romana', 'url' => 'historia/influencia_romana.php'],
        ]],
        ['label' => 'menu_lugares', 'url' => 'lugares/lugares.php', 'children' => [
            ['label' => 'menu_mapa_interactivo', 'url' => 'lugares/mapa_interactivo.php'],
            ['label' => 'menu_ruinas', 'url' => 'ruinas/index.php'],
            ['label' => 'menu_alfoz', 'url' => 'alfoz/alfoz.php'],
        ]],
        ['label' => 'menu_cultura_legado', 'url' => 'cultura/cultura.php'],
    ],
    'group_participa' => [
        ['label' => 'menu_visitas', 'url' => 'visitas/visitas.php'],
        ['label' => 'menu_museo', 'url' => 'museo/galeria.php', 'children' => [
            ['label' => 'menu_museo_3d', 'url' => 'museo/museo_3d.php'],
            ['label' => 'menu_subir_pieza', 'url' => 'museo/subir_pieza.php'], // Acceso restringido?
            ['label' => 'menu_galeria_colaborativa', 'url' => 'galeria/galeria_colaborativa.php'],
        ]],
        ['label' => 'menu_tienda', 'url' => 'tienda/index.php'],
    ],
    'group_comunidad' => [
        ['label' => 'menu_foro', 'url' => 'foro/index.php'],
        ['label' => 'menu_blog', 'url' => 'blog.php'],
        ['label' => 'menu_contacto', 'url' => 'contacto/contacto.php'],
        ['label' => 'menu_documentacion', 'url' => 'docs/index.php'],
    ],
    // 'group_servicios_visitante' => [
    //     ['label' => 'menu_programa_citas', 'url' => 'citas/agenda.php'],
    // ],
    // 'group_herramientas_admin' => [ // Considerar mover a un admin_menu.php
    //     ['label' => 'menu_catalogo_scripts', 'url' => 'scripts_admin.php'],
    // ]
];
