<?php
chdir(__DIR__ . '/..');
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../../includes/csrf.php';
// Automatically log in as admin for protected pages
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
?>
