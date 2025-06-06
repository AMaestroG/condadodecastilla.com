<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_admin_login(); // Redirect to login if not admin

require_once __DIR__ . '/db_connect.php'; // Provides $pdo
require_once __DIR__ . '/../includes/ai_utils.php'; // Provides AI functions

$all_texts = [];
$selected_text_id_content = '';
$manual_text_input = $_POST['manual_text_input'] ?? ''; // Keep input after submission
$selected_text_id = $_POST['selected_text_id'] ?? ''; // Keep selected ID

try {
    $stmt = $pdo->query("SELECT text_id, text_content FROM site_texts ORDER BY text_id ASC");
    $all_texts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error, maybe display a message
    error_log("AI Content Assistant: Error fetching texts: " . $e->getMessage());
}

$ai_output = null;
$action_performed = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $current_text_to_process = $manual_text_input; // Use the content from textarea
    $action_performed = $action;

    if (empty(trim($current_text_to_process))) {
        $ai_output = "Por favor, ingrese texto en el área de texto o seleccione un texto existente.";
    } else {
        switch ($action) {
            case 'summarize':
                $ai_output = get_smart_summary($current_text_to_process);
                break;
            case 'suggest_tags':
                $tags_array = get_suggested_tags($current_text_to_process);
                $ai_output = !empty($tags_array) ? "Etiquetas sugeridas: " . implode(', ', $tags_array) : "No se pudieron sugerir etiquetas.";
                break;
            case 'translate_en':
                $ai_output = get_ai_translation($current_text_to_process, 'en', 'español');
                break;
            case 'translate_fr':
                $ai_output = get_ai_translation($current_text_to_process, 'fr', 'español');
                break;
            case 'improve_phrasing':
                $prompt = "Eres un asistente de escritura experto. Revisa el siguiente texto y sugiere mejoras en la redacción, claridad, y estilo. Si es posible, ofrece una versión reescrita del texto o puntos específicos a mejorar. Texto original:\n\n\"" . $current_text_to_process . "\"";
                $ai_output = generate_text_gemini($prompt);
                $ai_output = $ai_output ?? "No se pudieron obtener sugerencias de mejora.";
                break;
            case 'expand_topic':
                $prompt = "Eres un asistente de contenido experto. Toma el siguiente texto o tema y expande sobre él, añadiendo más detalles, explicaciones, información relacionada o generando contenido adicional que sea coherente y útil. Texto base:\n\n\"" . $current_text_to_process . "\"";
                $ai_output = generate_text_gemini($prompt);
                $ai_output = $ai_output ?? "No se pudo expandir el tema.";
                break;
            default:
                $ai_output = "Acción no reconocida.";
        }
    }
}

// If a text_id was selected and no manual input yet, load its content for the textarea
// This handles initial load when selected_text_id is set by GET from another page, or if JS is disabled for selection
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['text_id_to_load'])) {
    $text_id_to_load = $_GET['text_id_to_load'];
    foreach($all_texts as $text_item) {
        if ($text_item['text_id'] === $text_id_to_load) {
            $manual_text_input = $text_item['text_content'];
            $selected_text_id = $text_id_to_load; // Set selected_text_id as well
            break;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente de Contenido IA</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; color: #333; }
        .container { max-width: 900px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { background-color: #333; padding: 10px 0; text-align: center; margin-bottom: 20px; }
        nav a { color: white; padding: 10px 15px; text-decoration: none; }
        nav a:hover { background-color: #555; }
        h1, h2 { color: #333; }
        textarea { width: 98%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; font-family: inherit; font-size: inherit; }
        select { width: 99%; padding: 8px; margin-bottom: 10px; border-radius: 4px; }
        .action-buttons button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; margin-bottom: 10px; }
        .action-buttons button:hover { background-color: #0056b3; }
        .ai-output { background-color: #e9ecef; padding: 15px; border-radius: 4px; margin-top: 20px; white-space: pre-wrap; font-family: monospace; }
        .current-action-info { margin-bottom: 15px; font-style: italic; color: #555;}
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Dashboard Principal</a>
        <a href="edit_texts.php">Editar Textos Manualmente</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
    <div class="container">
        <h1>Asistente de Contenido IA</h1>

        <form action="ai_content_assistant.php" method="POST">
            <div>
                <label for="selected_text_id">Seleccionar Texto Existente (opcional):</label>
                <select name="selected_text_id" id="selected_text_id">
                    <option value="">-- Nuevo Texto Manual o Seleccione uno --</option>
                    <?php foreach ($all_texts as $text_item): ?>
                        <option value="<?php echo htmlspecialchars($text_item['text_id']); ?>" data-content="<?php echo htmlspecialchars($text_item['text_content']); ?>" <?php echo ($selected_text_id === $text_item['text_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($text_item['text_id']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="manual_text_input">Texto a Procesar:</label>
                <textarea name="manual_text_input" id="manual_text_input" rows="15"><?php echo htmlspecialchars($manual_text_input); ?></textarea>
            </div>

            <div class="action-buttons">
                <button type="submit" name="action" value="summarize">Generar Resumen</button>
                <button type="submit" name="action" value="suggest_tags">Sugerir Etiquetas</button>
                <button type="submit" name="action" value="translate_en">Traducir a Inglés</button>
                <button type="submit" name="action" value="translate_fr">Traducir a Francés</button>
                <button type="submit" name="action" value="improve_phrasing">Mejorar Redacción</button>
                <button type="submit" name="action" value="expand_topic">Expandir Tema</button>
            </div>
        </form>

        <?php if ($ai_output !== null): ?>
            <hr>
            <h2>Resultado de la IA <?php if($action_performed) { echo "(Acción: " . htmlspecialchars($action_performed) . ")"; } ?>:</h2>
            <div class="ai-output"><?php echo nl2br(htmlspecialchars($ai_output)); ?></div>
        <?php endif; ?>

    </div>

    <script>
        document.getElementById('selected_text_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var content = selectedOption.getAttribute('data-content');
            if (content) {
                document.getElementById('manual_text_input').value = content;
            } else {
                 // document.getElementById('manual_text_input').value = ''; // Clear if "-- Select --" is chosen
            }
        });

        // Preserve scroll position after form submission
        window.addEventListener('load', function() {
            if (sessionStorage.getItem('scrollPosition')) {
                window.scrollTo(0, parseInt(sessionStorage.getItem('scrollPosition')));
                sessionStorage.removeItem('scrollPosition');
            }
        });

        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
    </script>
</body>
</html>
