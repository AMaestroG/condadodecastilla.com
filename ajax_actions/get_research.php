<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Se requiere POST.']);
    exit;
}
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json; charset=utf-8');
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$query = '';
if (is_array($data) && isset($data['query'])) {
    $query = trim($data['query']);
}
if (empty($query) && isset($_POST['query'])) {
    $query = trim($_POST['query']);
}
if (empty($query)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se proporcionó consulta.']);
    exit;
}
$result = function_exists('get_ai_research') ? get_ai_research($query) : 'Error: Funcionalidad no disponible.';
if (stripos($result, 'Error:') === 0) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result]);
} else {
    echo json_encode(['success' => true, 'research' => $result]);
}
exit;
?>
