<?php
// includes/ai_utils.php

require_once __DIR__ . '/env_loader.php';
// Cargar variables de entorno desde .env

require_once __DIR__ . '/session.php';
ensure_session_started();

// Claude API Settings
if (!defined('CLAUDE_API_KEY')) {
    $envKeyClaude = getenv('CLAUDE_API_KEY');
    define('CLAUDE_API_KEY', $envKeyClaude !== false ? $envKeyClaude : 'YOUR_CLAUDE_API_KEY_NOT_SET');
}
if (!defined('CLAUDE_API_ENDPOINT')) {
    $envEndpointClaude = getenv('CLAUDE_API_ENDPOINT');
    define('CLAUDE_API_ENDPOINT', $envEndpointClaude !== false ? $envEndpointClaude : 'https://api.anthropic.com/v1/messages');
}
if (!defined('CLAUDE_MODEL')) {
    $envModelClaude = getenv('CLAUDE_MODEL');
    define('CLAUDE_MODEL', $envModelClaude !== false ? $envModelClaude : 'claude-3-haiku-20240307');
}
if (!defined('CLAUDE_API_VERSION')) {
    $envVersionClaude = getenv('CLAUDE_API_VERSION');
    define('CLAUDE_API_VERSION', $envVersionClaude !== false ? $envVersionClaude : '2023-06-01');
}
if (!defined('CLAUDE_MAX_TOKENS')) {
    $envMaxTokensClaude = getenv('CLAUDE_MAX_TOKENS');
    define('CLAUDE_MAX_TOKENS', $envMaxTokensClaude !== false ? (int)$envMaxTokensClaude : 1024);
}


if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

if (CLAUDE_API_KEY === 'YOUR_CLAUDE_API_KEY_NOT_SET' || CLAUDE_API_KEY === '') {
    error_log('CLAUDE_API_KEY is missing. AI functions will use a simulator or fail.');
    // Consider adding a session notice if you have frontend UI for it
    // $_SESSION['claude_api_key_notice'] = 'La clave de la API de Claude no está configurada. Las funciones de IA usan un simulador.';
}


/**
 * Placeholder para una función que generaría etiquetas sugeridas por IA.
 * (No utiliza la API de IA, se mantiene como estaba)
 * @param string $content_key Un identificador para el contenido a etiquetar.
 * @return array Un array de strings con las etiquetas sugeridas.
 */
function get_suggested_tags_placeholder(string $content_key): array {
    if ($content_key === 'atapuerca' || $content_key === 'Contenido de Atapuerca') {
        return ['Prehistoria', 'Evolución Humana', 'Arqueología', 'Yacimientos UNESCO', 'Homo Antecessor', 'Burgos', 'Paleontología'];
    }
    return ['General', 'Contenido Interesante', 'Historia', 'Web'];
}

/**
 * Simulates a call to the Claude API.
 *
 * @param array $payload The request body to send to the API.
 * @return array|null The decoded API response as an array, or null on simulated error.
 */
function _call_claude_api_simulator(array $payload): ?array {
    $simulated_success = true;

    if (!$simulated_success) {
        error_log("Simulated Claude API call failed.");
        return ['type' => 'error', 'error' => ['type' => 'simulated_error', 'message' => 'Simulated API call failure.']];
    }

    $prompt_text = "";
    if (isset($payload['messages'][0]['content'])) {
        $prompt_text = $payload['messages'][0]['content'];
    }

    $generated_text = "Este es un texto simulado generado por la API de Claude en respuesta a un prompt que comenzaba con: '" . substr(htmlspecialchars($prompt_text), 0, 100) . "...'. ";
    if (stripos($prompt_text, "resume") !== false || stripos($prompt_text, "resumen") !== false) {
        $generated_text .= "Este parece ser un resumen. Los puntos clave incluyen X, Y y Z.";
    } elseif (stripos($prompt_text, "traduce") !== false || stripos($prompt_text, "translate") !== false) {
        $generated_text .= "Esta es la porción traducida simulada para Claude.";
    }

    return [
        'id' => 'msg_sim_' . uniqid(),
        'type' => 'message',
        'role' => 'assistant',
        'model' => CLAUDE_MODEL,
        'content' => [
            [
                'type' => 'text',
                'text' => $generated_text
            ]
        ],
        'stop_reason' => 'end_turn',
        'usage' => [
            'input_tokens' => 10, // Simulated
            'output_tokens' => 20 // Simulated
        ]
    ];
}

/**
 * Builds the payload for the Claude Messages API.
 *
 * @param string $prompt The user's prompt.
 * @param string $system_prompt Optional system prompt.
 * @return array The payload structure.
 */
function _build_claude_payload(string $prompt, string $system_prompt = ''): array {
    $payload = [
        'model' => CLAUDE_MODEL,
        'max_tokens' => CLAUDE_MAX_TOKENS,
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ]
    ];
    if (!empty(trim($system_prompt))) {
        $payload['system'] = $system_prompt;
    }
    return $payload;
}

/**
 * Parses the response from the Claude API.
 *
 * @param array|null $api_response The decoded API response.
 * @param string|null $call_error Error message from the API call itself (e.g., cURL error).
 * @return string The extracted text content or an error message prefixed with "Error:".
 */
