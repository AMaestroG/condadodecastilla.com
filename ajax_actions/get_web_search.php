<?php
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$query = $input['query'] ?? '';
$result = get_web_search_results($query);

$response = [
    'success' => strpos($result, 'Error:') !== 0,
];
if ($response['success']) {
    $response['results'] = $result;
} else {
    $response['error'] = $result;
}

echo json_encode($response);

