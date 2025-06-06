<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
require_once __DIR__ . '/../includes/ai_utils.php';
header('Content-Type: application/json; charset=utf-8');
$input = json_decode(file_get_contents('php://input'), true);
$text = trim($input['text'] ?? ($_POST['text'] ?? ''));
$target = trim($input['target_lang'] ?? ($_POST['target_lang'] ?? 'en-ai'));
if ($text === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se proporcionó texto']);
    exit;
}
if (function_exists('translate_with_gemini')) {
    $trans = translate_with_gemini('blog_post', $target, substr($text,0,100));
    echo json_encode(['success' => true, 'translation' => $trans]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Función de traducción no disponible']);
}
