<?php
// ajax_actions/get_correction.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Se requiere POST.']);
    exit;
}

require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$text = '';
if (is_array($data) && isset($data['text_to_correct'])) {
    $text = trim($data['text_to_correct']);
}
if (empty($text) && isset($_POST['text_to_correct'])) {
    $text = trim($_POST['text_to_correct']);
}

if (empty($text)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se proporcionó texto para corregir.']);
    exit;
}

$result = function_exists('get_ai_correction') ? get_ai_correction($text) : 'Error: Funcionalidad no disponible.';
if (stripos($result, 'Error:') === 0) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result]);
} else {
    echo json_encode(['success' => true, 'correction' => $result]);
}
exit;
?>
