<?php
// includes/ai_utils.php

require_once __DIR__ . '/env_loader.php';
// Cargar variables de entorno desde .env

require_once __DIR__ . '/session.php';
ensure_session_started();

// Read Gemini API settings from environment variables when available
if (!defined('GEMINI_API_KEY')) {
    $envKey = getenv('GEMINI_API_KEY');
    // Reads GEMINI_API_KEY only
    define('GEMINI_API_KEY', $envKey !== false ? $envKey : 'YOUR_GEMINI_API_KEY_NOT_SET');
}
if (!defined('GEMINI_API_ENDPOINT')) {
    $envEndpoint = getenv('GEMINI_API_ENDPOINT');
    define('GEMINI_API_ENDPOINT', $envEndpoint !== false ? $envEndpoint : 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent');
}

if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_NOT_SET' || GEMINI_API_KEY === '') {
    error_log('GEMINI_API_KEY is missing. Using simulator responses.');
    $_SESSION['gemini_api_key_notice'] = 'La clave de la API de Gemini no está configurada. Las funciones de IA usan un simulador.';
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

    // @phpstan-ignore-next-line
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
 * Builds the payload for the Gemini API.
 *
 * @param string $prompt The prompt string.
 * @return array The payload structure.
 */
function _build_gemini_payload(string $prompt): array {
    return [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
        // Consider if 'generationConfig' or 'safetySettings' should be default or parameterizable
        // For now, keep it simple as per existing usage.
    ];
}

/**
 * Parses the response from the Gemini API.
 *
 * @param array|null $api_response The decoded API response.
 * @param string|null $call_error Error message from the API call itself (e.g., cURL error).
 * @return string The extracted text content or an error message prefixed with "Error:".
 */
function _parse_gemini_response(?array $api_response, ?string $call_error): string {
    if ($call_error !== null) {
        return "Error: " . $call_error;
    }

    if ($api_response === null) {
        return "Error: API call failed without a specific message.";
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $text = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        if (empty($text)) {
            return "Error: El texto generado por la IA estaba vacío.";
        }
        return $text; // Return raw text
    }

    if (isset($api_response['error']['message'])) {
        return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    }

    return "Error: Respuesta inesperada del servicio de IA.";
}

/**
 * Llama a la API de Gemini utilizando cURL o usa el simulador si la
 * configuración sigue con valores de ejemplo.
 *
 * @param array $payload Cuerpo de la solicitud para la API.
 * @return array|null Respuesta decodificada o null si hay errores.
 */
function _call_gemini_api(array $payload, ?string &$error = null): ?array {
    // Use simulator when the API key is missing or still has the placeholder
    // value. A real HTTP request is only attempted if a non-empty key is
    // provided via the environment or configuration.
    if (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_NOT_SET' || GEMINI_API_KEY === '') {
        return _call_gemini_api_simulator($payload);
    }

    if (function_exists('curl_init')) {
        $ch = curl_init(GEMINI_API_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . GEMINI_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        if ($response === false) {
            $error = 'Gemini API curl error: ' . curl_error($ch);
            error_log($error);
            curl_close($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code < 200 || $http_code >= 300) {
            $error = 'Gemini API HTTP error: ' . $http_code;
            error_log($error);
            return null;
        }
    } else {
        // Fallback to file_get_contents if cURL is not available
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n" .
                            'Authorization: Bearer ' . GEMINI_API_KEY . "\r\n",
                'content' => json_encode($payload),
                'ignore_errors' => true
            ]
        ]);
        $response = @file_get_contents(GEMINI_API_ENDPOINT, false, $context);

        if (isset($http_response_header[0]) &&
            preg_match('#^HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
            $http_code = (int)$m[1];
            if ($http_code < 200 || $http_code >= 300) {
                $error = 'Gemini API HTTP error: ' . $http_code;
                error_log('Gemini API HTTP error: ' . $http_code . ' (using file_get_contents)');
                return null;
            }
        }

        if ($response === false) {
            $error = 'Gemini API fopen error';
            error_log($error);
            return null;
        }
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        $error = 'Gemini API decode error';
        error_log($error);
    }
    return $decoded;
}

/**
 * Genera un resumen de un texto utilizando la API de Gemini (o el simulador
 * si la configuración es de ejemplo).
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

    $payload = _build_gemini_payload($prompt);

    $error = null;
    $api_response = _call_gemini_api($payload, $error);
    $parsed_text = _parse_gemini_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text; // It's an error message, return as is.
    }
    // It's successful, non-empty text
    return nl2br(htmlspecialchars($parsed_text));
}

/**
 * Placeholder para una función que simularía una traducción inteligente.
 *
 * @param string $content_id Identificador del contenido (ej. 'atapuerca_main_text').
 * @param string $target_language Código del idioma objetivo (ej. 'en-ai', 'fr-ai').
 * @param string $original_sample_text Un extracto del texto original para incluir en la demo. O el texto completo si se desea devolverlo para 'es'.
 * @return string El texto "traducido" de demostración o el texto original si target_language es 'es'.
 */
function translate_with_gemini(string $content_id, string $target_language, string $original_sample_text = ''): string {
    if ($target_language === 'es') {
        // Si el objetivo es español, se asume que se quiere restaurar el original.
        // El JavaScript debería tener el contenido original completo.
        // Esta función, si es llamada con 'es', simplemente devuelve el sample/original que se le pasó.
        return $original_sample_text;
    }

    $original_snippet = !empty($original_sample_text) ? htmlspecialchars(substr(strip_tags($original_sample_text), 0, 70)) . "..." : "el contenido original";

    $outputText = "<div class='ai-translation-box'>";
    $outputText .= "<p><em>Traducción IA (Demostración) para: " . htmlspecialchars($content_id) . "</em></p>";

    switch ($target_language) {
        case 'en-ai':
            $outputText .= "<p><strong>Simulated English Translation:</strong> This demonstrates where AI-generated English text would appear. The original Spanish content started with: '<em>" . $original_snippet . "</em>'.</p>";
            $outputText .= "<p>In a production system, the full text would be processed by an advanced neural machine translation model to provide an accurate and nuanced English version.</p>";
            break;
        case 'fr-ai':
            $outputText .= "<p><strong>Traduction Française Simulée :</strong> Ceci montre où le texte français généré par l'IA apparaîtrait. Le contenu original en espagnol commençait par : '<em>" . $original_snippet . "</em>'.</p>";
            $outputText .= "<p>Dans un système de production, le texte intégral serait traité par un modèle avancé de traduction automatique neuronale pour fournir une version française précise et nuancée.</p>";
            break;
        case 'de-ai':
            $outputText .= "<p><strong>Simulierte Deutsche Übersetzung:</strong> Hier würde der von KI generierte deutsche Text erscheinen. Der ursprüngliche spanische Inhalt begann mit: '<em>" . $original_snippet . "</em>'.</p>";
            $outputText .= "<p>In einem Produktivsystem würde der vollständige Text von einem fortgeschrittenen neuronalen Übersetzungsmodell verarbeitet, um eine präzise und nuancierte deutsche Version bereitzustellen.</p>";
            break;
        // No hay caso 'default' o 'es' aquí porque ya se manejó al inicio de la función.
    }
    $outputText .= "<p class='ai-note'><em>(Esta es una simulación. La funcionalidad de traducción real con IA está pendiente de implementación).</em></p>";
    $outputText .= "</div>";
    return $outputText;
}

/**
 * Genera una traducción real mediante la API de Gemini (o el simulador).
 * Devuelve solo la traducción en el idioma solicitado.
 *
 * @param string $text            Texto de origen en castellano.
 * @param string $target_language Código del idioma de destino, ej. "en".
 * @return string Traducción generada o mensaje de error.
 */
function get_ai_translation(string $text, string $target_language): string {
    if (empty(trim($text))) {
        return "Error: No se proporcionó texto a traducir.";
    }

    $prompt = "Traduce el siguiente texto al idioma '" . $target_language . "'. Devuelve solo la traducción:\n\n\"" . $text . "\"";

    $payload = _build_gemini_payload($prompt);

    $error = null;
    $api_response = _call_gemini_api($payload, $error);
    $parsed_text = _parse_gemini_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text; // It's an error message, return as is.
    }
    // It's successful, non-empty text
    return nl2br(htmlspecialchars($parsed_text));
}



