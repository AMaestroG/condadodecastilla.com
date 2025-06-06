<?php
// scripts/evaluate_content_ai.php

// Allow running from CLI
if (php_sapi_name() !== 'cli' && php_sapi_name() !== 'cgi-fcgi') { // cgi-fcgi for some cron setups
    // Optional: Add security if web-triggerable later
     die("This script is primarily intended for CLI execution or trusted cron jobs.");
}

// Adjust include paths for CLI execution from repository root
require_once __DIR__ . '/../dashboard/db_connect.php'; // $pdo
require_once __DIR__ . '/../includes/ai_utils.php';   // generate_text_gemini

echo "Starting content evaluation script...\n";
ini_set('display_errors', 1); // Show errors for CLI
error_reporting(E_ALL);
ini_set('max_execution_time', 0); // No time limit for CLI script
set_time_limit(0);


try {
    $stmt_texts = $pdo->query("SELECT text_id, text_content FROM site_texts");
    $texts_to_evaluate = $stmt_texts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching texts from site_texts: " . $e->getMessage() . "\n";
    error_log("AI Evaluation Script DB Error: " . $e->getMessage());
    exit(1);
}

if (empty($texts_to_evaluate)) {
    echo "No texts found in site_texts to evaluate.\n";
    exit(0);
}

echo "Found " . count($texts_to_evaluate) . " text(s) to evaluate.\n";

