<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once 'includes/auth.php'; // For require_admin_login()

header('Content-Type: application/json'); // Set content type to JSON for the response

// Require admin login; redirect will not happen for AJAX, but good for security
if (!is_admin_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado. Se requiere autenticación de administrador.']);
    exit;
}

// Check if the request method is POST (as per the modified JS in galeria_colaborativa.php)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método de solicitud no válido. Se esperaba POST.']);
    exit;
}

$filename = $_POST['filename'] ?? null;

if (empty($filename)) {
    echo json_encode(['success' => false, 'error' => 'Nombre de archivo no proporcionado.']);
    exit;
}

// Security: Basic filename validation to prevent path traversal and invalid characters.
// Allow alphanumeric, dots, underscores, hyphens.
if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $filename) || strpos($filename, '..') !== false) {
    echo json_encode(['success' => false, 'error' => 'Nombre de archivo no válido o contiene caracteres no permitidos.']);
    exit;
}

// Define the gallery directory
$gallery_dir = __DIR__ . '/assets/img/galeria_colaborativa/'; // Use absolute path from this script's location
$filepath = $gallery_dir . $filename;

// Normalize the path to resolve any '..' (though preg_match should prevent it)
// and ensure it's a real path.
$real_gallery_dir = realpath($gallery_dir);
$real_filepath = realpath($filepath);

if ($real_gallery_dir === false) {
    error_log("Error: El directorio de la galería '$gallery_dir' no es accesible o no existe.");
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor: Configuración incorrecta del directorio de la galería.']);
    exit;
}

// Security check: Ensure the resolved file path is actually within the gallery directory
if (!$real_filepath || strpos($real_filepath, $real_gallery_dir) !== 0) {
    echo json_encode(['success' => false, 'error' => 'Intento de acceso a archivo fuera de los límites de la galería.']);
    exit;
}


if (file_exists($real_filepath)) {
    if (unlink($real_filepath)) {
        echo json_encode(['success' => true, 'mensaje' => 'Fotografía "' . htmlspecialchars($filename) . '" borrada con éxito.']);
    } else {
        // Log this error on the server for admin to check permissions
        error_log("Error al borrar el archivo '$real_filepath'. Verifique los permisos.");
        echo json_encode(['success' => false, 'error' => 'No se pudo borrar la fotografía. Posible problema de permisos en el servidor.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'La fotografía "' . htmlspecialchars($filename) . '" no fue encontrada en el servidor.']);
}
exit;
?>