/**
 * Genera una respuesta genérica para el chat utilizando Gemini.
 *
 * @param string $prompt Texto introducido por el usuario.
 * @return string Respuesta generada o mensaje de error.
 */
function get_ai_chat_response(string $prompt): string {
    if (empty(trim($prompt))) {
        return "Error: No se proporcionó prompt.";
    }

    $payload = _build_gemini_payload($prompt);

    $error = null;
    $api_response = _call_gemini_api($payload, $error);
    $parsed_text = _parse_gemini_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text; // It's an error message, return as is.
    }
    // It's successful, non-empty text
    return nl2br(htmlspecialchars($parsed_text));
}



/**
 * Genera un breve informe de investigación sobre un tema.
 * @param string $query Tema o pregunta a investigar.
 * @return string Resumen generado o mensaje de error.
 */
function get_ai_research(string $query): string {
    if (empty(trim($query))) {
        return "Error: No se proporcionó tema de investigación.";
    }
    $prompt = "Investiga brevemente el siguiente tema y ofrece un resumen conciso con datos clave. Tema: \"" . $query . "\"";
    $payload = _build_gemini_payload($prompt);
    $error = null;
    $api_response = _call_gemini_api($payload, $error);
    $parsed_text = _parse_gemini_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text; // It's an error message, return as is.
    }
    // It's successful, non-empty text
    return nl2br(htmlspecialchars($parsed_text));
}

/**
 * Devuelve un enlace de búsqueda web para la consulta dada.
 * @param string $query Consulta a buscar en la web.
 * @return string HTML con el enlace de búsqueda.
 */
function get_web_search_results(string $query): string {
    if (empty(trim($query))) {
        return "Error: No se proporcionó consulta de búsqueda.";
    }
    $url = 'https://www.google.com/search?q=' . rawurlencode($query);
    $html = '<p>Resultados de búsqueda disponibles en <a href="' . $url . '" target="_blank" rel="noopener">Google</a>.</p>';
    return $html;
}

?>
