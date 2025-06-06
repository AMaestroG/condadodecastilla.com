<?php
// /includes/text_manager.php

// Asegurarse de que auth.php (para is_admin_logged_in) está incluido.
// Esto podría hacerse en cada página que usa editableText, o aquí condicionalmente.
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
if (!function_exists('is_admin_logged_in')) { // Evitar re-declaración si ya está incluido
    require_once __DIR__ . '/auth.php';
}

/**
 * Obtiene el contenido de un texto desde la base de datos.
 * Si no se encuentra, inserta el texto por defecto y lo devuelve.
 */
function getTextContentFromDB(string $text_id, ?PDO $pdo, string $default_text): string {
    if ($pdo === null) {
        // Si no hay conexión a la base de datos, usar el valor por defecto
        return $default_text;
    }
    try {
        $stmt = $pdo->prepare("SELECT text_content FROM site_texts WHERE text_id = :text_id");
        $stmt->bindParam(':text_id', $text_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['text_content'];
        } else {
            // El texto no existe, lo insertamos con el valor por defecto
            try {
                $insert_stmt = $pdo->prepare("INSERT INTO site_texts (text_id, text_content, updated_at) VALUES (:text_id, :text_content, CURRENT_TIMESTAMP)");
                $insert_stmt->bindParam(':text_id', $text_id);
                $insert_stmt->bindParam(':text_content', $default_text);
                $insert_stmt->execute();
                return $default_text;
            } catch (PDOException $e) {
                // Log error, pero devolver el texto por defecto para que la página no se rompa
                error_log("text_manager.php - Error al insertar texto por defecto para ID '$text_id': " . $e->getMessage());
                return $default_text; // Devuelve el texto por defecto si la inserción falla
            }
        }
    } catch (PDOException $e) {
        error_log("text_manager.php - Error al obtener texto para ID '$text_id': " . $e->getMessage());
        return $default_text; // Devuelve el texto por defecto en caso de error de BD
    }
}

/**
 * Muestra un texto editable.
 * Si el administrador está logueado, muestra un enlace para editar el texto.
 *
 * @param string $text_id El identificador único del texto.
 * @param PDO $pdo La instancia de conexión a la base de datos.
 * @param string $default_text El texto a mostrar si no se encuentra en la BD.
 * @param string $html_tag La etiqueta HTML que envolverá el texto (ej: 'p', 'h1', 'span').
 * @param string $css_classes Clases CSS adicionales para la etiqueta HTML contenedora.
 * @param bool $allow_html Si se permite HTML en el contenido (default: false, se usa htmlspecialchars).
 */
function editableText(string $text_id, ?PDO $pdo, string $default_text, string $html_tag = 'span', string $css_classes = '', bool $allow_html = false) {
    if ($pdo === null) {
        $content = $default_text;
    } else {
        // Obtener el contenido del texto. Esta función ahora maneja la creación si no existe.
        $content = getTextContentFromDB($text_id, $pdo, $default_text);
    }

    // Escapar HTML si no está permitido, para prevenir XSS
    $display_content = $allow_html ? $content : htmlspecialchars($content);

    $output = "<" . htmlspecialchars($html_tag);
    if (!empty($css_classes)) {
        $output .= " class=\"" . htmlspecialchars($css_classes) . "\"";
    }
    // Añadir data-attribute para identificarlo en la página de edición si es necesario
    $output .= " data-text-id=\"" . htmlspecialchars($text_id) . "\">";
    $output .= $display_content;

    // Añadir enlace de edición si el administrador está logueado
    if (is_admin_logged_in()) {
        // Determinar la ruta correcta a edit_texts.php
        // Asumimos que text_manager.php está en /includes/ y edit_texts.php en /dashboard/
        // Si esta página (donde se llama editableText) está en la raíz, el enlace es correcto.
        // Si esta página está en una subcarpeta, se necesitaría ajustar el path.
        // Una solución más robusta sería definir una constante con la URL base del dashboard.
        $edit_url = '/dashboard/edit_texts.php?edit_id=' . urlencode($text_id);
        $output .= " <a href=\"" . htmlspecialchars($edit_url) . "\" class=\"edit-text-link\" title=\"Editar este texto\">✏️</a>";
    }

    $output .= "</" . htmlspecialchars($html_tag) . ">";
    echo $output;
}

// La línea 101 que causaba el error ha sido eliminada o la lógica ha sido reemplazada por getTextContentFromDB.
// El error 'gettext() expects exactly 1 argument, 4 given' ya no debería ocurrir.
