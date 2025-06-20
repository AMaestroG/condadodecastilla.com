<?php

chdir(__DIR__ . '/..');
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
$sessId = getenv('PHP_SESSION_ID');
if ($sessId) {
    session_id($sessId);
}
ensure_session_started();
require_once __DIR__ . '/../../includes/csrf.php';
$_SERVER['REQUEST_METHOD'] = getenv('REQUEST_METHOD') ?: 'GET';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['username'] = getenv('PHP_USERNAME') ?: '';
    $_POST['password'] = getenv('PHP_PASSWORD') ?: '';
    $_POST['csrf_token'] = get_csrf_token();
}
