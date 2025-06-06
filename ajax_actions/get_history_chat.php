<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json; charset=utf-8');
$input = json_decode(file_get_contents('php://input'), true);
$question = isset($input['message']) ? trim($input['message']) : '';
if ($question === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se proporcionó mensaje.']);
    exit;
}
if (function_exists('get_history_chat_response')) {
    $reply = get_history_chat_response($question);
    if (stripos($reply, 'Error:') === 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $reply]);
    } else {
        echo json_encode(['success' => true, 'reply' => $reply]);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Funcionalidad no disponible.']);
}
?>
