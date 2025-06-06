<?php
// ajax_actions/get_summary.php

// Asegurarse de que solo se acceda mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Se requiere POST.']);
    exit;
}

// Incluir utilidades de IA
// Corregir la ruta para que sea relativa al directorio raíz del proyecto
require_once __DIR__ . '/../includes/ai_utils.php';

// Establecer el tipo de contenido de la respuesta a JSON
header('Content-Type: application/json; charset=utf-8');

// Obtener el texto del cuerpo de la solicitud POST
$text_to_summarize = '';
// Primero, intentar obtener de un payload JSON
if (strpos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== false) {
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    if (isset($data['text_to_summarize'])) {
        $text_to_summarize = trim($data['text_to_summarize']);
    }
}
// Si no se encontró en JSON o el Content-Type no era JSON, intentar con $_POST
// Esto es útil si se envía como FormData desde JS
if (empty($text_to_summarize) && isset($_POST['text_to_summarize'])) {
    $text_to_summarize = trim($_POST['text_to_summarize']);
}


if (empty($text_to_summarize)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'No se proporcionó texto para resumir.']);
    exit;
}

// Validar longitud del texto si es necesario
if (!defined('MAX_TEXT_LENGTH_FOR_SUMMARY')) {
    define('MAX_TEXT_LENGTH_FOR_SUMMARY', 10000); // Límite de caracteres
}
if (mb_strlen($text_to_summarize) > MAX_TEXT_LENGTH_FOR_SUMMARY) {
    http_response_code(413); // Payload Too Large
    echo json_encode(['success' => false, 'error' => 'El texto es demasiado largo para ser procesado. Límite: ' . MAX_TEXT_LENGTH_FOR_SUMMARY . ' caracteres.']);
    exit;
}

// Llamar a la función para obtener el resumen
if (function_exists('get_real_ai_summary')) {
    $summary_html = get_real_ai_summary($text_to_summarize);

    if (stripos($summary_html, "Error:") === 0) {
        // Fallback to simple placeholder summary
        if (function_exists('get_smart_summary')) {
            $summary_html = get_smart_summary('fallback', $text_to_summarize);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $summary_html]);
            exit;
        }
    }
    echo json_encode(['success' => true, 'summary' => $summary_html]);
} else {
    // If real AI summary not available, use the placeholder if possible
    if (function_exists('get_smart_summary')) {
        $summary_html = get_smart_summary('fallback', $text_to_summarize);
        echo json_encode(['success' => true, 'summary' => $summary_html]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'La funcionalidad de resumen IA no está disponible en el servidor.']);
    }
}

exit;
?>
