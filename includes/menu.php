<?php
require_once __DIR__ . '/i18n.php';

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_menu_items(array $items, string $current, int $depth = 0, bool $root = false): void {
    $ulClass = $root ? 'nav-links' : 'submenu';
    $idAttr = $root ? ' id="main-menu"' : '';
    $roleAttr = $root ? ' role="menu"' : '';
    echo "<ul{$idAttr} class=\"{$ulClass}\"{$roleAttr}>";
    foreach ($items as $item) {
        $label = htmlspecialchars(t($item['label']));
        $url = htmlspecialchars($item['url']);
        $classes = [];
        if ($current === $item['url']) {
            $classes[] = 'active-link';
        }
        $classAttr = $classes ? ' class="'.implode(' ', $classes).'"' : '';
        echo "<li><a href=\"$url\"$classAttr>$label</a>";
        if (isset($item['children']) && is_array($item['children'])) {
            render_menu_items($item['children'], $current, $depth + 1);
        }
        echo '</li>';
    }
    echo '</ul>';
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    $current = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $current = ltrim($_SERVER['SCRIPT_NAME'], '/');
    }

    render_menu_items($items, $current, 0, true);
}
