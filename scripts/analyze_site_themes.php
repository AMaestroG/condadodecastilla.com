<?php
// scripts/analyze_site_themes.php
if (php_sapi_name() !== 'cli' && php_sapi_name() !== 'cgi-fcgi') {
    die("This script is intended for CLI execution.");
}

require_once __DIR__ . '/../dashboard/db_connect.php'; // $pdo
require_once __DIR__ . '/../includes/ai_utils.php';   // generate_text_gemini

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0); // No time limit for CLI
date_default_timezone_set('Europe/Madrid'); // Set a default timezone

echo "Starting Site-Wide Thematic Analysis Script...\n";

$all_texts_data = [];
try {
    $stmt_texts = $pdo->query("SELECT text_id, text_content FROM site_texts WHERE TRIM(text_content) != ''");
    $all_texts_data = $stmt_texts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching texts: " . $e->getMessage() . "\n";
    error_log("Analyze Site Themes DB Error: " . $e->getMessage());
    exit(1);
}

if (empty($all_texts_data)) {
    echo "No text content found to analyze.\n";
    exit(0);
}

echo "Found " . count($all_texts_data) . " non-empty text items to analyze.\n";

$temp_themes_list = [];
$temp_entities_list = ['people' => [], 'places' => [], 'events' => []];
$processed_text_ids = [];
$total_token_estimate = 0; // Simple way to estimate, 1 char ~ 1 token (very rough)

$max_texts_for_individual_analysis = 50; // Process only up to X texts individually to manage API cost/time.
                                       // For more texts, a different strategy (sampling, larger batches) might be needed.
$texts_for_analysis_loop = array_slice($all_texts_data, 0, $max_texts_for_individual_analysis);
if (count($all_texts_data) > $max_texts_for_individual_analysis) {
    echo "Warning: Analyzing only the first " . $max_texts_for_individual_analysis . " texts due to limit set in script.\n";
}


foreach ($texts_for_analysis_loop as $text_item) {
    $text_id = $text_item['text_id'];
    $content = $text_item['text_content'];
    // Limit individual content length before sending to API
    $max_content_length_item = 15000;
    if (mb_strlen($content) > $max_content_length_item) {
        $content = mb_substr($content, 0, $max_content_length_item);
        echo "Trimmed content for text_id: $text_id for individual analysis.\n";
    }

    $processed_text_ids[] = $text_id;
    $total_token_estimate += mb_strlen($content);

    echo "Processing text_id: $text_id ... ";

    $prompt_item_analysis = <<<PROMPT
Analiza el siguiente texto. Tu objetivo es identificar:
1. Temas clave (devuelve como un array JSON de strings, por ejemplo: ["tema1", "tema2"]).
2. Entidades nombradas importantes (personas, lugares, eventos). Devuelve como un objeto JSON con claves 'people', 'places', 'events', cada una conteniendo un array JSON de strings (ej: {"people": ["nombre1"], "places": ["lugar1"], "events": ["evento1"]}).

Responde únicamente con un objeto JSON válido que contenga dos claves principales: "themes" y "entities", estructuradas como se describió. No incluyas explicaciones adicionales fuera del JSON.

Texto a analizar:
---
{$content}
---
PROMPT;

    $api_response_item = generate_text_gemini($prompt_item_analysis);

    if (!$api_response_item) {
        echo "Failed to get response from AI for item $text_id.\n";
        error_log("Analyze Themes: No AI response for text_id $text_id");
        if (count($texts_for_analysis_loop) > 1) sleep(1); // Check against $texts_for_analysis_loop
        continue;
    }

    $cleaned_item_response = trim($api_response_item);
    if (strpos($cleaned_item_response, '```json') === 0) {
        $cleaned_item_response = preg_replace('/^```json\s*/', '', $cleaned_item_response);
        $cleaned_item_response = preg_replace('/\s*```$/', '', $cleaned_item_response);
        $cleaned_item_response = trim($cleaned_item_response);
    }

    $decoded_item = json_decode($cleaned_item_response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Failed to decode item JSON for $text_id: " . json_last_error_msg() . ". Raw response: " . $api_response_item . "\n";
        error_log("Analyze Themes JSON Error for $text_id: " . json_last_error_msg() . ". Cleaned Response: " . $cleaned_item_response);
    } else {
        if (isset($decoded_item['themes']) && is_array($decoded_item['themes'])) {
            foreach ($decoded_item['themes'] as $theme) $temp_themes_list[] = trim(strtolower($theme));
        }
        if (isset($decoded_item['entities']) && is_array($decoded_item['entities'])) {
            foreach (['people', 'places', 'events'] as $type) {
                if (isset($decoded_item['entities'][$type]) && is_array($decoded_item['entities'][$type])) {
                    foreach ($decoded_item['entities'][$type] as $entity) $temp_entities_list[$type][] = trim(ucwords(strtolower($entity)));
                }
            }
        }
        echo "OK.\n";
    }
    if (count($texts_for_analysis_loop) > 1) sleep(1); // Rate limiting
}

