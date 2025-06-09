<?php
header('Content-Type: application/json');

$menus = [
    'main' => [
        ['label' => 'Inicio', 'url' => '/index.php'],
        [
            'label' => 'Historia',
            'url' => '#',
            'children' => [
                ['label' => 'Nuestra Historia', 'url' => '/historia/historia.html'],
                ['label' => 'Historia de Cerezo', 'url' => '/historia_cerezo/index.html']
            ]
        ],
        ['label' => 'El Alfoz', 'url' => '/alfoz/alfoz.html'],
        [
            'label' => 'Museo',
            'url' => '#',
            'children' => [
                ['label' => 'Museo Colaborativo', 'url' => '/museo/galeria.php'],
                ['label' => 'Museo 3D', 'url' => '/museo/museo_3d.php'],
                ['label' => 'Subir Pieza', 'url' => '/museo/subir_pieza.php'],
                ['label' => 'Galería Colaborativa', 'url' => '/galeria/galeria_colaborativa.php']
            ]
        ],
        ['label' => 'Tienda', 'url' => '/tienda/index.php'],
        ['label' => 'Visita', 'url' => '/visitas/visitas.html'],
        ['label' => 'Cultura y Legado', 'url' => '/cultura/cultura.html'],
        ['label' => 'Personajes', 'url' => '/personajes/indice_personajes.html'],
        ['label' => 'Gestión de Yacimientos', 'url' => '/empresa/index.php'],
        ['label' => 'Foro', 'url' => '/foro/index.html'],
        ['label' => 'Contacto', 'url' => '/contacto/contacto.html']
    ],
    'admin' => [
        ['label' => 'Admin', 'url' => '/dashboard/login.php']
    ],
    'social' => [
        ['label' => 'Comunidad', 'url' => 'https://www.facebook.com/groups/1052427398664069', 'icon' => 'fab fa-facebook-square'],
        ['label' => 'WhatsApp', 'url' => 'https://chat.whatsapp.com/JWJ6mWXPuekIBZ8HJSSsZx', 'icon' => 'fab fa-whatsapp']
    ]
];

$type = $_GET['type'] ?? 'main';

if (!isset($menus[$type])) {
    http_response_code(404);
    echo json_encode(['error' => 'Menu not found']);
    exit;
}

echo json_encode($menus[$type]);
