<?php

chdir(__DIR__ . '/..');
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';
$GLOBALS['TESTING'] = true;
require_once __DIR__ . '/../../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../../includes/csrf.php';
// Do not modify include_path so scripts use real files
