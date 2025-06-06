<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
require_once __DIR__ . '/db_connect.php'; // $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['feedback_message'] = 'Acceso no válido.';
    $_SESSION['feedback_type'] = 'error';
    header("Location: ai_recommendations.php");
    exit;
}

$text_id = $_POST['text_id'] ?? null;
$suggestion_type = $_POST['suggestion_type'] ?? null;
$suggestion_content = $_POST['suggestion_content'] ?? null;
$current_sort = $_POST['current_sort'] ?? 'date_desc'; // To return to the same sort order

if (empty($text_id) || empty($suggestion_type) || $suggestion_content === null) {
    $_SESSION['feedback_message'] = 'Datos insuficientes para aplicar la sugerencia. Faltan text_id, suggestion_type o suggestion_content.';
    $_SESSION['feedback_type'] = 'error';
    header("Location: ai_recommendations.php?sort=" . urlencode($current_sort));
    exit;
}

try {
    $stmt_orig = $pdo->prepare("SELECT text_content FROM site_texts WHERE text_id = :text_id");
    $stmt_orig->bindParam(':text_id', $text_id);
    $stmt_orig->execute();
    $original_text_row = $stmt_orig->fetch(PDO::FETCH_ASSOC);

    if (!$original_text_row) {
        $_SESSION['feedback_message'] = 'Texto original no encontrado para ID: ' . htmlspecialchars($text_id);
        $_SESSION['feedback_type'] = 'error';
        header("Location: ai_recommendations.php?sort=" . urlencode($current_sort));
        exit;
    }
    $original_content = $original_text_row['text_content'];
    $new_content = $original_content;

    $applied_description = '';

    switch ($suggestion_type) {
        case 'append_summary':
            $new_content = $original_content . "\n\n<hr class='ai-separator'>\n<div class='ai-generated-summary'>\n";
            $new_content .= "<h3>Resumen Sugerido por IA</h3>\n<p>" . nl2br(htmlspecialchars($suggestion_content)) . "</p>\n</div>\n";
            $applied_description = 'Resumen de IA añadido a ';
            break;
        case 'append_keywords':
            $new_content = $original_content . "\n\n<div class='ai-suggested-keywords' style='font-size:0.9em; color:#555; margin-top:10px; padding: 5px; border: 1px solid #eee;'>\n";
            $new_content .= "<strong>Palabras Clave Sugeridas por IA:</strong> " . htmlspecialchars($suggestion_content) . "\n</div>\n";
            $applied_description = 'Palabras clave de IA añadidas a ';
            break;
        default:
            $_SESSION['feedback_message'] = 'Tipo de sugerencia no reconocido: ' . htmlspecialchars($suggestion_type);
            $_SESSION['feedback_type'] = 'error';
            header("Location: ai_recommendations.php?sort=" . urlencode($current_sort));
            exit;
    }

    $stmt_update = $pdo->prepare("UPDATE site_texts SET text_content = :text_content, updated_at = CURRENT_TIMESTAMP WHERE text_id = :text_id");
    $stmt_update->bindParam(':text_content', $new_content);
    $stmt_update->bindParam(':text_id', $text_id);

    if ($stmt_update->execute()) {
        $_SESSION['feedback_message'] = $applied_description . htmlspecialchars($text_id) . " exitosamente.";
        $_SESSION['feedback_type'] = 'success';
    } else {
        $_SESSION['feedback_message'] = 'Error al actualizar el texto: ' . htmlspecialchars($text_id);
        $_SESSION['feedback_type'] = 'error';
    }

} catch (PDOException $e) {
    $_SESSION['feedback_message'] = "Error de base de datos: " . $e->getMessage();
    $_SESSION['feedback_type'] = 'error';
    error_log("Apply AI Suggestion DB Error for text_id $text_id: " . $e->getMessage());
}

$redirect_url = "ai_recommendations.php?sort=" . urlencode($current_sort) . "#textrow-" . urlencode($text_id);
header("Location: " . $redirect_url);
exit;
?>
