<?php
require_once __DIR__ . '/env_loader.php';

if (!defined('FORUM_COMMENT_COOLDOWN')) {
    $cooldown = getenv('FORUM_COMMENT_COOLDOWN');
    define('FORUM_COMMENT_COOLDOWN', $cooldown !== false ? (int)$cooldown : 60);
}

if (!defined('HEADER_BANNER_URL')) {
    $banner = getenv('HEADER_BANNER_URL');
    define('HEADER_BANNER_URL', $banner !== false ? $banner : '/assets/img/escudo.jpg');
}
