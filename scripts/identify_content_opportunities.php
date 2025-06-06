<?php
// scripts/identify_content_opportunities.php
if (php_sapi_name() !== 'cli' && php_sapi_name() !== 'cgi-fcgi') { die("CLI only"); }

require_once __DIR__ . '/../dashboard/db_connect.php'; // $pdo
require_once __DIR__ . '/../includes/ai_utils.php';   // generate_text_gemini

ini_set('display_errors', 1); error_reporting(E_ALL); set_time_limit(0);
date_default_timezone_set('Europe/Madrid'); // Set a default timezone

echo "Starting Content Opportunity Identification Script...\n";

// --- Helper function to store suggestions ---
function store_suggestion(PDO $pdo, array $suggestion_data) {
    // Basic deduplication: check if a similar 'new' suggestion already exists
    $check_sql = "";
    $check_params = [];

    if (isset($suggestion_data['suggested_new_topic']) && !empty($suggestion_data['suggested_new_topic'])) {
        $check_sql = "SELECT suggestion_id FROM ai_content_strategy_suggestions WHERE suggestion_type = :type AND suggested_new_topic = :topic AND status = 'new'";
        $check_params = [':type' => $suggestion_data['suggestion_type'], ':topic' => $suggestion_data['suggested_new_topic']];
    } elseif (isset($suggestion_data['related_text_id_fk']) && !empty($suggestion_data['related_text_id_fk']) && $suggestion_data['suggestion_type'] === 'underdeveloped') {
         $check_sql = "SELECT suggestion_id FROM ai_content_strategy_suggestions WHERE suggestion_type = :type AND related_text_id_fk = :text_id AND status = 'new'";
        $check_params = [':type' => $suggestion_data['suggestion_type'], ':text_id' => $suggestion_data['related_text_id_fk']];
    } elseif (isset($suggestion_data['related_text_id_fk']) && isset($suggestion_data['related_text_id_fk2']) && $suggestion_data['suggestion_type'] === 'interlink') {
        $check_sql = "SELECT suggestion_id FROM ai_content_strategy_suggestions WHERE suggestion_type = :type AND related_text_id_fk = :text_id1 AND related_text_id_fk2 = :text_id2 AND status = 'new'";
        $check_params = [':type' => $suggestion_data['suggestion_type'], ':text_id1' => $suggestion_data['related_text_id_fk'], ':text_id2' => $suggestion_data['related_text_id_fk2']];
    }

    if (!empty($check_sql)) {
        $stmt_check = $pdo->prepare($check_sql);
        $stmt_check->execute($check_params);
        if ($stmt_check->fetch()) {
            echo "Skipping duplicate 'new' suggestion for type '" . $suggestion_data['suggestion_type'] . "' related to: " . ($suggestion_data['suggested_new_topic'] ?? $suggestion_data['related_text_id_fk'] ?? 'N/A') . "\n";
            return;
        }
    }


    $sql = "INSERT INTO ai_content_strategy_suggestions
                (suggestion_type, description_text, related_text_id_fk, related_text_id_fk2, suggested_new_topic, priority, status, source_analysis_id_fk, created_at)
            VALUES
                (:suggestion_type, :description_text, :related_text_id_fk, :related_text_id_fk2, :suggested_new_topic, :priority, :status, :source_analysis_id_fk, CURRENT_TIMESTAMP)";
    $stmt = $pdo->prepare($sql);
    $params = [
        ':suggestion_type' => $suggestion_data['suggestion_type'],
        ':description_text' => $suggestion_data['description_text'],
        ':related_text_id_fk' => $suggestion_data['related_text_id_fk'] ?? null,
        ':related_text_id_fk2' => $suggestion_data['related_text_id_fk2'] ?? null,
        ':suggested_new_topic' => $suggestion_data['suggested_new_topic'] ?? null,
        ':priority' => $suggestion_data['priority'] ?? 5, // Default priority
        ':status' => $suggestion_data['status'] ?? 'new',
        ':source_analysis_id_fk' => $suggestion_data['source_analysis_id_fk'] ?? null,
    ];
    try {
        $stmt->execute($params);
        echo "Stored suggestion: " . $suggestion_data['suggestion_type'] . " - " . ($suggestion_data['suggested_new_topic'] ?? $suggestion_data['related_text_id_fk'] ?? 'N/A') . "\n";
    } catch (PDOException $e) {
        echo "DB Error storing suggestion: " . $e->getMessage() . ". Params: " . json_encode($params) . "\n";
        error_log("Identify Opportunities DB Error: " . $e->getMessage());
    }
}

// --- 1. Fetch necessary data ---
$latest_analysis = null;
$evaluations = [];
$all_site_texts_map = []; // text_id => content snippet

