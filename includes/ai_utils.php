<?php
// includes/ai_utils.php

// Cargar variables de entorno desde .env

// Read Gemini API settings from environment variables when available
if (!defined('GEMINI_API_KEY')) {
    $envKey = getenv('GEMINI_API_KEY');
    if ($envKey === false) {
        // Compatibility: allow the variable name 'GeminiAPI'
        $envKey = getenv('GeminiAPI');
    }
    define('GEMINI_API_KEY', $envKey !== false ? $envKey : 'TU_API_KEY_AQUI_CONFIGURACION_ENTORNO');
}
if (!defined('GEMINI_API_ENDPOINT')) {
    $envEndpoint = getenv('GEMINI_API_ENDPOINT');
    define('GEMINI_API_ENDPOINT', $envEndpoint !== false ? $envEndpoint : 'https://api.gemini.example.com/v1/generateContent');
}

if (!defined('AI_UTILS_LOADED')) {
    define('AI_UTILS_LOADED', true);
}

/**
 * Placeholder para una función que generaría un resumen inteligente.
 * En una implementación real, esto podría llamar a una API de IA,
 * procesar el texto localmente con un modelo, etc.
 *
 * @param string $content_key Un identificador para el contenido a resumir,
 *                            o potencialmente el texto completo si es corto.
 * @param string $full_text (Opcional) El texto completo a resumir.
 * @return string El resumen generado (o un placeholder).
 */
