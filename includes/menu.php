<?php
require_once __DIR__ . '/i18n.php';

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_menu_items(array $items, string $parentPath = '', int &$counter = 0, string $current = ''): void {
    foreach ($items as $item) {
        $label = htmlspecialchars(t($item['label']));
        $path = $parentPath . '/' . $item['label'];

        if (isset($item['children']) && is_array($item['children'])) {
            $counter++;
            $id = 'submenu-' . md5($path);
            echo '<li class="has-submenu">';
            echo '<button class="submenu-toggle" aria-expanded="false" aria-controls="' . $id . '">' . $label . '</button>';
            echo '<ul id="' . $id . '" class="submenu" aria-hidden="true">';
            render_menu_items($item['children'], $path, $counter, $current);
            echo '</ul>';
            echo '</li>';
        } else {
            $url = htmlspecialchars($item['url']);
            $classes = [];
            if ($current === $item['url']) {
                $classes[] = 'active-link';
            }
            $classAttr = $classes ? ' class="'.implode(' ', $classes).'"' : '';
            echo "<li><a href=\"$url\"$classAttr>$label</a></li>";
        }
    }
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    $current = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $current = ltrim($_SERVER['SCRIPT_NAME'], '/');
    }

    echo '<ul id="main-menu" class="nav-links">';
    $counter = 0;
    render_menu_items($items, '', $counter, $current);
    echo '</ul>';
}
