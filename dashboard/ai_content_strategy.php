<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/db_connect.php'; // $pdo

$feedback_message = $_SESSION['feedback_message'] ?? null;
$feedback_type = $_SESSION['feedback_type'] ?? 'info';
if ($feedback_message) {
    unset($_SESSION['feedback_message']);
    unset($_SESSION['feedback_type']);
}

// Handle status updates for suggestions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suggestion_id'], $_POST['new_status'])) {
    $suggestion_id = filter_input(INPUT_POST, 'suggestion_id', FILTER_VALIDATE_INT);
    $new_status = htmlspecialchars($_POST['new_status']); // Basic sanitization
    $allowed_statuses = ['viewed', 'accepted', 'dismissed', 'new']; // 'new' typically not set via form but good to have

    if ($suggestion_id && in_array($new_status, $allowed_statuses)) {
        try {
            $stmt_update = $pdo->prepare("UPDATE ai_content_strategy_suggestions SET status = :status WHERE suggestion_id = :suggestion_id");
            $stmt_update->execute([':status' => $new_status, ':suggestion_id' => $suggestion_id]);
            $_SESSION['feedback_message'] = "Sugerencia #" . $suggestion_id . " actualizada a '" . $new_status . "'.";
            $_SESSION['feedback_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['feedback_message'] = "Error al actualizar la sugerencia: " . $e->getMessage();
            $_SESSION['feedback_type'] = 'error';
            error_log("AI Strategy Page: Error updating suggestion status: " . $e->getMessage());
        }
    } else {
        $_SESSION['feedback_message'] = "Datos inválidos para actualizar la sugerencia.";
        $_SESSION['feedback_type'] = 'error';
    }
    header("Location: ai_content_strategy.php"); // Redirect to prevent form resubmission
    exit;
}

// Fetch latest thematic analysis - This needs to be available before outline generation logic
$thematic_analysis = null;
try {
    $stmt_analysis = $pdo->query("SELECT * FROM ai_site_thematic_analysis ORDER BY analyzed_at DESC LIMIT 1");
    $thematic_analysis = $stmt_analysis->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message_analysis = "Error al cargar el análisis temático: " . $e->getMessage();
    error_log("AI Strategy Page: DB error fetching thematic analysis: " . $e->getMessage());
}

// Handle 'generate_outline' action
$generated_outline_for_suggestion_id = null;
$generated_outline_content = null;
$error_generating_outline = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_outline') {
    if (isset($_POST['suggestion_id_for_outline'], $_POST['topic_for_outline'])) {
        $generated_outline_for_suggestion_id = $_POST['suggestion_id_for_outline'];
        $topic_for_outline = $_POST['topic_for_outline'];

        $site_themes_summary_for_prompt = "";
        if (isset($thematic_analysis) && !empty($thematic_analysis['identified_themes_json'])) {
            $themes_array = json_decode($thematic_analysis['identified_themes_json'], true);
            if ($themes_array) $site_themes_summary_for_prompt = implode(', ', $themes_array);
        }
        $site_style_summary_for_prompt = $thematic_analysis['overall_style_summary'] ?? "";

        // ai_utils.php should be included via auth.php or db_connect.php, or explicitly require it here.
        // For this project structure, ai_utils.php is typically included by pages that use its functions.
        // Let's assume it's available or ensure it:
        if (!function_exists('get_gemini_article_outline')) {
            require_once __DIR__ . '/../includes/ai_utils.php';
        }

        if (function_exists('get_gemini_article_outline')) {
             $generated_outline_content = get_gemini_article_outline($topic_for_outline, $site_themes_summary_for_prompt, $site_style_summary_for_prompt);
             if ($generated_outline_content === null) {
                 $error_generating_outline = "No se pudo generar el esquema desde la IA para el tema: '" . htmlspecialchars($topic_for_outline) . "'.";
             }
        } else {
            $error_generating_outline = "Error: La función get_gemini_article_outline no está disponible.";
        }
    } else {
        $error_generating_outline = "Faltan datos (ID de sugerencia o tema) para generar el esquema.";
    }

    // If there was an error, store it in session to display after redirect (optional)
    // Or display inline if not redirecting for this action. For simplicity, we'll display inline.
    // if($error_generating_outline && isset($_SESSION)){
    //      $_SESSION['feedback_message'] = $error_generating_outline;
    //      $_SESSION['feedback_type'] = 'error';
    //      header("Location: ai_content_strategy.php#suggestion-" . $generated_outline_for_suggestion_id); exit;
    // }
}