function _parse_claude_response(?array $api_response, ?string $call_error): string {
    if ($call_error !== null) {
        return "Error: " . $call_error;
    }

    if ($api_response === null) {
        return "Error: API call failed without a specific message.";
    }

    if (isset($api_response['type']) && $api_response['type'] === 'error') {
        $error_message = $api_response['error']['message'] ?? 'Unknown API error.';
        return "Error de la API de IA: " . htmlspecialchars($error_message);
    }

    if (isset($api_response['content'][0]['text'])) {
        $text = trim($api_response['content'][0]['text']);
        if (empty($text)) {
            return "Error: El texto generado por la IA estaba vacío.";
        }
        return $text; // Return raw text
    }

    return "Error: Respuesta inesperada del servicio de IA.";
}

/**
 * Calls the Claude API using cURL or uses the simulator if the
 * configuration is not set.
 *
 * @param array $payload Request body for the API.
 * @param string|null &$error Error message if the call fails.
 * @return array|null Decoded response or null on error.
 */
function _call_claude_api(array $payload, ?string &$error = null): ?array {
    if (CLAUDE_API_KEY === 'YOUR_CLAUDE_API_KEY_NOT_SET' || CLAUDE_API_KEY === '') {
        return _call_claude_api_simulator($payload);
    }

    $headers = [
        'x-api-key: ' . CLAUDE_API_KEY,
        'anthropic-version: ' . CLAUDE_API_VERSION,
        'Content-Type: application/json'
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init(CLAUDE_API_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response_body = curl_exec($ch);
        if ($response_body === false) {
            $error = 'Claude API cURL error: ' . curl_error($ch);
            error_log($error);
            curl_close($ch);
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code < 200 || $http_code >= 300) {
            $error = 'Claude API HTTP error: ' . $http_code . '. Response: ' . $response_body;
            error_log($error);
            // Attempt to decode error response from Claude
            $decoded_error = json_decode($response_body, true);
            return $decoded_error ?? ['type' => 'error', 'error' => ['type' => 'http_error', 'message' => 'HTTP error ' . $http_code]];
        }
    } else {
        // Fallback to file_get_contents if cURL is not available
        $context_options = [
            'http' => [
                'method'  => 'POST',
                'header'  => implode("\r\n", $headers) . "\r\n",
                'content' => json_encode($payload),
                'ignore_errors' => true // To capture error responses
            ]
        ];
        $context = stream_context_create($context_options);
        $response_body = @file_get_contents(CLAUDE_API_ENDPOINT, false, $context);

        if ($response_body === false) {
            $error = 'Claude API fopen error';
            error_log($error);
            return null;
        }

        // Try to get HTTP status code from $http_response_header (available in this scope)
        $http_code = 0;
        if (isset($http_response_header[0]) && preg_match('#^HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
            $http_code = (int)$m[1];
        }

        if ($http_code < 200 || $http_code >= 300) {
            $error = 'Claude API HTTP error: ' . $http_code . ' (using file_get_contents). Response: ' . $response_body;
            error_log($error);
            $decoded_error = json_decode($response_body, true);
            return $decoded_error ?? ['type' => 'error', 'error' => ['type' => 'http_error', 'message' => 'HTTP error ' . $http_code]];
        }
    }

    $decoded = json_decode($response_body, true);
    if ($decoded === null) {
        $error = 'Claude API JSON decode error. Response: ' . $response_body;
        error_log($error);
        return null; // Or return a structured error if Claude API guarantees JSON errors
    }
    return $decoded;
}

/**
 * Generates a summary of text using the Claude API.
 *
 * @param string $text_to_summarize The text to summarize.
 * @return string The generated summary or an error message.
 */
function get_real_ai_summary(string $text_to_summarize): string {
    if (empty(trim($text_to_summarize))) {
        return "Error: No se proporcionó texto para resumir.";
    }

    $system_prompt = "Eres un asistente experto en resumir textos, especialmente aquellos relacionados con historia y cultura. Genera resúmenes concisos y claros.";
    $user_prompt = "Por favor, resume el siguiente texto. Enfócate en los puntos clave relevantes para un lector interesado en historia y cultura. El resumen debe ser adecuado para mostrar en una página web. Texto a resumir:\n\n\"" . $text_to_summarize . "\"";

    $payload = _build_claude_payload($user_prompt, $system_prompt);

    $error = null;
    $api_response = _call_claude_api($payload, $error);
    $parsed_text = _parse_claude_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text;
    }
    return nl2br(htmlspecialchars($parsed_text));
}

/**
 * Placeholder function for intelligent translation simulation.
 * This version is kept for compatibility if some UI elements still call it,
 * but should ideally be replaced by calls to get_ai_translation.
 *
 * @param string $content_id Content identifier (e.g., 'atapuerca_main_text').
 * @param string $target_language Target language code (e.g., 'en-ai', 'fr-ai').
 * @param string $original_sample_text An excerpt of the original text.
 * @return string The "translated" demo text or original text.
 */
function translate_with_ai_placeholder(string $content_id, string $target_language, string $original_sample_text = ''): string {
    if ($target_language === 'es') {
        return $original_sample_text;
    }

    $original_snippet = !empty($original_sample_text) ? htmlspecialchars(substr(strip_tags($original_sample_text), 0, 70)) . "..." : "el contenido original";
    $ai_provider = "Claude"; // Updated provider name

    $outputText = "<div class='ai-translation-box'>";
    $outputText .= "<p><em>Traducción IA (Demostración con {$ai_provider}) para: " . htmlspecialchars($content_id) . "</em></p>";

    switch ($target_language) {
        case 'en-ai':
            $outputText .= "<p><strong>Simulated English Translation:</strong> This demonstrates where {$ai_provider}-generated English text would appear. Original: '<em>" . $original_snippet . "</em>'.</p>";
            break;
        case 'fr-ai':
            $outputText .= "<p><strong>Traduction Française Simulée :</strong> Ceci montre où le texte français généré par {$ai_provider} apparaîtrait. Original: '<em>" . $original_snippet . "</em>'.</p>";
            break;
        // Add more cases if needed
    }
    $outputText .= "<p class='ai-note'><em>(Esta es una simulación. La funcionalidad de traducción real con {$ai_provider} está activa a través de otras funciones).</em></p>";
    $outputText .= "</div>";
    return $outputText;
}

/**
 * Generates a translation using the Claude API.
 *
 * @param string $text Text to translate (assumed to be Spanish).
 * @param string $target_language Target language code (e.g., "en", "fr").
 * @return string Generated translation or an error message.
 */
function get_ai_translation(string $text, string $target_language): string {
    if (empty(trim($text))) {
        return "Error: No se proporcionó texto a traducir.";
    }
    // Mapping common language codes to full names for better prompts
    $language_map = [
        'en' => 'inglés', 'fr' => 'francés', 'de' => 'alemán', 'pt' => 'portugués',
        'it' => 'italiano', 'gl' => 'gallego',
        // Add more as needed
    ];
    $target_language_name = $language_map[$target_language] ?? $target_language;

    $system_prompt = "Eres un traductor experto. Tu tarea es traducir el texto proporcionado del español al idioma solicitado, manteniendo la naturalidad y precisión.";
    $user_prompt = "Traduce el siguiente texto al " . htmlspecialchars($target_language_name) . ". Devuelve únicamente la traducción:\n\n\"" . $text . "\"";

    $payload = _build_claude_payload($user_prompt, $system_prompt);

    $error = null;
    $api_response = _call_claude_api($payload, $error);
    $parsed_text = _parse_claude_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text;
    }
    return nl2br(htmlspecialchars($parsed_text));
}


