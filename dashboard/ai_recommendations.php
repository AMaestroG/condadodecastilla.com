<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/db_connect.php'; // $pdo

// Feedback message display
$feedback_message = $_SESSION['feedback_message'] ?? null;
$feedback_type = $_SESSION['feedback_type'] ?? 'info'; // Default to 'info'
if ($feedback_message) {
    unset($_SESSION['feedback_message']);
    unset($_SESSION['feedback_type']);
}

$sort_options = [
    'date_desc' => 'ace.evaluated_at DESC',
    'date_asc' => 'ace.evaluated_at ASC',
    'clarity_asc' => 'ace.clarity_score ASC, ace.evaluated_at DESC',
    'clarity_desc' => 'ace.clarity_score DESC, ace.evaluated_at DESC',
    'engagement_asc' => 'ace.engagement_score ASC, ace.evaluated_at DESC',
    'engagement_desc' => 'ace.engagement_score DESC, ace.evaluated_at DESC',
    'seo_asc' => 'ace.seo_score ASC, ace.evaluated_at DESC',
    'seo_desc' => 'ace.seo_score DESC, ace.evaluated_at DESC',
    'text_id_asc' => 'ace.text_id_fk ASC, ace.evaluated_at DESC',
    'text_id_desc' => 'ace.text_id_fk DESC, ace.evaluated_at DESC',
];
$current_sort_key = $_GET['sort'] ?? 'date_desc';
if (!array_key_exists($current_sort_key, $sort_options)) {
    $current_sort_key = 'date_desc'; // Default sort
}
$order_by_sql = $sort_options[$current_sort_key];

$evaluations = [];
$error_message = '';
try {
    // Select a snippet of text_content
    $sql = "SELECT
                ace.eval_id,
                ace.text_id_fk,
                ace.evaluated_at,
                ace.clarity_score,
                ace.engagement_score,
                ace.seo_score,
                ace.overall_assessment,
                ace.suggested_keywords, -- Added suggested_keywords
                st.text_content
            FROM ai_content_evaluations ace
            JOIN site_texts st ON ace.text_id_fk = st.text_id
            ORDER BY " . $order_by_sql; // Secure: $order_by_sql is from a predefined list
    $stmt = $pdo->query($sql);
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching evaluations: " . $e->getMessage();
    error_log("AI Recommendations Page DB Error: " . $e->getMessage());
}

