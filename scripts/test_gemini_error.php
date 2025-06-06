<?php
// Simple script to demonstrate _call_gemini_api error reporting

define('GEMINI_API_KEY', 'dummy');
define('GEMINI_API_ENDPOINT', 'https://httpstat.us/404');

require_once __DIR__ . '/../includes/ai_utils.php';

$payload = ['test' => 'value'];
$error = null;
$result = _call_gemini_api($payload, $error);

if ($result === null) {
    echo "Error captured: $error\n";
} else {
    echo "Success:\n";
    var_dump($result);
}
