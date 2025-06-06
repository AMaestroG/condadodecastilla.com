<?php
// includes/ai_utils.php

/**
 * Obtiene la clave API de Gemini desde variables de entorno.
 *
 * @return string|null
 */
function get_gemini_api_key(): ?string {
    $key = getenv('GEMINI_API_KEY');
    return $key !== false ? $key : null;
}

/**
 * Obtiene el endpoint de la API de Gemini desde variables de entorno.
 *
 * @return string|null
 */
function get_gemini_api_endpoint(): ?string {
    $endpoint = getenv('GEMINI_API_ENDPOINT');
    return $endpoint !== false ? $endpoint : null;
}

/**
 * Realiza una llamada HTTP POST a la API de Gemini.
 *
 * @param array $payload Datos que se enviarán como JSON.
 * @param string &$error Mensaje de error en caso de fallo.
 * @return array|null Respuesta decodificada o null si ocurre un error.
 */
function call_gemini_api(array $payload, string &$error = ''): ?array {
    $api_key = get_gemini_api_key();
    $endpoint = get_gemini_api_endpoint();

    if (empty($api_key) || empty($endpoint)) {
        $error = 'Variables de entorno GEMINI_API_KEY o GEMINI_API_ENDPOINT no definidas.';
        return null;
    }

    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n" .
                        'Authorization: Bearer ' . $api_key,
            'content' => json_encode($payload),
            'timeout' => 30,
        ]
    ]);

    $response_json = @file_get_contents($endpoint, false, $context);
    if ($response_json === false) {
        $last_error = error_get_last();
        $error = $last_error['message'] ?? 'Error desconocido al conectar con la API.';
        return null;
    }

    $decoded = json_decode($response_json, true);
    if ($decoded === null) {
        $error = 'La respuesta de la API no es JSON válido.';
        return null;
    }

    return $decoded;
}

if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

/**
 * Genera un resumen inteligente utilizando la API de Gemini.
 *
 * @param string $content_key Un identificador para el contenido a resumir,
 *                            o potencialmente el texto completo si es corto.
 * @param string $full_text (Opcional) El texto completo a resumir.
 * @return string El resumen generado o un mensaje de error.
 */
function get_smart_summary_placeholder(string $content_key, string $full_text = ''): string {
    $text = !empty($full_text) ? $full_text : $content_key;
    $text = trim($text);

    if ($text === '') {
        return 'Error: No se proporcionó texto para resumir.';
    }

    $prompt = "Resume el siguiente texto en español:\n\n\"{$text}\"";
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $error = '';
    $api_response = call_gemini_api($payload, $error);
    if ($api_response === null) {
        return 'Error al obtener resumen: ' . $error;
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $summary = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return nl2br(htmlspecialchars($summary));
    }

    if (isset($api_response['error']['message'])) {
        return 'Error de la API: ' . htmlspecialchars($api_response['error']['message']);
    }

    return 'Error: Respuesta inesperada de la API de resumen.';
}

/**
 * Placeholder para una función que generaría etiquetas sugeridas por IA.
 *
 * @param string $content_key Un identificador para el contenido a etiquetar.
 * @return array Un array de strings con las etiquetas sugeridas.
 */
function get_suggested_tags_placeholder(string $content_key): array {
    // Simulación basada en content_key
    if ($content_key === 'atapuerca' || $content_key === 'Contenido de Atapuerca') {
        return ['Prehistoria', 'Evolución Humana', 'Arqueología', 'Yacimientos UNESCO', 'Homo Antecessor', 'Burgos', 'Paleontología'];
    }

    // Etiquetas por defecto para otros contenidos no especificados
    return ['General', 'Contenido Interesante', 'Historia', 'Web'];
}

/**
 * Función helper para simular una llamada a la API de Gemini.
 * En un entorno real, esto realizaría una solicitud HTTP real usando curl.
 *
 * @param array $payload El cuerpo de la solicitud a enviar a la API.
 * @return array|null La respuesta decodificada de la API como array, o null en caso de error simulado.
 */