function get_text_snippet(string $text, int $length = 100): string {
    $text = strip_tags($text); // Remove HTML tags for snippet
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . "...";
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recomendaciones de Contenido IA</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body { font-family: Arial, sans-serif; margin:0; background-color: #f4f4f4; color: #333; }
        .container { max-width: 1200px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); overflow-x: auto; }
        nav { background-color: #333; padding: 10px 0; text-align: center; margin-bottom: 20px; }
        nav a { color: white; padding: 10px 15px; text-decoration: none; }
        nav a:hover { background-color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; white-space: nowrap; }
        td { white-space: normal; } /* Allow text wrap for content cells */
        th { background-color: #e9ecef; }
        th a { text-decoration: none; color: inherit; display: block; }
        th a:hover { text-decoration: underline; }
        .actions a { margin-right: 10px; text-decoration: none; white-space: nowrap;}
        .actions a.edit { color: #007bff; }
        .actions a.improve { color: #28a745; }
        .no-data { padding: 15px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 4px; }
        .error-message { padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px; }
        .sort-options { margin-bottom: 15px; }
        .sort-options label { font-weight: bold; margin-right: 5px;}
        .sort-options select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .score-good { color: green; font-weight: bold; }
        .score-medium { color: orange; }
        .score-low { color: red; }
        .feedback-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .feedback-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .feedback-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        .feedback-message.info { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff;}
        .action-buttons-group form { margin-bottom: 5px; } /* Space between apply buttons */
        .action-buttons-group button { padding: 3px 8px; font-size: 0.85em; cursor: pointer; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Dashboard Principal</a>
        <a href="ai_content_assistant.php">Asistente de Contenido IA</a>
        <a href="manage_whispers.php">Gestionar Susurros IA</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
    <div class="container">
        <h1>Recomendaciones de Contenido IA</h1>

        <?php if ($feedback_message): ?>
            <div class="feedback-message <?php echo htmlspecialchars($feedback_type); ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <div class="sort-options">
            <form action="ai_recommendations.php" method="GET" id="sortForm">
                <label for="sort">Ordenar por: </label>
                <select name="sort" id="sort" onchange="document.getElementById('sortForm').submit()">
                    <option value="date_desc" <?php echo ($current_sort_key === 'date_desc') ? 'selected' : ''; ?>>Más Reciente</option>
                    <option value="date_asc" <?php echo ($current_sort_key === 'date_asc') ? 'selected' : ''; ?>>Más Antiguo</option>
                    <option value="text_id_asc" <?php echo ($current_sort_key === 'text_id_asc') ? 'selected' : ''; ?>>Text ID (Asc)</option>
                    <option value="text_id_desc" <?php echo ($current_sort_key === 'text_id_desc') ? 'selected' : ''; ?>>Text ID (Desc)</option>
                    <option value="clarity_asc" <?php echo ($current_sort_key === 'clarity_asc') ? 'selected' : ''; ?>>Claridad (Baja a Alta)</option>
                    <option value="clarity_desc" <?php echo ($current_sort_key === 'clarity_desc') ? 'selected' : ''; ?>>Claridad (Alta a Baja)</option>
                    <option value="engagement_asc" <?php echo ($current_sort_key === 'engagement_asc') ? 'selected' : ''; ?>>Interés (Bajo a Alto)</option>
                    <option value="engagement_desc" <?php echo ($current_sort_key === 'engagement_desc') ? 'selected' : ''; ?>>Interés (Alto a Bajo)</option>
                    <option value="seo_asc" <?php echo ($current_sort_key === 'seo_asc') ? 'selected' : ''; ?>>SEO (Bajo a Alto)</option>
                    <option value="seo_desc" <?php echo ($current_sort_key === 'seo_desc') ? 'selected' : ''; ?>>SEO (Alto a Bajo)</option>
                </select>
            </form>
        </div>

        <?php if (empty($evaluations) && !$error_message): ?>
            <p class="no-data">No se encontraron evaluaciones de contenido. Puede ejecutar el script <code>scripts/evaluate_content_ai.php</code> desde la línea de comandos para generar algunas.</p>
        <?php elseif (!empty($evaluations)): ?>
            <table>
                <thead>
                    <tr>
                        <th><a href="?sort=<?php echo ($current_sort_key === 'date_desc' ? 'date_asc' : 'date_desc'); ?>">Evaluado En &#x2195;</a></th>
                        <th><a href="?sort=<?php echo ($current_sort_key === 'text_id_desc' ? 'text_id_asc' : 'text_id_desc'); ?>">Text ID &#x2195;</a></th>
                        <th><a href="?sort=<?php echo ($current_sort_key === 'clarity_desc' ? 'clarity_asc' : 'clarity_desc'); ?>">Claridad &#x2195;</a></th>
                        <th><a href="?sort=<?php echo ($current_sort_key === 'engagement_desc' ? 'engagement_asc' : 'engagement_desc'); ?>">Interés &#x2195;</a></th>
                        <th><a href="?sort=<?php echo ($current_sort_key === 'seo_desc' ? 'seo_asc' : 'seo_desc'); ?>">SEO &#x2195;</a></th>
                        <th style="width: 30%;">Resumen IA</th>
                        <th style="width: 25%;">Texto Original (Extracto)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                        <tr id="textrow-<?php echo htmlspecialchars($eval['text_id_fk']); ?>">
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($eval['evaluated_at']))); ?></td>
                            <td><?php echo htmlspecialchars($eval['text_id_fk']); ?></td>
                            <td><span class="<?php $s = $eval['clarity_score'] ?? 0; echo $s >= 8 ? 'score-good' : ($s >= 5 ? 'score-medium' : 'score-low'); ?>"><?php echo htmlspecialchars($eval['clarity_score'] ?? 'N/A'); ?></span></td>
                            <td><span class="<?php $s = $eval['engagement_score'] ?? 0; echo $s >= 8 ? 'score-good' : ($s >= 5 ? 'score-medium' : 'score-low'); ?>"><?php echo htmlspecialchars($eval['engagement_score'] ?? 'N/A'); ?></span></td>
                            <td><span class="<?php $s = $eval['seo_score'] ?? 0; echo $s >= 8 ? 'score-good' : ($s >= 5 ? 'score-medium' : 'score-low'); ?>"><?php echo htmlspecialchars($eval['seo_score'] ?? 'N/A'); ?></span></td>
                            <td style="min-width: 250px;"><?php echo htmlspecialchars(get_text_snippet($eval['overall_assessment'] ?? 'N/A', 200)); ?></td>
                            <td style="min-width: 200px;"><?php echo htmlspecialchars(get_text_snippet($eval['text_content'] ?? 'N/A', 150)); ?></td>
                            <td class="actions action-buttons-group">
                                <a href="edit_texts.php?edit_id=<?php echo urlencode($eval['text_id_fk']); ?>" class="edit" title="Editar Texto Original">Editar Manual</a><br>
                                <a href="ai_content_assistant.php?text_id_to_load=<?php echo urlencode($eval['text_id_fk']); ?>" class="improve" title="Mejorar con Asistente IA" style="display:inline-block; margin-bottom:5px;">Asistente IA</a>

                                <?php if (!empty($eval['overall_assessment'])): ?>
                                <form action="apply_ai_suggestion.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="text_id" value="<?php echo htmlspecialchars($eval['text_id_fk']); ?>">
                                    <input type="hidden" name="suggestion_type" value="append_summary">
                                    <input type="hidden" name="suggestion_content" value="<?php echo htmlspecialchars($eval['overall_assessment']); ?>">
                                    <input type="hidden" name="current_sort" value="<?php echo htmlspecialchars($current_sort_key); ?>">
                                    <button type="submit" title="Añade la evaluación general de la IA como un resumen al final del texto." onclick="return confirm('¿Estás seguro de que quieres añadir este resumen de IA al texto ID: <?php echo htmlspecialchars($eval['text_id_fk']); ?>?');">Aplicar Resumen</button>
                                </form>
                                <?php endif; ?>

                                <?php
                                // Assuming $eval['suggested_keywords'] is fetched from ai_content_evaluations table
                                // For this example, let's assume it might not be in the current $eval array from the query.
                                // If it were, the check would be: !empty($eval['suggested_keywords'])
                                // For now, we'll simulate its presence for button structure.
                                // In a real scenario, ensure 'suggested_keywords' is selected in the main SQL query if it's a direct column.
                                // If it's part of a JSON field, it would need to be extracted and checked.
                                // The provided SQL does not fetch 'suggested_keywords' field from 'ai_content_evaluations'
                                // Let's assume it's NOT available for now to avoid errors.
                                // To enable this, add `ace.suggested_keywords` to the SELECT list in the SQL query.
                                // For example, if $eval['suggested_keywords_from_db'] was fetched:
                                // if (!empty($eval['suggested_keywords_from_db'])):
                                ?>
                                <?php
                                // Placeholder for suggested_keywords. To make this functional:
                                // 1. Add `ace.suggested_keywords` to the SQL SELECT statement.
                                // 2. The value in $eval['suggested_keywords'] would then be used.
                                // For now, this button will not appear unless you modify the SQL.
                                $current_eval_suggested_keywords = ''; // Fetch this from $eval if available from DB
                                if (isset($eval['suggested_keywords']) && !empty($eval['suggested_keywords'])) { // Check if the key exists and is not empty
                                    $current_eval_suggested_keywords = $eval['suggested_keywords'];
                                }

                                if ($current_eval_suggested_keywords):
                                ?>
                                <form action="apply_ai_suggestion.php" method="POST" style="display: inline-block; margin-top:5px;">
                                    <input type="hidden" name="text_id" value="<?php echo htmlspecialchars($eval['text_id_fk']); ?>">
                                    <input type="hidden" name="suggestion_type" value="append_keywords">
                                    <input type="hidden" name="suggestion_content" value="<?php echo htmlspecialchars($current_eval_suggested_keywords); ?>">
                                    <input type="hidden" name="current_sort" value="<?php echo htmlspecialchars($current_sort_key); ?>">
                                    <button type="submit" title="Añade las palabras clave sugeridas por la IA al final del texto." onclick="return confirm('¿Estás seguro de que quieres añadir estas palabras clave de IA al texto ID: <?php echo htmlspecialchars($eval['text_id_fk']); ?>?');">Aplicar Palabras Clave</button>
                                </form>
                                <?php endif; ?>
                                <!-- Future: Link to full detail page: <a href="view_evaluation_detail.php?eval_id=<?php echo $eval['eval_id']; ?>" title="Ver Evaluación Completa">Ver Detalle</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
