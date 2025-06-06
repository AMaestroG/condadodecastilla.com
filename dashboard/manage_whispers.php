<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_admin_login();

require_once __DIR__ . '/db_connect.php'; // Provides $pdo
require_once __DIR__ . '/../includes/ai_utils.php'; // Provides AI functions
// require_once __DIR__ . '/../includes/text_manager.php'; // For save_text_content, if we use it directly

$character_name_input = $_POST['character_name'] ?? '';
$character_bio_input = $_POST['character_bio'] ?? '';
$character_facts_input = $_POST['character_facts'] ?? '';
$entity_type_input = $_POST['entity_type'] ?? 'character'; // Default to character

$generated_whisper = null;
$suggested_text_id = '';
$feedback_message = $_SESSION['feedback_message'] ?? null; // For displaying messages after redirect
if (isset($_SESSION['feedback_message'])) {
    unset($_SESSION['feedback_message']);
}


// Helper function for sanitizing name to text_id part
function sanitize_for_text_id(string $name, string $prefix = 'whisper_character_'): string {
    $name = strtolower($name);
    $name = preg_replace('/\s+/', '_', $name); // Replace spaces with underscores
    $name = preg_replace('/[^a-z0-9_]/', '', $name); // Remove non-alphanumeric except underscore
    $sanitized = trim($name, '_');
    if (empty($sanitized)) {
        return $prefix . 'generic';
    }
    return $prefix . $sanitized;
}

