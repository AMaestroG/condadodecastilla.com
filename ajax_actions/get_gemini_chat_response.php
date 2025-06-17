<?php
require_once __DIR__ . '/../includes/ai_utils.php'; // For GEMINI_API_KEY and Gemini functions
require_once __DIR__ . '/../includes/env_loader.php'; // Ensure environment variables are loaded

header('Content-Type: application/json');

// Get the prompt from the POST request
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (empty(trim($prompt))) {
    echo json_encode(['success' => false, 'error' => 'El prompt no puede estar vacío.']);
    exit;
}

// Call the function from ai_utils.php that interacts with Gemini for chat
// (This function should already be set up to use GEMINI_API_KEY)
$ai_response_text = get_ai_chat_response($prompt); // Assumes get_ai_chat_response handles errors internally and prefixes with "Error:"

$response = [];
if (strpos($ai_response_text, "Error:") === 0) {
    $response['success'] = false;
    // Remove "Error: " prefix, ensuring not to cut too much if "Error:" is part of the actual message.
    // A slightly safer way is to check if the string actually starts with "Error: "
    if (substr($ai_response_text, 0, strlen("Error: ")) === "Error: ") {
        $response['error'] = substr($ai_response_text, strlen("Error: "));
    } else {
        $response['error'] = $ai_response_text; // Should not happen if get_ai_chat_response is consistent
    }
} else {
    $response['success'] = true;
    $response['response'] = $ai_response_text; // 'response' key as expected by js/ia-tools.js
}

echo json_encode($response);
?>
