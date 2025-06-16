<?php
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text_to_translate'] ?? '';
$target = $input['target_lang'] ?? 'en';
$result = get_ai_translation($text, $target);

$response = [
    'success' => strpos($result, 'Error:') !== 0,
];
if ($response['success']) {
    $response['translation'] = $result;
} else {
    $response['error'] = $result;
}

echo json_encode($response);