function _call_gemini_api_simulator(array $payload): ?array {
    // Simulación de la llamada a la API y la respuesta.
    // No se realizan llamadas cURL reales aquí para el entorno de demostración.

    $simulated_success = true; // Cambiar a false para probar manejo de errores

    if (!$simulated_success) {
        error_log("Simulated Gemini API call failed.");
        return null;
    }

    // Extraer el prompt para la simulación de respuesta (asumiendo una estructura simple)
    $prompt_text = "";
    if (isset($payload['contents'][0]['parts'][0]['text'])) {
        $prompt_text = $payload['contents'][0]['parts'][0]['text'];
    }

    // Simular una respuesta basada en el tipo de prompt (muy básico)
    $generated_text = "Este es un texto simulado generado por la API de Gemini en respuesta a un prompt que comenzaba con: '" . substr(htmlspecialchars($prompt_text), 0, 100) . "...'. ";
    if (stripos($prompt_text, "resume") !== false || stripos($prompt_text, "resumen") !== false) {
        $generated_text .= "Este parece ser un resumen. Los puntos clave incluyen A, B y C. La elaboración detallada seguiría.";
    } elseif (stripos($prompt_text, "etiquetas") !== false || stripos($prompt_text, "tags") !== false) {
        $generated_text = json_encode(['tag1', 'tag2', 'tag3', 'simulated_tag']); // Simular respuesta para etiquetas
    } elseif (stripos($prompt_text, "traduce") !== false || stripos($prompt_text, "translate") !== false) {
        $generated_text .= "Esta es la porción traducida simulada. El contenido original se ha procesado y convertido al idioma de destino.";
    }


    // Estructura de respuesta simulada similar a lo que podría devolver Gemini API (muy simplificada)
    // Referencia: https://ai.google.dev/docs/gemini_api_overview?hl=es-419#text-generation-response
    // La estructura real puede variar, esto es solo para la simulación.
    $simulated_response = [
        'candidates' => [
            [
                'content' => [
                    'parts' => [
                        [
                            'text' => $generated_text
                        ]
                    ],
                    'role' => 'model'
                ],
                // Otros campos como 'finishReason', 'index', 'safetyRatings' podrían estar aquí.
            ]
        ],
        // 'promptFeedback' podría estar aquí.
    ];

    return $simulated_response;
}

/**
 * Genera un resumen de un texto utilizando el simulador de la API de Gemini.
 *
 * @param string $text_to_summarize El texto que se va a resumir.
 * @return string El resumen generado o un mensaje de error.
 */
function get_real_ai_summary(string $text_to_summarize): string {
    if (empty(trim($text_to_summarize))) {
        return "Error: No se proporcionó texto para resumir.";
    }

    // Preparar el prompt para la API de Gemini
    $prompt = "Por favor, resume el siguiente texto de forma concisa y clara, enfocándote en los puntos clave relevantes para un lector interesado en historia y cultura. El resumen debe ser adecuado para mostrar en una página web. Texto a resumir:

\"" . $text_to_summarize . "\"";

    // Estructura del payload para la API de Gemini (simplificada para el simulador)
    // Ver: https://ai.google.dev/docs/gemini_api_overview?hl=es-419#text-generation-prompt
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        // Se podrían añadir 'generationConfig' o 'safetySettings' aquí si el simulador los manejara.
    ];

    $api_response = _call_gemini_api_simulator($payload);

    if ($api_response === null) {
        return "Error: La llamada simulada a la API de IA para el resumen falló.";
    }

    // Procesar la respuesta simulada (adaptar según la estructura real de Gemini si es necesario)
    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $summary = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        // Podría ser necesario un post-procesamiento adicional aquí para limpiar el resumen.
        return !empty($summary) ? nl2br(htmlspecialchars($summary)) : "Error: El resumen generado por la IA simulada estaba vacío.";
    } elseif (isset($api_response['error']['message'])) { // Manejo de errores de la API si los hubiera
         return "Error de la API de IA simulada: " . htmlspecialchars($api_response['error']['message']);
    } else {
        // Loggear la respuesta inesperada para depuración si es posible en un entorno real.
        // error_log("Respuesta inesperada de la API de IA simulada: " . print_r($api_response, true));
        return "Error: Respuesta inesperada del servicio de resumen de IA simulada.";
    }
}

/**
 * Realiza una traducción utilizando la API de Gemini.
 *
 * @param string $content_id Identificador del contenido (ej. 'atapuerca_main_text').
 * @param string $target_language Código del idioma objetivo (ej. 'en-ai', 'fr-ai').
 * @param string $original_sample_text Un extracto del texto original para incluir en la demo. O el texto completo si se desea devolverlo para 'es'.
 * @return string El texto traducido o un mensaje de error.
 */
function get_simulated_translation_placeholder(string $content_id, string $target_language, string $original_sample_text = ''): string {
    $text = trim($original_sample_text);

    if ($target_language === 'es') {
        return $text;
    }

    if ($text === '') {
        return 'Error: No se proporcionó texto para traducir.';
    }

    $prompt = "Traduce al idioma {$target_language} el siguiente texto conservando el significado:\n\n\"{$text}\"";
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $error = '';
    $api_response = call_gemini_api($payload, $error);
    if ($api_response === null) {
        return 'Error al traducir: ' . $error;
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $translation = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return nl2br(htmlspecialchars($translation));
    }

    if (isset($api_response['error']['message'])) {
        return 'Error de la API: ' . htmlspecialchars($api_response['error']['message']);
    }

    return 'Error: Respuesta inesperada de la API de traducción.';
}

?>
