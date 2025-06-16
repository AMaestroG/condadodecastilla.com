<?php
// includes/env_loader.php
// Automatically load environment variables using vlucas/phpdotenv
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Dotenv\\Dotenv')) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
    }
}