// Helper function to build the whisper prompt
function build_whisper_prompt(string $name, string $bio = '', string $facts_str = ''): string {
    $prompt_parts = [];
    $prompt_parts[] = "Genera una frase corta, poética y evocadora (un 'susurro') sobre el personaje histórico '$name'.";

    if (!empty($bio)) {
        $prompt_parts[] = "Considera estas características o biografía breve: \"$bio\".";
    }
    if (!empty($facts_str)) {
        $prompt_parts[] = "Incorpora sutilmente alguno de estos hechos clave (si es posible y natural): \"$facts_str\".";
    }

    $styles = [
        "El susurro debe ser misterioso y atractivo, adecuado para una web de historia y leyendas.",
        "Busca un tono enigmático que invite a la curiosidad.",
        "La frase debe ser breve, impactante y memorable.",
        "Evita ser demasiado directo o literal; prefiere la sugerencia y la metáfora."
    ];
    $prompt_parts[] = $styles[array_rand($styles)];
    $prompt_parts[] = "El resultado debe ser únicamente la frase del susurro, sin explicaciones adicionales ni comillas al inicio o final.";

    return implode(" ", $prompt_parts);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'generate_whisper') {
        if (empty($character_name_input)) {
            $feedback_message = ['type' => 'error', 'text' => 'El nombre del personaje es obligatorio.'];
        } else {
            $prompt = build_whisper_prompt($character_name_input, $character_bio_input, $character_facts_input);
            $generated_whisper = generate_text_gemini($prompt);
            if (!$generated_whisper) {
                $generated_whisper = "No se pudo generar el susurro. Inténtalo de nuevo.";
                $feedback_message = ['type' => 'error', 'text' => 'La IA no pudo generar un susurro. Verifica la configuración o inténtalo más tarde.'];
            }
            $suggested_text_id = sanitize_for_text_id($character_name_input, 'whisper_' . $entity_type_input . '_');
        }
    } elseif ($action === 'save_whisper') {
        $text_id_to_save = $_POST['text_id_to_save'] ?? '';
        $whisper_to_save = $_POST['generated_whisper_to_save'] ?? '';
        $saved_character_name = $_POST['character_name_for_save'] ?? 'N/A';


        if (empty($text_id_to_save) || empty($whisper_to_save)) {
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Error: El ID del texto y el contenido del susurro son obligatorios para guardar.'];
            header("Location: manage_whispers.php"); // Redirect back
            exit;
        }

        try {
            // Check if text_id already exists
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM site_texts WHERE text_id = :text_id");
            $stmt_check->execute([':text_id' => $text_id_to_save]);
            $exists = $stmt_check->fetchColumn() > 0;

            if ($exists) {
                // For now, allow overwrite. Could add a confirmation step.
                 $stmt_save = $pdo->prepare("UPDATE site_texts SET text_content = :text_content, updated_at = CURRENT_TIMESTAMP WHERE text_id = :text_id");
                 $_SESSION['feedback_message'] = ['type' => 'success', 'text' => "Susurro para '$saved_character_name' actualizado con ID: '$text_id_to_save'."];
            } else {
                $stmt_save = $pdo->prepare("INSERT INTO site_texts (text_id, text_content, updated_at) VALUES (:text_id, :text_content, CURRENT_TIMESTAMP)");
                 $_SESSION['feedback_message'] = ['type' => 'success', 'text' => "Susurro para '$saved_character_name' guardado con nuevo ID: '$text_id_to_save'."];
            }

            $stmt_save->execute([':text_id' => $text_id_to_save, ':text_content' => $whisper_to_save]);

            header("Location: edit_texts.php?edit_id=" . urlencode($text_id_to_save));
            exit;

        } catch (PDOException $e) {
            error_log("Error saving whisper: " . $e->getMessage());
            $_SESSION['feedback_message'] = ['type' => 'error', 'text' => 'Error al guardar el susurro en la base de datos: ' . $e->getMessage()];
            // To retain form values on error, we'd need to pass them back or re-render the form here instead of just redirecting
            // For simplicity, we'll redirect and the user might lose the generated whisper not yet saved.
             header("Location: manage_whispers.php");
             exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Susurros IA</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; color: #333; }
        .container { max-width: 800px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { background-color: #333; padding: 10px 0; text-align: center; margin-bottom: 20px; }
        nav a { color: white; padding: 10px 15px; text-decoration: none; }
        nav a:hover { background-color: #555; }
        h1, h2 { color: #333; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], textarea { width: 98%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; font-family: inherit; font-size: inherit; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
        button:hover { background-color: #0056b3; }
        .generated-whisper-section { background-color: #e9ecef; padding: 15px; border-radius: 4px; margin-top: 20px; }
        .generated-whisper-section p { margin: 5px 0; }
        .generated-whisper-section strong { font-size: 1.1em; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .feedback.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .feedback.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Dashboard Principal</a>
        <a href="ai_content_assistant.php">Asistente de Contenido IA</a>
        <a href="edit_texts.php">Editar Textos</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>

    <div class="container">
        <h1>Gestionar Susurros IA</h1>

        <?php if ($feedback_message): ?>
            <div class="feedback <?php echo htmlspecialchars($feedback_message['type']); ?>">
                <?php echo htmlspecialchars($feedback_message['text']); ?>
            </div>
        <?php endif; ?>

        <h2>Generar Nuevo Susurro</h2>
        <form action="manage_whispers.php" method="POST">
            <div>
                <label for="character_name">Nombre del Personaje (Obligatorio):</label>
                <input type="text" id="character_name" name="character_name" value="<?php echo htmlspecialchars($character_name_input); ?>" required>
            </div>
            <div>
                <label for="character_bio">Biografía Corta / Palabras Clave (Opcional):</label>
                <textarea id="character_bio" name="character_bio" rows="3"><?php echo htmlspecialchars($character_bio_input); ?></textarea>
            </div>
            <div>
                <label for="character_facts">Hechos Clave (separados por comas, opcional):</label>
                <input type="text" id="character_facts" name="character_facts" value="<?php echo htmlspecialchars($character_facts_input); ?>">
            </div>
            <input type="hidden" name="entity_type" value="<?php echo htmlspecialchars($entity_type_input); ?>">
            <button type="submit" name="action" value="generate_whisper">Generar Susurro</button>
        </form>

        <?php if ($generated_whisper !== null): ?>
            <div class="generated-whisper-section">
                <h2>Susurro Generado</h2>
                <p><strong>Para:</strong> <?php echo htmlspecialchars($character_name_input); ?></p>
                <p><strong>Susurro:</strong></p>
                <blockquote style="font-style: italic; padding-left: 15px; border-left: 3px solid #007bff;">
                    <?php echo nl2br(htmlspecialchars($generated_whisper)); ?>
                </blockquote>

                <hr>
                <h3>Guardar Susurro Generado</h3>
                <form action="manage_whispers.php" method="POST">
                    <input type="hidden" name="character_name_for_save" value="<?php echo htmlspecialchars($character_name_input); ?>">
                    <input type="hidden" name="generated_whisper_to_save" value="<?php echo htmlspecialchars($generated_whisper); ?>">
                    <input type="hidden" name="entity_type" value="<?php echo htmlspecialchars($entity_type_input); ?>">

                    <div>
                        <label for="text_id_to_save">ID del Texto para Guardar:</label>
                        <input type="text" id="text_id_to_save" name="text_id_to_save" value="<?php echo htmlspecialchars($suggested_text_id); ?>" required>
                        <small>Sugerencia basada en el nombre. Puede editarlo. Si el ID ya existe, se sobrescribirá.</small>
                    </div>
                    <button type="submit" name="action" value="save_whisper">Guardar Susurro</button>
                </form>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
