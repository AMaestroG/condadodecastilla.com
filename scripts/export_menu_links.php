<?php
$menu = include __DIR__ . '/../config/main_menu.php';
foreach ($menu as $item) {
    echo "- `{$item['url']}` – {$item['label']}\n";
}

