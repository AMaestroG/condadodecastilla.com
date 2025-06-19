<?php

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    echo '<ul id="main-menu" class="nav-links">';
    foreach ($items as $item) {
        $label = htmlspecialchars($item['label']);
        $url = htmlspecialchars($item['url']);
        echo "<li><a href=\"$url\">$label</a></li>";
    }
    echo '</ul>';
}
