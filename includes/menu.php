<?php
require_once __DIR__ . '/i18n.php';

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    $current = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $current = ltrim($_SERVER['SCRIPT_NAME'], '/');
    }

    echo '<ul id="main-menu" class="nav-links">';
    foreach ($items as $item) {
        $label = htmlspecialchars(t($item['label']));
        $url = htmlspecialchars($item['url']);
        $classes = [];
        if ($current === $item['url']) {
            $classes[] = 'active-link';
        }
        $classAttr = $classes ? ' class="'.implode(' ', $classes).'"' : '';
        echo "<li><a href=\"$url\"$classAttr>$label</a></li>";
    }
    echo '</ul>';
}
