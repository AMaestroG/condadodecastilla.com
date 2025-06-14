<?php
require_once __DIR__ . '/env_loader.php';

function ensure_session_started() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}
?>
