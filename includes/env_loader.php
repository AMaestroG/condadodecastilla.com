<?php
// includes/env_loader.php
// Automatically load environment variables using vlucas/phpdotenv
require_once __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
