<?php
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text_to_summarize'] ?? '';
$result = get_real_ai_summary($text);

$response = [
    'success' => strpos($result, 'Error:') !== 0,
];
if ($response['success']) {
    $response['summary'] = $result;
} else {
    $response['error'] = $result;
}

echo json_encode($response);

