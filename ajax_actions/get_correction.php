<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json; charset=utf-8');
$input = json_decode(file_get_contents('php://input'), true);
$text = trim($input['text_to_correct'] ?? ($_POST['text_to_correct'] ?? ''));
if ($text === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se proporcionó texto']);
    exit;
}
if (function_exists('get_ai_correction')) {
    $corr = get_ai_correction($text);
    if (stripos($corr, 'Error:') === 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $corr]);
    } else {
        echo json_encode(['success' => true, 'correction' => $corr]);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Función de corrección no disponible']);
}
