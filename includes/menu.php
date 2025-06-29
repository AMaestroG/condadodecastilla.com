<?php
require_once __DIR__ . '/i18n.php';

function get_main_menu_items(): array {
    static $menu;
    if ($menu === null) {
        $menu = require __DIR__ . '/../config/main_menu.php';
    }
    return $menu;
}

function render_menu_items(array $items, string $parentPath = '', int &$counter = 0, string $current = '', bool $isSubmenu = false): void {
    foreach ($items as $key => $item) {
        $baseClasses = 'block px-3 py-2 rounded-md text-base font-medium transition-colors duration-150 ease-in-out';
        // Updated classes for new panel theme
        $activeClass = 'bg-epic-purple-hover-custom text-old-gold'; // Active link: darker purple bg, gold text
        $inactiveClass = 'text-epic-text-light-custom hover:bg-epic-purple-hover-custom hover:text-old-gold'; // Inactive: light text, hover darker purple bg & gold text

        if (is_string($key) && is_array($item)) { // Group
            echo '<li class="menu-group mt-4 first:mt-0">';
            echo '<span class="group-title block px-3 py-2 text-xs font-semibold text-old-gold opacity-75 uppercase tracking-wider">' . htmlspecialchars(t($key)) . '</span>'; // Group titles: gold, slightly transparent
            echo '<ul class="mt-1 space-y-1">';
            render_menu_items($item, $parentPath, $counter, $current, false); // Children of a group are not submenus in the same way
            echo '</ul>';
            echo '</li>';
            continue;
        }

        $label = htmlspecialchars(t($item['label']));
        $url = htmlspecialchars($item['url'] ?? '#'); // Use '#' if no URL for parent items

        if (isset($item['children']) && is_array($item['children'])) {
            $counter++;
            $id = 'submenu-' . md5($parentPath . $item['label'] . $counter);
            $isActive = ($current === $url || array_filter($item['children'], fn($child) => ltrim($child['url'], '/') === $current));

            echo '<li class="has-submenu space-y-1">';
            echo '<button class="submenu-toggle w-full flex items-center justify-between ' . $baseClasses . ($isActive ? $activeClass : $inactiveClass) . '" aria-expanded="false" aria-controls="' . $id . '">';
            echo '<span>' . $label . '</span>';
            echo '<svg class="submenu-arrow w-5 h-5 transform transition-transform duration-150 ease-in-out" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
            echo '</button>';
            echo '<ul id="' . $id . '" class="submenu ml-4 mt-1 space-y-1" style="display: none;" aria-hidden="true">'; // Hidden by default, JS will show
            render_menu_items($item['children'], $parentPath . $item['label'] . '/', $counter, $current, true);
            echo '</ul>';
            echo '</li>';
        } else {
            $isCurrentPage = ($current === ltrim($url, '/'));
            $linkClasses = $baseClasses . ' ' . ($isCurrentPage ? $activeClass : $inactiveClass);
            if ($isSubmenu) {
                $linkClasses .= ' text-sm'; // Slightly smaller text for submenu items
            }
            echo '<li><a href="' . $url . '" class="' . $linkClasses . '"' . ($isCurrentPage ? ' aria-current="page"' : '') . '>' . $label . '</a></li>';
        }
    }
}

function render_main_menu(): void {
    $items = get_main_menu_items();
    $current = '';
    if (!empty($_SERVER['REQUEST_URI'])) {
        $current = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }
    // Fallback for index pages
    if ($current === '' || $current === 'index.php') {
        $current = 'index.php';
    }


    echo '<ul id="main-menu" class="nav-links space-y-1" role="menu">';
    $counter = 0;
    render_menu_items($items, '', $counter, $current);
    echo '</ul>';
    // Script for submenu toggling is now in /assets/js/sidebar-menu.js
}

// Helper function to check if a menu item or its children is active
// (Not strictly needed with current URI check, but can be useful for complex scenarios)
function is_menu_item_active(array $item, string $currentUrl): bool {
    if (isset($item['url']) && ltrim($item['url'], '/') === $currentUrl) {
        return true;
    }
    if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
            if (is_menu_item_active($child, $currentUrl)) {
                return true;
            }
        }
    }
    return false;
}

?>
