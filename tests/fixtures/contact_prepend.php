<?php

chdir(__DIR__ . '/..');
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
ensure_session_started();
$_SERVER['REQUEST_METHOD'] = getenv('REQUEST_METHOD') ?: 'POST';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['nombre'] = getenv('CONTACT_NOMBRE') ?: '';
    $_POST['email'] = getenv('CONTACT_EMAIL') ?: '';
    $_POST['asunto'] = getenv('CONTACT_ASUNTO') ?: '';
    $_POST['mensaje'] = getenv('CONTACT_MENSAJE') ?: '';
}