// Fetch strategy suggestions
$strategy_suggestions_grouped = [];
$filter_status = $_GET['filter_status'] ?? 'new'; // Default to 'new', allow filtering
$allowed_filter_statuses = ['new', 'viewed', 'accepted', 'dismissed', 'all'];
if (!in_array($filter_status, $allowed_filter_statuses)) {
    $filter_status = 'new';
}

try {
    $sql_suggestions = "SELECT * FROM ai_content_strategy_suggestions ";
    if ($filter_status !== 'all') {
        $sql_suggestions .= "WHERE status = :status ";
    }
    $sql_suggestions .= "ORDER BY suggestion_type, priority DESC, created_at DESC";

    $stmt_suggestions = $pdo->prepare($sql_suggestions);
    if ($filter_status !== 'all') {
        $stmt_suggestions->bindParam(':status', $filter_status);
    }
    $stmt_suggestions->execute();
    $raw_suggestions = $stmt_suggestions->fetchAll(PDO::FETCH_ASSOC);

    foreach ($raw_suggestions as $sug) {
        $strategy_suggestions_grouped[$sug['suggestion_type']][] = $sug;
    }

} catch (PDOException $e) {
    $error_message_suggestions = "Error al cargar las sugerencias estratégicas: " . $e->getMessage();
    error_log("AI Strategy Page: DB error fetching strategy suggestions: " . $e->getMessage());
}

