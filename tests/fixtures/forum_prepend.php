<?php

chdir(__DIR__ . '/..');
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
$sid = getenv('PHP_SESSION_ID');
if ($sid) {
    session_id($sid);
}
ensure_session_started();
require_once __DIR__ . '/../../includes/csrf.php';
$_SERVER['REQUEST_METHOD'] = getenv('REQUEST_METHOD') ?: 'GET';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['agent'] = getenv('FORUM_AGENT') ?: '';
    $_POST['comment'] = getenv('FORUM_COMMENT') ?: '';
    $token = getenv('FORUM_TOKEN');
    if ($token !== false) {
        $_POST['csrf_token'] = $token;
    } else {
        $_POST['csrf_token'] = get_csrf_token();
    }
}