try {
    $stmt_analysis = $pdo->query("SELECT * FROM ai_site_thematic_analysis ORDER BY analyzed_at DESC LIMIT 1");
    $latest_analysis = $stmt_analysis->fetch(PDO::FETCH_ASSOC);

    $stmt_evals = $pdo->query("SELECT text_id_fk, clarity_score, engagement_score, overall_assessment FROM ai_content_evaluations"); // Only fetch needed fields
    $evaluations = $stmt_evals->fetchAll(PDO::FETCH_ASSOC);

    $stmt_texts = $pdo->query("SELECT text_id, text_content FROM site_texts WHERE TRIM(text_content) != ''");
    while($row = $stmt_texts->fetch(PDO::FETCH_ASSOC)) {
        // Store full content for potential interlinking analysis, snippet for titles list
        $all_site_texts_map[$row['text_id']] = ['content' => $row['text_content'], 'snippet_title' => mb_substr(strip_tags($row['text_content']), 0, 70) . "..."];
    }

} catch (PDOException $e) {
    echo "Error fetching initial data: " . $e->getMessage() . "\n";
    error_log("Identify Opportunities: DB error fetching initial data: " . $e->getMessage());
    exit(1);
}

if (!$latest_analysis) {
    echo "No site-wide thematic analysis found. Run analyze_site_themes.php first.\n"; exit(0);
}
$source_analysis_id = $latest_analysis['analysis_id'];
$site_themes_json = $latest_analysis['identified_themes_json'];
$site_entities_json = $latest_analysis['identified_entities_json'];
$site_style = $latest_analysis['overall_style_summary'];

$site_themes_array = !empty($site_themes_json) ? json_decode($site_themes_json, true) : [];
$site_entities_map = !empty($site_entities_json) ? json_decode($site_entities_json, true) : [];

if (empty($site_themes_array) && empty($site_entities_map['people']) && empty($site_entities_map['places']) && empty($site_entities_map['events'])) {
    echo "Warning: Site themes and entities from thematic analysis are empty. Gap analysis might be less effective.\n";
}


// --- 2. Identify "Underdeveloped Content" ---
echo "\n--- Identifying Underdeveloped Content ---\n";
$underdeveloped_threshold = 5; // Scores at or below this threshold are flagged
foreach ($evaluations as $eval) {
    $reasons = [];
    if (isset($eval['clarity_score']) && $eval['clarity_score'] <= $underdeveloped_threshold) $reasons[] = "baja claridad (".$eval['clarity_score'].")";
    if (isset($eval['engagement_score']) && $eval['engagement_score'] <= $underdeveloped_threshold) $reasons[] = "bajo interés (".$eval['engagement_score'].")";

    if (!empty($reasons)) {
        store_suggestion($pdo, [
            'suggestion_type' => 'underdeveloped',
            'description_text' => "El texto '" . $eval['text_id_fk'] . "' parece subdesarrollado. Razones: " . implode(', ', $reasons) . ". Evaluación general: " . (mb_substr($eval['overall_assessment'] ?? 'N/A', 0, 150)."..."),
            'related_text_id_fk' => $eval['text_id_fk'],
            'source_analysis_id_fk' => $source_analysis_id,
            'priority' => 7 // Higher priority for fixing existing content
        ]);
    }
}

// --- 3. Identify "Content Gaps" for Existing Themes (AI-driven) ---
echo "\n--- Identifying Content Gaps for Existing Themes ---\n";
$existing_titles_list_str = !empty($all_site_texts_map) ? implode("\n- ", array_keys($all_site_texts_map)) : "ninguno";

$prompt_gaps = "Este sitio web trata principalmente sobre: " . (!empty($site_themes_array) ? implode(', ', $site_themes_array) : "temas generales de historia y cultura") . ". ";
if (!empty($site_entities_map)) {
    $prompt_gaps .= "Entidades clave mencionadas: Personas (" . (!empty($site_entities_map['people']) ? implode(', ', $site_entities_map['people']) : "varias") . "), Lugares (" . (!empty($site_entities_map['places']) ? implode(', ', $site_entities_map['places']) : "varios") . "), Eventos (" . (!empty($site_entities_map['events']) ? implode(', ', $site_entities_map['events']) : "varios") . "). ";
}
$prompt_gaps .= "Los siguientes son títulos/IDs de contenido ya existente en el sitio:\n- {$existing_titles_list_str}\n\n";
$prompt_gaps .= "Considerando los temas y entidades existentes, ¿qué 3-5 temas específicos o entidades importantes, directamente relacionados con estos temas principales, parecen estar ausentes o necesitarían su propio artículo/sección dedicada para completar la cobertura? Responde únicamente con un array JSON de strings con los nombres de estos temas/entidades sugeridos. Ejemplo: [\"Biografía Detallada de Personaje X\", \"Impacto del Evento Y en la Región Z\"]";

