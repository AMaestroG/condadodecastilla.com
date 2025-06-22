<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_connect.php';
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
    return;
}
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

$edit_id_raw = $_GET['edit_id'] ?? null;
$edit_id_highlight = null;
if ($edit_id_raw) {
    // Sanitizar el ID: permitir solo alfanuméricos, guiones bajos y guiones.
    $edit_id_highlight = preg_replace('/[^a-zA-Z0-9_-]/', '', $edit_id_raw);
    // Opcional: loguear si $edit_id_raw contenía caracteres no válidos.
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Textos del Sitio</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
</head>
<body class="alabaster-bg admin-page">
    <?php require_once __DIR__ . '/../fragments/admin_header.php'; ?>
    <div class="admin-container wide">
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
                <button type="submit" class="add-new-button">Añadir Nuevo Texto</button>
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
                        <button type="submit" class="btn-primary">Guardar Cambios</button>
                        <span class="text-meta">Última actualización: <?php echo htmlspecialchars($text['updated_at'] ? date('d/m/Y H:i:s', strtotime($text['updated_at'])) : 'N/A'); ?></span>
                         <button type="submit" name="action" value="delete" class="btn-danger ml-10" onclick="return confirm('¿Está seguro de que desea eliminar este texto? Esta acción no se puede deshacer.');">Eliminar</button>
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