foreach ($texts_to_evaluate as $text_item) {
    $text_id = $text_item['text_id'];
    $content = $text_item['text_content'];

    if (empty(trim($content))) {
        echo "Skipping empty content for text_id: $text_id\n";
        continue;
    }
    // Limit content length to avoid overly long API requests / high costs
    $max_content_length = 15000; // Approx 4k-5k tokens, adjust as needed
    if (mb_strlen($content) > $max_content_length) {
        $content = mb_substr($content, 0, $max_content_length);
        echo "Trimmed content for text_id: $text_id due to length.\n";
    }


    echo "Evaluating text_id: $text_id ... ";

    // Note: Gemini might have a preferred way to request JSON output if it supports it directly.
    // This prompt tries to force it.
    $prompt = <<<PROMPT
Analiza el siguiente texto de una página web. Proporciona una evaluación detallada. Responde en un formato JSON válido que incluya las siguientes claves EXACTAS:
- "clarity_score": Un puntaje entero del 1 al 10 (10 es el mejor) sobre la claridad y facilidad de comprensión.
- "engagement_score": Un puntaje entero del 1 al 10 (10 es el mejor) sobre qué tan atractivo o interesante es el texto.
- "seo_score": Un puntaje entero del 1 al 10 (10 es el mejor) sobre la optimización para motores de búsqueda (considerando uso de palabras clave, estructura, etc.).
- "factual_accuracy_score": Un puntaje entero del 1 al 10 (10 es el mejor) sobre la aparente exactitud factual. Si no puedes evaluarlo, usa null.
- "overall_assessment": Un breve resumen (2-3 frases) de tu evaluación general (string).
- "positive_points": Un array de strings con 2-3 puntos fuertes del texto. (Ej: ["Bien estructurado", "Lenguaje claro"])
- "areas_for_improvement": Un array de strings con 2-3 sugerencias específicas para mejorar el texto. (Ej: ["Expandir sección X", "Añadir ejemplos"])
- "suggested_keywords": Un array de strings con 3-5 palabras clave relevantes para este texto. (Ej: ["historia", "castilla", "siglo X"])

Texto a analizar:
---
{$content}
---
Asegúrate de que la respuesta sea únicamente el objeto JSON válido, sin texto adicional antes o después, ni markdown.
PROMPT;

    $raw_ai_response = generate_text_gemini($prompt);

    if (!$raw_ai_response) {
        echo "Failed to get response from AI for text_id: $text_id.\n";
        error_log("AI Evaluation Error: No response from AI for text_id: $text_id.");
        continue;
    }

    $cleaned_response = $raw_ai_response;
    if (strpos(trim($cleaned_response), '```json') === 0) {
        $cleaned_response = preg_replace('/^```json\s*/', '', $cleaned_response);
        $cleaned_response = preg_replace('/\s*```$/', '', $cleaned_response);
    }
    $cleaned_response = trim($cleaned_response);

    $eval_data = json_decode($cleaned_response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Failed to decode JSON response from AI for text_id: $text_id. Error: " . json_last_error_msg() . ". Trying to extract from potential markdown...\n";
        // Attempt to extract JSON from within ```json ... ``` if not caught above
        if (preg_match('/```json\s*(.*?)\s*```/s', $raw_ai_response, $matches)) {
            $cleaned_response = trim($matches[1]);
            $eval_data = json_decode($cleaned_response, true);
        }
        if (json_last_error() !== JSON_ERROR_NONE) {
             echo "Still failed to decode JSON for text_id: $text_id. Error: " . json_last_error_msg() . "\n";
             error_log("AI Evaluation JSON Error for text_id: $text_id. Error: " . json_last_error_msg() . ". Cleaned Response: " . $cleaned_response . ". Raw Response: " . $raw_ai_response);
            // Store raw response anyway for debugging
            try {
                $sql_insert_raw = "INSERT INTO ai_content_evaluations
                    (text_id_fk, evaluated_at, evaluation_prompt, raw_ai_response, overall_assessment)
                    VALUES (:text_id_fk, CURRENT_TIMESTAMP, :evaluation_prompt, :raw_ai_response, :overall_assessment)";
                $stmt_insert_raw = $pdo->prepare($sql_insert_raw);
                $stmt_insert_raw->execute([
                    ':text_id_fk' => $text_id,
                    ':evaluation_prompt' => $prompt,
                    ':raw_ai_response' => $raw_ai_response,
                    ':overall_assessment' => 'Error: Could not parse AI JSON response.'
                ]);
            } catch (PDOException $e) {
                error_log("DB Error storing raw AI error response for text_id $text_id: " . $e->getMessage());
            }
            continue;
        } else {
            echo "Successfully decoded JSON after manual extraction for text_id: $text_id.\n";
        }
    }

    $positive_points_json = null;
    if (isset($eval_data['positive_points']) && is_array($eval_data['positive_points'])) {
        $positive_points_json = json_encode($eval_data['positive_points']);
    } elseif (isset($eval_data['positive_points']) && is_string($eval_data['positive_points'])) {
        $positive_points_json = json_encode([$eval_data['positive_points']]); // Wrap single string in array
    }

    $areas_for_improvement_json = null;
    if (isset($eval_data['areas_for_improvement']) && is_array($eval_data['areas_for_improvement'])) {
        $areas_for_improvement_json = json_encode($eval_data['areas_for_improvement']);
    } elseif (isset($eval_data['areas_for_improvement']) && is_string($eval_data['areas_for_improvement'])) {
        $areas_for_improvement_json = json_encode([$eval_data['areas_for_improvement']]);
    }

    $suggested_keywords_str = null;
    if (isset($eval_data['suggested_keywords']) && is_array($eval_data['suggested_keywords'])) {
        $suggested_keywords_str = implode(', ', $eval_data['suggested_keywords']);
    } elseif (isset($eval_data['suggested_keywords']) && is_string($eval_data['suggested_keywords'])) {
        $suggested_keywords_str = $eval_data['suggested_keywords'];
    }

    $insert_params = [
        ':text_id_fk' => $text_id,
        ':evaluation_prompt' => $prompt,
        ':raw_ai_response' => $raw_ai_response,
        ':clarity_score' => $eval_data['clarity_score'] ?? null,
        ':engagement_score' => $eval_data['engagement_score'] ?? null,
        ':seo_score' => $eval_data['seo_score'] ?? null,
        ':factual_accuracy_score' => $eval_data['factual_accuracy_score'] ?? null,
        ':overall_assessment' => $eval_data['overall_assessment'] ?? null,
        ':positive_points' => $positive_points_json,
        ':areas_for_improvement' => $areas_for_improvement_json,
        ':suggested_keywords' => $suggested_keywords_str,
    ];

    try {
        // Delete previous evaluations for this text_id to only keep the latest one
        $stmt_delete = $pdo->prepare("DELETE FROM ai_content_evaluations WHERE text_id_fk = :text_id_fk");
        $stmt_delete->bindParam(':text_id_fk', $text_id);
        $stmt_delete->execute();

        $sql_insert = "INSERT INTO ai_content_evaluations (
            text_id_fk, evaluated_at, evaluation_prompt, raw_ai_response,
            clarity_score, engagement_score, seo_score, factual_accuracy_score,
            overall_assessment, positive_points, areas_for_improvement, suggested_keywords
        ) VALUES (
            :text_id_fk, CURRENT_TIMESTAMP, :evaluation_prompt, :raw_ai_response,
            :clarity_score, :engagement_score, :seo_score, :factual_accuracy_score,
            :overall_assessment, :positive_points, :areas_for_improvement, :suggested_keywords
        )";

        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute($insert_params);
        echo "Stored evaluation for text_id: $text_id.\n";

    } catch (PDOException $e) {
        echo "DB Error storing evaluation for text_id $text_id: " . $e->getMessage() . "\n";
        error_log("DB Error storing evaluation for text_id $text_id: " . $e->getMessage() . ". Params: " . json_encode($insert_params));
    }
    // Optional: Add a small delay to avoid hitting API rate limits
    if (count($texts_to_evaluate) > 1) sleep(1);
}

echo "Content evaluation script finished.\n";
?>