$gap_suggestions_str = generate_text_gemini($prompt_gaps);
if ($gap_suggestions_str) {
    $cleaned_gaps_str = trim($gap_suggestions_str);
    if (strpos($cleaned_gaps_str, '```json') === 0) {
        $cleaned_gaps_str = preg_replace('/^```json\s*/', '', $cleaned_gaps_str);
        $cleaned_gaps_str = preg_replace('/\s*```$/', '', $cleaned_gaps_str);
        $cleaned_gaps_str = trim($cleaned_gaps_str);
    }
    $suggested_topics = json_decode($cleaned_gaps_str, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($suggested_topics)) {
        foreach ($suggested_topics as $topic) {
            if(is_string($topic) && !empty(trim($topic))) {
                 store_suggestion($pdo, [
                    'suggestion_type' => 'gap',
                    'description_text' => "El tema/entidad '" . htmlspecialchars($topic) . "' parece estar ausente o subrepresentado, pero está relacionado con los temas principales del sitio.",
                    'suggested_new_topic' => $topic,
                    'source_analysis_id_fk' => $source_analysis_id,
                    'priority' => 6
                ]);
            }
        }
    } else {
        echo "Advertencia: La respuesta de la IA para 'content gaps' no fue un JSON válido o no contenía un array: " . $cleaned_gaps_str . "\n";
        error_log("Identify Opportunities: AI response for gaps not valid JSON array. Response: " . $cleaned_gaps_str);
    }
} else {
     echo "Advertencia: No se obtuvo respuesta de la IA para 'content gaps'.\n";
     error_log("Identify Opportunities: No AI response for gaps.");
}
if (count($evaluations) > 0 || count($all_site_texts_map) > 0) sleep(1); // Pause before next big AI call


// --- 4. Suggest "New Theme Expansion" (AI-driven) ---
echo "\n--- Suggesting New Theme Expansions ---\n";
$prompt_new_themes = "Un sitio web tiene los siguientes temas principales: [" . (!empty($site_themes_array) ? implode(', ', $site_themes_array) : "historia y cultura local") . "] y un estilo general descrito como: '" . (!empty($site_style) ? $site_style : "informativo y detallado") . "'. ";
$prompt_new_themes .= "Sugiere 1-2 nuevas direcciones temáticas o áreas de contenido principales que podrían expandir lógicamente el alcance del sitio manteniendo la complementariedad. Para cada sugerencia, proporciona el nombre del nuevo tema y una breve justificación (1-2 frases).";
$prompt_new_themes .= " Responde únicamente con un array JSON de objetos, donde cada objeto tiene las claves 'new_theme_idea' (string) y 'rationale' (string). Ejemplo: [{\"new_theme_idea\": \"Rutas Gastronómicas Históricas de la Región\", \"rationale\": \"Conectaría la historia local con el turismo y la cultura gastronómica, atrayendo a un público interesado en experiencias vivenciales.\"}]";

$new_theme_suggestions_str = generate_text_gemini($prompt_new_themes);
if ($new_theme_suggestions_str) {
    $cleaned_new_themes_str = trim($new_theme_suggestions_str);
     if (strpos($cleaned_new_themes_str, '```json') === 0) {
        $cleaned_new_themes_str = preg_replace('/^```json\s*/', '', $cleaned_new_themes_str);
        $cleaned_new_themes_str = preg_replace('/\s*```$/', '', $cleaned_new_themes_str);
        $cleaned_new_themes_str = trim($cleaned_new_themes_str);
    }
    $new_themes_data = json_decode($cleaned_new_themes_str, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($new_themes_data)) {
        foreach ($new_themes_data as $theme_idea) {
            if (isset($theme_idea['new_theme_idea']) && !empty(trim($theme_idea['new_theme_idea'])) && isset($theme_idea['rationale'])) {
                store_suggestion($pdo, [
                    'suggestion_type' => 'new_theme_expansion',
                    'description_text' => "Expansión temática sugerida: '" . htmlspecialchars($theme_idea['new_theme_idea']) . "'. Justificación: " . htmlspecialchars($theme_idea['rationale']),
                    'suggested_new_topic' => $theme_idea['new_theme_idea'],
                    'source_analysis_id_fk' => $source_analysis_id,
                    'priority' => 4 // Lower priority than filling existing gaps
                ]);
            }
        }
    } else {
        echo "Advertencia: La respuesta de la IA para 'new theme expansion' no fue un JSON válido o no contenía el formato esperado: " . $cleaned_new_themes_str . "\n";
        error_log("Identify Opportunities: AI response for new themes not valid. Response: " . $cleaned_new_themes_str);
    }
} else {
    echo "Advertencia: No se obtuvo respuesta de la IA para 'new theme expansion'.\n";
    error_log("Identify Opportunities: No AI response for new themes.");
}
if (count($evaluations) > 0 || count($all_site_texts_map) > 0) sleep(1);