$suggestion_type_headings = [
    'gap' => 'Brechas de Contenido Identificadas',
    'underdeveloped' => 'Contenido Subdesarrollado',
    'new_theme_expansion' => 'Nuevas Posibilidades de Temas',
    'interlink' => 'Oportunidades de Interlinking'
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Estrategia de Contenido IA</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body { font-family: Arial, sans-serif; margin:0; background-color: #f4f4f4; color: #333; }
        .container { max-width: 1000px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { background-color: #333; padding: 10px 0; text-align: center; margin-bottom: 20px; }
        nav a { color: white; padding: 10px 15px; text-decoration: none; }
        nav a:hover { background-color: #555; }
        h1, h2, h3 { color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        h2 { margin-top: 30px; }
        section { margin-bottom: 30px; padding: 15px; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 5px;}
        .feedback-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .feedback-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .feedback-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        .error-text { color: red; font-style: italic; }
        .suggestion-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px; background-color: #fff; }
        .suggestion-item p { margin: 5px 0; }
        .suggestion-item small { color: #777; }
        .suggestion-actions form { display: inline-block; margin-right: 5px; margin-top: 5px; }
        .suggestion-actions button { padding: 3px 8px; font-size: 0.85em; cursor: pointer; }
        .filter-form { margin-bottom:20px; }
        .filter-form label {font-weight:bold;}
        .filter-form select, .filter-form button {padding:5px; border-radius:3px; border:1px solid #ccc;}
        .generated-outline { margin-top:10px; padding:10px; border:1px solid #e0e0e0; background-color:#f0f8ff; border-radius: 4px; }
        .generated-outline h4 { margin-top:0; color: #007bff; }
        .generated-outline pre { white-space: pre-wrap; font-family: inherit; background-color: #fff; padding: 8px; border: 1px solid #ccc; }
        .generated-outline-error { color:red; margin-top:10px; padding:10px; background-color:#ffebee; border:1px solid #ffcdd2; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Dashboard Principal</a>
        <a href="ai_recommendations.php">Recomendaciones IA</a>
        <a href="ai_content_assistant.php">Asistente de Contenido</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
    <div class="container">
        <h1>Dashboard de Estrategia de Contenido IA</h1>

        <?php if ($feedback_message): ?>
            <div class="feedback-message <?php echo htmlspecialchars($feedback_type); ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <section id="thematic-analysis">
            <h2>Resumen Temático Global del Sitio</h2>
            <?php if (isset($error_message_analysis)): ?>
                <p class="error-text"><?php echo htmlspecialchars($error_message_analysis); ?></p>
            <?php elseif ($thematic_analysis): ?>
                <p><strong>Último Análisis:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($thematic_analysis['analyzed_at']))); ?></p>
                <p><strong>Resumen del Estilo General:</strong></p>
                <div style="padding-left:15px; border-left:3px solid #007bff; white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars($thematic_analysis['overall_style_summary'])); ?></div>

                <p style="margin-top:10px;"><strong>Temas Clave Identificados:</strong></p>
                <?php $themes = !empty($thematic_analysis['identified_themes_json']) ? json_decode($thematic_analysis['identified_themes_json'], true) : []; ?>
                <?php if ($themes): echo "<p>" . htmlspecialchars(implode(', ', $themes)) . "</p>"; else: echo "<p>N/A</p>"; endif; ?>

                <p style="margin-top:10px;"><strong>Entidades Clave Identificadas:</strong></p>
                <?php $entities = !empty($thematic_analysis['identified_entities_json']) ? json_decode($thematic_analysis['identified_entities_json'], true) : []; ?>
                <?php if ($entities && (!empty($entities['people']) || !empty($entities['places']) || !empty($entities['events']))): ?>
                    <ul>
                        <?php if(!empty($entities['people'])): ?><li><strong>Personas:</strong> <?php echo htmlspecialchars(implode(', ', $entities['people'])); ?></li><?php endif; ?>
                        <?php if(!empty($entities['places'])): ?><li><strong>Lugares:</strong> <?php echo htmlspecialchars(implode(', ', $entities['places'])); ?></li><?php endif; ?>
                        <?php if(!empty($entities['events'])): ?><li><strong>Eventos:</strong> <?php echo htmlspecialchars(implode(', ', $entities['events'])); ?></li><?php endif; ?>
                    </ul>
                <?php else: echo "<p>N/A</p>"; endif; ?>
                 <small>Basado en el análisis ID: <?php echo htmlspecialchars($thematic_analysis['analysis_id']); ?></small>
            <?php else: ?>
                <p>No se ha encontrado ningún análisis temático. Ejecute <code>scripts/analyze_site_themes.php</code> desde la línea de comandos.</p>
            <?php endif; ?>
        </section>

        <section id="strategy-suggestions">
            <h2>Sugerencias Estratégicas de Contenido</h2>
            <form action="ai_content_strategy.php" method="GET" class="filter-form">
                <label for="filter_status">Filtrar por estado: </label>
                <select name="filter_status" id="filter_status">
                    <option value="new" <?php if ($filter_status === 'new') echo 'selected'; ?>>Nuevas</option>
                    <option value="viewed" <?php if ($filter_status === 'viewed') echo 'selected'; ?>>Vistas</option>
                    <option value="accepted" <?php if ($filter_status === 'accepted') echo 'selected'; ?>>Aceptadas</option>
                    <option value="dismissed" <?php if ($filter_status === 'dismissed') echo 'selected'; ?>>Descartadas</option>
                    <option value="all" <?php if ($filter_status === 'all') echo 'selected'; ?>>Todas</option>
                </select>
                <button type="submit">Filtrar</button>
            </form>

            <?php if (isset($error_message_suggestions)): ?>
                 <p class="error-text"><?php echo htmlspecialchars($error_message_suggestions); ?></p>
            <?php elseif (empty($strategy_suggestions_grouped)): ?>
                <p>No hay sugerencias estratégicas con el estado '<?php echo htmlspecialchars($filter_status); ?>'. Pruebe a ejecutar <code>scripts/identify_content_opportunities.php</code> o cambie el filtro.</p>
            <?php else: ?>
                <?php foreach ($suggestion_type_headings as $type_key => $type_heading): ?>
                    <?php if (!empty($strategy_suggestions_grouped[$type_key])): ?>
                        <h3><?php echo htmlspecialchars($type_heading); ?> (<?php echo count($strategy_suggestions_grouped[$type_key]); ?>)</h3>
                        <?php foreach ($strategy_suggestions_grouped[$type_key] as $sug): ?>
                            <div class="suggestion-item" id="suggestion-<?php echo $sug['suggestion_id']; ?>">
                                <p><?php echo nl2br(htmlspecialchars($sug['description_text'])); ?></p>
                                <?php if (!empty($sug['suggested_new_topic'])): ?>
                                    <p><strong>Tema Sugerido:</strong> <?php echo htmlspecialchars($sug['suggested_new_topic']); ?>
                                       <a href="ai_content_assistant.php?prefill_text=<?php echo urlencode("Desarrollar el tema: " . $sug['suggested_new_topic']); ?>" title="Redactar sobre este tema con IA">Redactar con IA</a>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($sug['related_text_id_fk'])): ?>
                                    <p><strong>Texto Relacionado:</strong> <a href="edit_texts.php?edit_id=<?php echo urlencode($sug['related_text_id_fk']); ?>" title="Editar texto relacionado"><?php echo htmlspecialchars($sug['related_text_id_fk']); ?></a>
                                    <?php if(!empty($sug['related_text_id_fk2'])): ?>
                                        y <a href="edit_texts.php?edit_id=<?php echo urlencode($sug['related_text_id_fk2']); ?>" title="Editar segundo texto relacionado"><?php echo htmlspecialchars($sug['related_text_id_fk2']); ?></a>
                                    <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                <small>Creado: <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($sug['created_at']))); ?> | Prioridad: <?php echo htmlspecialchars($sug['priority']); ?> | Estado: <?php echo htmlspecialchars($sug['status']); ?> | ID Sugerencia: <?php echo $sug['suggestion_id']; ?></small>
                                <div class="suggestion-actions">
                                    <!-- Status update forms -->
                                    <?php if ($sug['status'] === 'new'): ?>
                                    <form action="ai_content_strategy.php#suggestion-<?php echo $sug['suggestion_id']; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?php echo $sug['suggestion_id']; ?>">
                                        <input type="hidden" name="new_status" value="viewed">
                                        <button type="submit" title="Marcar como revisada">Marcar Vista</button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if ($sug['status'] === 'new' || $sug['status'] === 'viewed'): ?>
                                    <form action="ai_content_strategy.php#suggestion-<?php echo $sug['suggestion_id']; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?php echo $sug['suggestion_id']; ?>">
                                        <input type="hidden" name="new_status" value="accepted">
                                        <button type="submit" title="Aceptar esta sugerencia">Aceptar</button>
                                    </form>
                                    <form action="ai_content_strategy.php#suggestion-<?php echo $sug['suggestion_id']; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?php echo $sug['suggestion_id']; ?>">
                                        <input type="hidden" name="new_status" value="dismissed">
                                        <button type="submit" title="Descartar esta sugerencia">Descartar</button>
                                    </form>
                                    <?php endif; ?>
                                     <?php if ($sug['status'] !== 'new'): ?>
                                    <form action="ai_content_strategy.php#suggestion-<?php echo $sug['suggestion_id']; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="suggestion_id" value="<?php echo $sug['suggestion_id']; ?>">
                                        <input type="hidden" name="new_status" value="new">
                                        <button type="submit" title="Volver a marcar como nueva">Re-Nueva</button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Generate Outline Button -->
                                    <?php if (($sug['suggestion_type'] === 'gap' || $sug['suggestion_type'] === 'new_theme_expansion') && !empty($sug['suggested_new_topic'])): ?>
                                        <form action="ai_content_strategy.php#suggestion-<?php echo $sug['suggestion_id']; ?>" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="generate_outline">
                                            <input type="hidden" name="suggestion_id_for_outline" value="<?php echo htmlspecialchars($sug['suggestion_id']); ?>">
                                            <input type="hidden" name="topic_for_outline" value="<?php echo htmlspecialchars($sug['suggested_new_topic']); ?>">
                                            <button type="submit">Generar Esquema</button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <!-- Display Generated Outline -->
                                <?php if ($generated_outline_for_suggestion_id == $sug['suggestion_id'] && $generated_outline_content): ?>
                                    <div class="generated-outline">
                                        <h4>Esquema Generado para "<?php echo htmlspecialchars($sug['suggested_new_topic']); ?>":</h4>
                                        <pre><?php echo htmlspecialchars($generated_outline_content); ?></pre>
                                        <p><a href="ai_content_assistant.php?prefill_text=<?php echo urlencode("# " . $sug['suggested_new_topic'] . "\n\n" . $generated_outline_content); ?>" title="Copiar esquema y tema al Asistente de Contenido IA">Usar este esquema en Asistente IA</a></p>
                                    </div>
                                <?php elseif ($generated_outline_for_suggestion_id == $sug['suggestion_id'] && $error_generating_outline): ?>
                                     <div class="generated-outline-error">
                                        <p>Error al generar esquema: <?php echo htmlspecialchars($error_generating_outline); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php
                // Check if any suggestions were displayed at all for the current filter
                $has_suggestions_for_filter = false;
                foreach($suggestion_type_headings as $type_key => $type_heading) {
                    if(!empty($strategy_suggestions_grouped[$type_key])) {
                        $has_suggestions_for_filter = true;
                        break;
                    }
                }
                if(!$has_suggestions_for_filter && empty($error_message_suggestions)):
                ?>
                 <p>No hay sugerencias estratégicas con el estado '<?php echo htmlspecialchars($filter_status); ?>'. Pruebe a ejecutar <code>scripts/identify_content_opportunities.php</code> o cambie el filtro.</p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