function get_smart_summary(string $content_key, string $full_text = ''): string {
    // Simulación de procesamiento
    $summary = "Resumen inteligente para '" . htmlspecialchars($content_key) . "': ";

    if (!empty($full_text)) {
        // Tomar las primeras ~250 caracteres como un resumen muy básico si se proporciona el texto completo.
        // strip_tags para evitar problemas si el contenido tiene HTML.
        $summary .= substr(strip_tags($full_text), 0, 250) . "...";
    } else {
        $summary .= "Este es un resumen de demostración generado por IA. En el futuro, aquí aparecería un extracto conciso y relevante del contenido principal, procesado por un modelo de lenguaje avanzado para destacar los puntos clave de la sección sobre " . htmlspecialchars($content_key) . ".";
    }

    return "<p><strong>" . $summary . "</strong></p><p><em>(Funcionalidad de resumen real con IA pendiente de implementación completa).</em></p>";
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
 * Llama a la API de Gemini utilizando cURL o usa el simulador si la
 * configuración sigue con valores de ejemplo.
 *
 * @param array $payload Cuerpo de la solicitud para la API.
 * @return array|null Respuesta decodificada o null si hay errores.
 */
function _call_gemini_api(array $payload, ?string &$error = null): ?array {
    if (GEMINI_API_KEY === 'TU_API_KEY_AQUI_CONFIGURACION_ENTORNO' ||
        GEMINI_API_ENDPOINT === 'https://api.gemini.example.com/v1/generateContent') {
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
                $error = $http_response_header[0];
                error_log('Gemini API HTTP error: ' . $error);
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

    $error = null;
    $api_response = _call_gemini_api($payload, $error);

    if ($api_response === null) {
        $msg = $error !== null ? $error : 'La llamada a la API de IA para el resumen falló.';
        return "Error: " . $msg;
    }

    // Procesar la respuesta recibida (adaptar según la estructura real de Gemini si es necesario)
    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $summary = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        // Podría ser necesario un post-procesamiento adicional aquí para limpiar el resumen.
        return !empty($summary) ? nl2br(htmlspecialchars($summary)) : "Error: El resumen generado por la IA estaba vacío.";
    } elseif (isset($api_response['error']['message'])) { // Manejo de errores de la API si los hubiera
         return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    } else {
        // Loggear la respuesta inesperada para depuración si es posible en un entorno real.
        // error_log("Respuesta inesperada de la API de IA: " . print_r($api_response, true));
        return "Error: Respuesta inesperada del servicio de resumen de IA.";
    }
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

    $outputText = "<div style='padding:15px; background-color:#e3f2fd; border:1px solid #bbdefb; border-radius:4px; margin-top:10px;'>";
    $outputText .= "<p style='font-size:0.9em; color:#0d47a1;'><em>Traducción IA (Demostración) para: " . htmlspecialchars($content_id) . "</em></p>";

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
    $outputText .= "<p style='font-size:0.8em; color:#1976d2; margin-top:10px;'><em>(Esta es una simulación. La funcionalidad de traducción real con IA está pendiente de implementación).</em></p>";
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

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $error = null;
    $api_response = _call_gemini_api($payload, $error);

    if ($api_response === null) {
        $msg = $error !== null ? $error : 'La llamada a la API de IA para la traducción falló.';
        return "Error: " . $msg;
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $translation = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return !empty($translation) ? nl2br(htmlspecialchars($translation)) : "Error: La traducción generada por la IA estaba vacía.";
    } elseif (isset($api_response['error']['message'])) {
        return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    }

    return "Error: Respuesta inesperada del servicio de traducción de IA.";
}

/**
 * Genera una versión corregida de un texto usando la API de Gemini.
 *
 * @param string $text Texto original en castellano.
 * @return string Texto corregido o mensaje de error.
 */
function get_ai_correction(string $text): string {
    if (empty(trim($text))) {
        return "Error: No se proporcionó texto a corregir.";
    }

    $prompt = "Corrige ortografía y gramática del siguiente texto. Devuelve solo la versión corregida:\n\n\"" . $text . "\"";

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $error = null;
    $api_response = _call_gemini_api($payload, $error);

    if ($api_response === null) {
        $msg = $error !== null ? $error : 'La llamada a la API de IA para la corrección falló.';
        return "Error: " . $msg;
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $correction = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return !empty($correction) ? nl2br(htmlspecialchars($correction)) : "Error: La corrección generada por la IA estaba vacía.";
    } elseif (isset($api_response['error']['message'])) {
        return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    }

    return "Error: Respuesta inesperada del servicio de corrección de IA.";
}

/**
 * Genera una respuesta para el chat histórico utilizando nuevo4.md como contexto.
 * Si la pregunta no está relacionada con historia, devuelve un aviso.
 *
 * @param string $question Pregunta del usuario.
 * @return string Respuesta generada o mensaje de error.
 */
function get_history_chat_response(string $question): string {
    if (empty(trim($question))) {
        return "Error: No se proporcionó pregunta.";
    }

    $context = @file_get_contents(__DIR__ . '/../nuevo4.md');
    if ($context === false) { $context = ''; }
    $context = mb_substr($context, 0, 5000);

    $prompt = "Responde únicamente preguntas sobre historia utilizando el siguiente contexto. Si la pregunta no es histórica, indica que solo respondes sobre historia.\n\nContexto:\n" . $context . "\n\nPregunta: \"" . $question . "\"";

    $payload = [
        'contents' => [
            [ 'parts' => [ ['text' => $prompt] ] ]
        ]
    ];

    $error = null;
    $api_response = _call_gemini_api($payload, $error);

    if ($api_response === null) {
        $msg = $error !== null ? $error : 'La llamada a la API de IA para el chat falló.';
        return "Error: " . $msg;
    }

    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $answer = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return !empty($answer) ? nl2br(htmlspecialchars($answer)) : "Error: La respuesta generada por la IA estaba vacía.";
    } elseif (isset($api_response['error']['message'])) {
        return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    }

    return "Error: Respuesta inesperada del servicio de chat de IA.";
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
    $payload = [ 'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]] ];
    $error = null;
    $api_response = _call_gemini_api($payload, $error);
    if ($api_response === null) {
        $msg = $error !== null ? $error : 'La llamada a la API de IA para la investigación falló.';
        return "Error: " . $msg;
    }
    if (isset($api_response['candidates'][0]['content']['parts'][0]['text'])) {
        $text = trim($api_response['candidates'][0]['content']['parts'][0]['text']);
        return !empty($text) ? nl2br(htmlspecialchars($text)) : "Error: La investigación generada por la IA estaba vacía.";
    } elseif (isset($api_response['error']['message'])) {
        return "Error de la API de IA: " . htmlspecialchars($api_response['error']['message']);
    }
    return "Error: Respuesta inesperada del servicio de investigación de IA.";
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