// --- 5. Interlinking Opportunities ---
echo "\n--- Identifying Interlinking Opportunities (Simplified) ---\n";
// Simplified approach: Check a few important texts or those with good scores.
// For texts that are central to a theme, suggest linking to other texts that mention related entities/sub-themes.
// This is still complex; a truly robust solution is a larger task.
// We'll iterate a few texts and ask for linking opportunities.
$texts_for_interlinking_sample = array_slice($all_site_texts_map, 0, 5); // Analyze first 5 texts for interlinking
$site_themes_str_for_prompt = !empty($site_themes_array) ? implode(', ', $site_themes_array) : "varios temas históricos y culturales";
$other_titles_sample = array_slice(array_keys($all_site_texts_map), 0, 10); // Sample of other titles

foreach($texts_for_interlinking_sample as $text_id => $text_data) {
    $content_snippet = mb_substr(strip_tags($text_data['content']), 0, 1000);
    $prompt_interlink = <<<PROMPT
Analiza el siguiente extracto de texto (de text_id '{$text_id}').
Extracto: "{$content_snippet}"

El sitio web cubre temas generales como: {$site_themes_str_for_prompt}.
Algunos otros títulos de artículos en el sitio incluyen:
- {$other_titles_sample[0] ?? ''}
- {$other_titles_sample[1] ?? ''}
- {$other_titles_sample[2] ?? ''}
(y otros más)

Identifica hasta 2 oportunidades claras para enlazar desde el extracto proporcionado hacia otros conceptos o posibles artículos (basados en los temas generales o los títulos de ejemplo).
Responde SÓLO con un array JSON de objetos. Cada objeto debe tener las claves:
- "phrase_to_link": La frase exacta del extracto que debería ser enlazada.
- "link_to_concept_or_potential_page": Una descripción breve del concepto o el título de una página existente/potencial a la que enlazar.
- "reason": Una breve justificación para el enlace.

Ejemplo de respuesta JSON:
[
  {
    "phrase_to_link": "el Conde Diego Porcelos",
    "link_to_concept_or_potential_page": "Biografía del Conde Diego Porcelos",
    "reason": "Ampliar información sobre esta figura histórica clave mencionada."
  }
]
Si no encuentras oportunidades claras, devuelve un array JSON vacío [].
PROMPT;

    echo "Buscando oportunidades de interlinking para text_id: $text_id ... ";
    $interlink_suggestions_str = generate_text_gemini($prompt_interlink);

    if ($interlink_suggestions_str) {
        $cleaned_interlink_str = trim($interlink_suggestions_str);
        if (strpos($cleaned_interlink_str, '```json') === 0) {
            $cleaned_interlink_str = preg_replace('/^```json\s*/', '', $cleaned_interlink_str);
            $cleaned_interlink_str = preg_replace('/\s*```$/', '', $cleaned_interlink_str);
            $cleaned_interlink_str = trim($cleaned_interlink_str);
        }
        $interlinks = json_decode($cleaned_interlink_str, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($interlinks)) {
            foreach ($interlinks as $link) {
                if (isset($link['phrase_to_link']) && isset($link['link_to_concept_or_potential_page']) && isset($link['reason'])) {
                    store_suggestion($pdo, [
                        'suggestion_type' => 'interlink',
                        'description_text' => "En el texto '{$text_id}', enlazar la frase \"".htmlspecialchars($link['phrase_to_link'])."\" al concepto/página \"".htmlspecialchars($link['link_to_concept_or_potential_page'])."\". Razón: ".htmlspecialchars($link['reason']),
                        'related_text_id_fk' => $text_id, // Text containing the phrase
                        // related_text_id_fk2 could be used if AI identified a specific existing text_id to link to.
                        // For now, link_to_concept_or_potential_page is descriptive.
                        'suggested_new_topic' => $link['link_to_concept_or_potential_page'], // Store the target concept here
                        'source_analysis_id_fk' => $source_analysis_id,
                        'priority' => 3
                    ]);
                }
            }
            echo "OK (" . count($interlinks) . " sugerencias procesadas).\n";
        } else {
            echo "Advertencia: La respuesta de la IA para 'interlinking' en $text_id no fue un JSON válido o no contenía el formato esperado: " . $cleaned_interlink_str . "\n";
            error_log("Identify Opportunities: AI response for interlinking $text_id not valid. Response: " . $cleaned_interlink_str);
        }
    } else {
        echo "Advertencia: No se obtuvo respuesta de la IA para 'interlinking' en $text_id.\n";
        error_log("Identify Opportunities: No AI response for interlinking $text_id.");
    }
    if (count($texts_for_interlinking_sample) > 1) sleep(1); // Rate limiting
}


echo "\nContent Opportunity Identification Script Finished.\n";
?>
