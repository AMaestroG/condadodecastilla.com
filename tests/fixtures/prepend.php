<?php
chdir(__DIR__ . '/..');
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
// Attempt to force load pdo_sqlite if not already loaded
if (!extension_loaded('pdo_sqlite')) {
    @dl('pdo_sqlite.so');
}
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../../includes/csrf.php';
?>
