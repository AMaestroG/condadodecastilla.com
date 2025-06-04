<?php
// This file should be included after db_connect.php and auth.php are available.

/**
 * Fetches a text snippet from the site_texts table.
 * If not found, it can optionally insert a default value.
 *
 * IMPORTANT: Assumes $pdo is globally available or passed in. For this implementation,
 * it's better to require it as a parameter.
 * IMPORTANT: Assumes is_admin_logged_in() is available.
 *
 * @param string $text_id The unique identifier for the text.
 * @param PDO $pdo The PDO database connection object.
 * @param string $default_content Optional default content to display and insert if the text_id is not found and auto_create is true.
 * @param bool $auto_create If true and text_id not found, creates it with default_content.
 * @return string The text content.
 */
function getText(string $text_id, PDO $pdo, string $default_content = '', bool $auto_create = true): string {
    try {
        $stmt = $pdo->prepare("SELECT text_content FROM site_texts WHERE text_id = :text_id");
        $stmt->bindParam(':text_id', $text_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['text_content'];
        } elseif ($auto_create) {
            // Text not found, and auto_create is true. Attempt to insert the default content.
            // This basic INSERT might fail if another request creates it simultaneously.
            // A more robust solution for Progress OpenEdge might involve a MERGE statement or specific error handling for duplicate keys.
            // For simplicity, we'll try to insert and if it fails (e.g. duplicate), we'll re-fetch or just return default.
            try {
                $insert_stmt = $pdo->prepare("INSERT INTO site_texts (text_id, text_content, updated_at) VALUES (:text_id, :text_content, CURRENT_TIMESTAMP)");
                $insert_stmt->bindParam(':text_id', $text_id);
                $insert_stmt->bindParam(':text_content', $default_content);
                $insert_stmt->execute();
                return $default_content; // Return the default content that was just inserted
            } catch (PDOException $e) {
                // Log error or handle duplicate key: $e->getCode() might give '23000' for integrity constraint violation.
                // If it was a duplicate key, the content might now exist, so try fetching again.
                // error_log("getText auto_create for $text_id failed with: " . $e->getMessage());
                $retry_stmt = $pdo->prepare("SELECT text_content FROM site_texts WHERE text_id = :text_id");
                $retry_stmt->bindParam(':text_id', $text_id);
                $retry_stmt->execute();
                $retry_result = $retry_stmt->fetch(PDO::FETCH_ASSOC);
                if ($retry_result) {
                    return $retry_result['text_content'];
                }
                return $default_content; // Fallback to default if insert and retry failed
            }
        }
        // Not found and auto_create is false
        return $default_content;
    } catch (PDOException $e) {
        error_log("PDOException in getText for '" . $text_id . "': " . $e->getMessage());
        // Return default content on error to prevent breaking the page
        return "Error cargando texto ID: '" . htmlspecialchars($text_id) . "'. Contenido por defecto: " . htmlspecialchars($default_content);
    }
}

/**
 * Displays text with an edit button for admins.
 * The text itself is wrapped in the specified HTML tag.
 * The edit button is an anchor link placed immediately after the tag.
 *
 * @param string $text_id The unique identifier for the text.
 * @param PDO $pdo The PDO database connection object.
 * @param string $default_content Default content if the text is not found.
 * @param string $tag The HTML tag to wrap the content in (e.g., 'p', 'span', 'h1', 'div'). Defaults to 'span'.
 * @param string $css_class A CSS class to apply to the wrapping tag.
 * @param string $editor_page_path Path to the main text editor page (e.g., '/edit_texts.php').
 * @return void Outputs HTML directly.
 */
function editableText(string $text_id, PDO $pdo, string $default_content = '', string $tag = 'span', string $css_class = '', string $editor_page_path = '/edit_texts.php'): void {
    // Ensure is_admin_logged_in() is available. It should be included by the calling script.
    // if (!function_exists('is_admin_logged_in')) {
    //     echo "Error: is_admin_logged_in() function not found. Make sure auth.php is included.";
    //     return;
    // }

    // Auto-create with default content if not found. True by default for getText.
    $content = getText($text_id, $pdo, $default_content, true);

    $class_attribute = $css_class ? " class='" . htmlspecialchars($css_class) . "'" : "";

    // Output the editable text content, wrapped in the specified tag
    echo "<" . $tag . $class_attribute . " data-text-id='" . htmlspecialchars($text_id) . "'>";
    echo nl2br(htmlspecialchars($content)); // Use nl2br to respect newlines, and htmlspecialchars for security
    echo "</" . $tag . ">";

    // If admin is logged in, show an edit link/button
    if (is_admin_logged_in()) {
        echo " <a href='" . htmlspecialchars($editor_page_path) . "?edit_id=" . urlencode($text_id) . "' class='edit-text-link' title='Editar: " . htmlspecialchars($text_id) . "'>✏️</a>";
    }
}
?>
