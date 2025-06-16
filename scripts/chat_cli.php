<?php
require_once __DIR__ . '/../includes/ai_utils.php';

$prompt = $argv[1] ?? '';
$prompt = is_string($prompt) ? $prompt : '';
$prompt = trim($prompt);
if ($prompt === '') {
    fwrite(STDERR, "No prompt provided\n");
    exit(1);
}
$response = get_ai_chat_response($prompt);
// Output plain text; errors are returned as text starting with 'Error:'
// We do not JSON encode to keep usage simple.
// Convert newlines to \n to preserve formatting when read from Python.
$response = str_replace(["\r", "\n"], ["", "\n"], $response);
echo $response;