/**
 * Generates a generic chat response using Claude.
 *
 * @param string $prompt User's input text.
 * @return string Generated response or an error message.
 */
function get_ai_chat_response(string $prompt): string {
    if (empty(trim($prompt))) {
        return "Error: No se proporcionó prompt.";
    }
    // For general chat, a system prompt can guide Claude's persona if desired.
    // Example: $system_prompt = "Eres un asistente virtual amigable y servicial.";
    // For now, no specific system prompt for generic chat.
    $payload = _build_claude_payload($prompt);

    $error = null;
    $api_response = _call_claude_api($payload, $error);
    $parsed_text = _parse_claude_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text;
    }
    return nl2br(htmlspecialchars($parsed_text));
}


/**
 * Generates a brief research report on a topic using Claude.
 * @param string $query Topic or question to research.
 * @return string Generated summary or an error message.
 */
function get_ai_research(string $query): string {
    if (empty(trim($query))) {
        return "Error: No se proporcionó tema de investigación.";
    }
    $system_prompt = "Eres un asistente de investigación. Proporciona información concisa y datos clave sobre el tema solicitado.";
    $user_prompt = "Investiga brevemente el siguiente tema y ofrece un resumen conciso con datos clave. Tema: \"" . $query . "\"";

    $payload = _build_claude_payload($user_prompt, $system_prompt);
    $error = null;
    $api_response = _call_claude_api($payload, $error);
    $parsed_text = _parse_claude_response($api_response, $error);

    if (strpos($parsed_text, "Error:") === 0) {
        return $parsed_text;
    }
    return nl2br(htmlspecialchars($parsed_text));
}

/**
 * Returns a web search link for the given query.
 * (No AI API call, remains unchanged)
 * @param string $query Query to search on the web.
 * @return string HTML with the search link.
 */
function get_web_search_results(string $query): string {
    if (empty(trim($query))) {
        return "Error: No se proporcionó consulta de búsqueda.";
    }
    $url = 'https://www.google.com/search?q=' . rawurlencode($query);
    $html = '<p>Resultados de búsqueda disponibles en <a href="' . $url . '" target="_blank" rel="noopener">Google</a>.</p>';
    return $html;
}

// The function `translate_with_gemini` was directly renamed to `translate_with_ai_placeholder`.
// Any remaining calls would need to be updated to `translate_with_ai_placeholder` (for simulated text)
// or `get_ai_translation` (for actual AI translation).
// The conditional block that was here previously is no longer necessary.

?>
