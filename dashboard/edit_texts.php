<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/db_connect.php'; // Provides $pdo
require_once __DIR__ . '/../includes/text_manager.php'; // For getText, though not directly used for display here
require_once __DIR__ . '/../includes/csrf.php';

// Ensure user is admin
require_admin_login(); // Redirect to login if not admin

$feedback_message = '';
$feedback_type = ''; // 'success' or 'error'

// Handle potential feedback from save_text.php redirect
if (isset($_SESSION['feedback_message'])) {
    $feedback_message = $_SESSION['feedback_message'];
    $feedback_type = $_SESSION['feedback_type'] ?? 'info';
    unset($_SESSION['feedback_message']);
    unset($_SESSION['feedback_type']);
}

// Fetch all editable texts from the database
$texts = [];
try {
    $stmt = $pdo->query("SELECT text_id, text_content, updated_at FROM site_texts ORDER BY text_id ASC");
    $texts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedback_message = "Error al cargar los textos: " . $e->getMessage();
    $feedback_type = 'error';
    // Log this error for admin/developer
    error_log("edit_texts.php PDOException: " . $e->getMessage());
}

$edit_id_highlight = $_GET['edit_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Textos del Sitio</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css"> <!-- Main theme -->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .feedback { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .feedback.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .feedback.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .feedback.info { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .text-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: #f9f9f9; }
        .text-item.highlight { border-color: #007bff; background-color: #e7f3ff; }
        .text-item strong { display: block; margin-bottom: 5px; color: #0056b3; }
        textarea { width: 98%; min-height: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; font-family: inherit; font-size: inherit; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        button.add-new-button { background-color: #28a745; margin-bottom:20px}
        button.add-new-button:hover { background-color: #1e7e34; }
        .text-meta { font-size: 0.8em; color: #666; margin-top: 5px; }
        label { font-weight: bold; margin-bottom: 5px; display: inline-block; }
        input[type="text"] { width: calc(98% - 100px); padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; }
        .add-text-form { margin-bottom: 30px; padding: 15px; border: 1px solid #28a745; border-radius: 5px; background-color: #f0fff0; }
        nav { margin-bottom: 20px; }
        nav a { text-decoration: none; padding: 8px 15px; background-color: #6c757d; color: white; border-radius: 4px; margin-right: 10px; }
        nav a:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="../index.php">Volver al Inicio</a> <?php // TODO: Link to index.php if it becomes dynamic ?>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
        <h1>Editor de Textos del Sitio</h1>

        <?php if ($feedback_message): ?>
            <div class="feedback <?php echo htmlspecialchars($feedback_type); ?>"><?php echo htmlspecialchars($feedback_message); ?></div>
        <?php endif; ?>

        <h2>Añadir Nuevo Texto</h2>
        <div class="add-text-form">
            <form action="save_text.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                <div>
                    <label for="new_text_id">ID del Texto (único, sin espacios, ej: 'titulo_principal'):</label><br>
                    <input type="text" id="new_text_id" name="text_id" required pattern="[a-zA-Z0-9_\-]+">
                </div>
                <div>
                    <label for="new_text_content">Contenido:</label><br>
                    <textarea id="new_text_content" name="text_content" required></textarea>
                </div>
                <input type="hidden" name="action" value="create">
                <button type_submit" class="add-new-button">Añadir Nuevo Texto</button>
            </form>
        </div>

        <h2>Textos Existentes</h2>
        <?php if (empty($texts)): ?>
            <p>No hay textos para editar todavía. Puede añadir uno usando el formulario de arriba.</p>
        <?php else: ?>
            <?php foreach ($texts as $text): ?>
                <div class="text-item <?php echo ($edit_id_highlight === $text['text_id']) ? 'highlight' : ''; ?>" id="text-<?php echo htmlspecialchars($text['text_id']); ?>">
                    <form action="save_text.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                        <strong>ID: <?php echo htmlspecialchars($text['text_id']); ?></strong>
                        <input type="hidden" name="text_id" value="<?php echo htmlspecialchars($text['text_id']); ?>">
                        <input type="hidden" name="action" value="update">
                        <div>
                            <label for="content-<?php echo htmlspecialchars($text['text_id']); ?>">Contenido:</label>
                            <textarea id="content-<?php echo htmlspecialchars($text['text_id']); ?>" name="text_content" rows="5"><?php echo htmlspecialchars($text['text_content']); ?></textarea>
                        </div>
                        <button type="submit">Guardar Cambios</button>
                        <span class="text-meta">Última actualización: <?php echo htmlspecialchars($text['updated_at'] ? date('d/m/Y H:i:s', strtotime($text['updated_at'])) : 'N/A'); ?></span>
                         <button type="submit" name="action" value="delete" style="background-color: #dc3545; margin-left: 10px;" onclick="return confirm('¿Está seguro de que desea eliminar este texto? Esta acción no se puede deshacer.');">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
        // Scroll to highlighted item if present
        <?php if ($edit_id_highlight): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('text-<?php echo htmlspecialchars($edit_id_highlight); ?>');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
