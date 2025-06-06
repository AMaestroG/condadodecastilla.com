<?php
// includes/gemini_api_client.php

require_once __DIR__ . '/../config/ai_config.php'; // Load the API key

if (!defined('GEMINI_API_CLIENT_LOADED')) {
    define('GEMINI_API_CLIENT_LOADED', true);
}

/**
 * Sends a request to the Gemini API.
 *
 * @param string $model The model to use (e.g., 'gemini-pro').
 * @param array $payload The payload to send to the API, typically containing 'contents'.
 * @param string $apiKey The API key for authentication. Defaults to GEMINI_API_KEY from config.
 * @return array|null The API response as an associative array, or null on error.
 */
function call_gemini_api(string $model, array $payload, string $apiKey = GEMINI_API_KEY): ?array {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    // It's good practice to set a timeout for API calls
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 seconds to connect
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);      // 30 seconds for the whole operation

    $response_body = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        error_log("Gemini API cURL Error: " . $curl_error);
        return null;
    }

    if ($http_code >= 200 && $http_code < 300) {
        $decoded_response = json_decode($response_body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Gemini API JSON Decode Error: " . json_last_error_msg());
            error_log("Gemini API Raw Response: " . $response_body);
            return null;
        }
        // Check for API-specific errors in the response body
        if (isset($decoded_response['error'])) {
            error_log("Gemini API Error: " . $decoded_response['error']['message']);
            return null;
        }
        return $decoded_response;
    } else {
        error_log("Gemini API HTTP Error: Code " . $http_code . " - Response: " . $response_body);
        return null;
    }
}

/**
 * Generates text using a simple text prompt with the Gemini API.
 *
 * @param string $prompt The text prompt.
 * @param string $model The model to use (e.g., 'gemini-pro').
 * @return string|null The generated text content or null on error/if no text generated.
 */
function generate_text_gemini(string $prompt, string $model = 'gemini-pro'): ?string {
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        // Optional: Add generationConfig if needed for temperature, maxOutputTokens etc.
        // 'generationConfig' => [
        //   'temperature' => 0.7,
        //   'maxOutputTokens' => 1000,
        // ]
    ];

    $response = call_gemini_api($model, $payload);

    if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        return $response['candidates'][0]['content']['parts'][0]['text'];
    }

    // Log if the expected structure is not found
    if ($response) {
        error_log("Gemini API: Unexpected response structure for text generation. Full response: " . json_encode($response));
    }

    return null;
}
?>
