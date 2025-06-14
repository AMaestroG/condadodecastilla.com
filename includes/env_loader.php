<?php
// includes/env_loader.php
// Load environment variables from .env using vlucas/phpdotenv if available.

function env_load($root = null) {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $root = $root ?: dirname(__DIR__);
    $autoload = $root . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
        if (class_exists('Dotenv\\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable($root);
            $dotenv->safeLoad();
        }
    }
    $loaded = true;
}

env_load();
?>