// Aggregate and filter themes
$theme_counts = !empty($temp_themes_list) ? array_count_values($temp_themes_list) : [];
arsort($theme_counts);
$final_themes = array_keys(array_slice($theme_counts, 0, 20)); // Top 20 themes

// Aggregate and filter entities
$final_entities = [];
foreach (['people', 'places', 'events'] as $type) {
    $entity_counts = !empty($temp_entities_list[$type]) ? array_count_values($temp_entities_list[$type]) : [];
    arsort($entity_counts);
    $final_entities[$type] = array_keys(array_slice($entity_counts, 0, 20)); // Top 20 of each type
}

echo "Aggregated themes and entities.\n";

// Generate Overall Style Summary
$style_sample_texts = "";
$sample_count = 0;
// Ensure $all_texts_data is not empty before calling array_rand
$num_texts_for_style_sample = count($all_texts_data) > 0 ? min(5, count($all_texts_data)) : 0;

if ($num_texts_for_style_sample > 0) {
    $text_indices_for_style = array_rand($all_texts_data, $num_texts_for_style_sample);
    if(!is_array($text_indices_for_style)) $text_indices_for_style = [$text_indices_for_style];

    foreach ($text_indices_for_style as $index) {
        // Limit length of each sample for style summary prompt
        $style_sample_texts .= mb_substr($all_texts_data[$index]['text_content'], 0, 3000) . "\n---\n";
        $sample_count++;
    }
}


$overall_style_summary = "No se pudo generar el resumen de estilo.";
if ($sample_count > 0) {
    echo "Generating overall style summary from $sample_count text sample(s)...\n";
    $prompt_style = "Basado en los siguientes extractos de texto de un sitio web, describe el estilo de escritura general, el tono y las características lingüísticas comunes. Sé conciso (3-5 frases). Extractos:\n\n{$style_sample_texts}";
    $style_api_response = generate_text_gemini($prompt_style);
    if ($style_api_response) {
        $overall_style_summary = trim($style_api_response);
        echo "Style summary generated.\n";
    } else {
        echo "Failed to generate style summary from AI.\n";
        error_log("Analyze Themes: Failed to get AI response for style summary.");
    }
} else {
    echo "Not enough text samples to generate style summary.\n";
}


// Store in database
$identified_themes_json = json_encode($final_themes);
$identified_entities_json = json_encode($final_entities);
$processed_text_ids_json = json_encode($processed_text_ids);

try {
    // Optional: Delete old analysis row(s) if you only want one latest analysis.
    // For example, keep only the last 3:
    // $pdo->query("DELETE FROM ai_site_thematic_analysis WHERE analysis_id NOT IN (SELECT analysis_id FROM ai_site_thematic_analysis ORDER BY analyzed_at DESC LIMIT 3)");

    $sql_insert = "INSERT INTO ai_site_thematic_analysis
        (analyzed_at, identified_themes_json, identified_entities_json, overall_style_summary, processed_text_ids_json, token_count_processed)
        VALUES (CURRENT_TIMESTAMP, :themes, :entities, :style, :processed_ids, :tokens)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':themes' => $identified_themes_json,
        ':entities' => $identified_entities_json,
        ':style' => $overall_style_summary,
        ':processed_ids' => $processed_text_ids_json,
        ':tokens' => $total_token_estimate // This is a character count, not actual tokens.
    ]);
    echo "Site-wide thematic analysis stored successfully. Analysis ID: " . $pdo->lastInsertId() . "\n";
} catch (PDOException $e) {
    echo "DB Error storing site-wide analysis: " . $e->getMessage() . "\n";
    error_log("Analyze Themes DB Error (Insert): " . $e->getMessage());
}

echo "Site-Wide Thematic Analysis Script Finished.\n";
?>
