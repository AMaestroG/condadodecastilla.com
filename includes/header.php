<?php
$baseDir = dirname(__DIR__);

// Output language bar
echo file_get_contents($baseDir . '/fragments/header/language-bar.html');


// Load navigation fragment with menu placeholders
$navigation = file_get_contents($baseDir . '/fragments/header/navigation.html');

// Replace main menu placeholder
$navigation = str_replace(
    '<div id="main-menu-placeholder"></div>',
    file_get_contents($baseDir . '/fragments/menus/main-menu.html'),
    $navigation
);

// Replace admin menu placeholder (this fragment contains PHP)
ob_start();
include $baseDir . '/fragments/menus/admin-menu.php';
$adminMenu = ob_get_clean();
$navigation = str_replace(
    '<div id="admin-menu-placeholder"></div>',
    $adminMenu,
    $navigation
);

// Replace social menu placeholder
$navigation = str_replace(
    '<div id="social-menu-placeholder"></div>',
    file_get_contents($baseDir . '/fragments/menus/social-menu.html'),
    $navigation
);

// Output final navigation markup
echo $navigation;


