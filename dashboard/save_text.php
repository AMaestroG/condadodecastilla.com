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
require_once __DIR__ . '/../includes/csrf.php';

// Ensure user is admin and request is POST
require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['feedback_message'] = 'Acceso no válido.';
    $_SESSION['feedback_type'] = 'error';
    header("Location: edit_texts.php");
    exit;
}

$action = $_POST['action'] ?? null;
$text_id = trim($_POST['text_id'] ?? '');
$text_content = trim($_POST['text_content'] ?? ''); // For create/update

$redirect_url = "edit_texts.php" . ($text_id ? "?edit_id=" . urlencode($text_id) : "");

if (empty($text_id) && ($action === 'update' || $action === 'delete' || $action === 'create')) {
    $_SESSION['feedback_message'] = 'El ID del texto no puede estar vacío.';
    $_SESSION['feedback_type'] = 'error';
    header("Location: $redirect_url");
    exit;
}
// Validate text_id format for create action
if ($action === 'create' && !preg_match('/^[a-zA-Z0-9_\-]+$/', $text_id)) {
    $_SESSION['feedback_message'] = 'ID de texto no válido. Use solo letras, números, guiones bajos o guiones.';
    $_SESSION['feedback_type'] = 'error';
    header("Location: edit_texts.php"); // Redirect to main editor page for new invalid ID
    exit;
}


try {
    switch ($action) {
        case 'create':
            if (empty($text_content)) { // Also check content for create
                 $_SESSION['feedback_message'] = 'El contenido del texto no puede estar vacío para crear.';
                 $_SESSION['feedback_type'] = 'error';
                 break;
            }
            // Check if text_id already exists
            $stmt_check = $pdo->prepare("SELECT text_id FROM site_texts WHERE text_id = :text_id");
            $stmt_check->bindParam(':text_id', $text_id);
            $stmt_check->execute();
            if ($stmt_check->fetch()) {
                $_SESSION['feedback_message'] = "Error: El ID de texto '" . htmlspecialchars($text_id) . "' ya existe.";
                $_SESSION['feedback_type'] = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO site_texts (text_id, text_content, updated_at) VALUES (:text_id, :text_content, CURRENT_TIMESTAMP)");
                $stmt->bindParam(':text_id', $text_id);
                $stmt->bindParam(':text_content', $text_content);
                if ($stmt->execute()) {
                    $_SESSION['feedback_message'] = "Texto '" . htmlspecialchars($text_id) . "' creado exitosamente.";
                    $_SESSION['feedback_type'] = 'success';
                } else {
                    $_SESSION['feedback_message'] = "Error al crear el texto '" . htmlspecialchars($text_id) . "'.";
                    $_SESSION['feedback_type'] = 'error';
                }
            }
            $redirect_url = "edit_texts.php?edit_id=" . urlencode($text_id); // Ensure highlight after creation attempt
            break;

        case 'update':
            // Content can be empty for an update (e.g. admin wants to clear it)
            $stmt = $pdo->prepare("UPDATE site_texts SET text_content = :text_content, updated_at = CURRENT_TIMESTAMP WHERE text_id = :text_id");
            $stmt->bindParam(':text_content', $text_content);
            $stmt->bindParam(':text_id', $text_id);
            if ($stmt->execute()) {
                $_SESSION['feedback_message'] = "Texto '" . htmlspecialchars($text_id) . "' actualizado exitosamente.";
                $_SESSION['feedback_type'] = 'success';
            } else {
                $_SESSION['feedback_message'] = "Error al actualizar el texto '" . htmlspecialchars($text_id) . "'.";
                $_SESSION['feedback_type'] = 'error';
            }
            break;

        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM site_texts WHERE text_id = :text_id");
            $stmt->bindParam(':text_id', $text_id);
            if ($stmt->execute()) {
                $_SESSION['feedback_message'] = "Texto '" . htmlspecialchars($text_id) . "' eliminado exitosamente.";
                $_SESSION['feedback_type'] = 'success';
                $redirect_url = "edit_texts.php"; // Go back to main list after delete
            } else {
                $_SESSION['feedback_message'] = "Error al eliminar el texto '" . htmlspecialchars($text_id) . "'.";
                $_SESSION['feedback_type'] = 'error';
            }
            break;

        default:
            $_SESSION['feedback_message'] = 'Acción no reconocida.';
            $_SESSION['feedback_type'] = 'error';
            $redirect_url = "edit_texts.php";
    }
} catch (PDOException $e) {
    $_SESSION['feedback_message'] = "Error de base de datos: " . $e->getMessage();
    $_SESSION['feedback_type'] = 'error';
    // Log the detailed error for the admin/developer
    error_log("save_text.php PDOException for action '$action' on text_id '$text_id': " . $e->getMessage());
}

if (!headers_sent()) {
    header("Location: " . $redirect_url);
} else {
    // Fallback if headers already sent
    echo "Operación procesada. <a href='" . htmlspecialchars($redirect_url) . "'>Volver al editor</a>.";
    echo "<script>window.location.href='" . htmlspecialchars($redirect_url) . "';</script>";
}
exit;
?>
