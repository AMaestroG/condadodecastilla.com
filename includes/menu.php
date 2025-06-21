<?php
require_once __DIR__ . '/i18n.php';

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_menu_items(array $items, string $current, int $depth = 0): void {
    $ulClass = $depth === 0 ? 'nav-links' : 'submenu';
    echo '<ul class="' . $ulClass . '" role="menu">';
    foreach ($items as $item) {
        if (isset($item['items'])) {
            $label = htmlspecialchars(t($item['label']));
            $id = 'submenu-' . md5($item['label'] . $depth);
            echo '<li class="has-submenu">';
            echo '<button class="submenu-toggle" aria-expanded="false" aria-controls="' . $id . '">' . $label . '</button>';
            echo '<ul id="' . $id . '" class="submenu" aria-hidden="true">';
            render_menu_items($item['items'], $current, $depth + 1);
            echo '</ul>';
            echo '</li>';
        } else {
            $label = htmlspecialchars(t($item['label']));
            $url = htmlspecialchars($item['url']);
            $classes = [];
            if ($current === $item['url']) {
                $classes[] = 'active-link';
            }
            $classAttr = $classes ? ' class="' . implode(' ', $classes) . '"' : '';
            echo '<li><a href="' . $url . '"' . $classAttr . '>' . $label . '</a></li>';
        }
    }
    echo '</ul>';
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    $current = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $current = ltrim($_SERVER['SCRIPT_NAME'], '/');
    }

    render_menu_items($items, $current);
}
