<?php
// api_galeria.php - Endpoint para manejar la galeria colaborativa

require_once __DIR__ . '/includes/db_connect.php';
require_once 'includes/auth.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

global $pdo;

function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host;
}

// Directorio de subida
define('UPLOAD_DIR_BASE', 'uploads/galeria/');

if (!is_dir(UPLOAD_DIR_BASE)) {
    if (!mkdir(UPLOAD_DIR_BASE, 0775, true)) {
        json_response(['error' => 'Failed to create upload directory.', 'details' => error_get_last()], 500);
    }
}

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri    = $_SERVER['REQUEST_URI'];

$base_path  = '';
$path       = parse_url($request_uri, PHP_URL_PATH);
$path       = str_replace($base_path, '', $path);
$path_segments = explode('/', trim($path, '/'));

if ($path_segments[0] !== 'api' || $path_segments[1] !== 'galeria' || $path_segments[2] !== 'fotos') {
    json_response(['error' => 'Endpoint not found'], 404);
}

$foto_id = isset($path_segments[3]) ? intval($path_segments[3]) : null;

switch ($request_method) {
    case 'GET':
        if ($foto_id !== null) {
            json_response(['error' => 'Fetching a single photo is not implemented.'], 404);
        } else {
            try {
                $stmt = $pdo->query("SELECT id, titulo, descripcion, autor, imagen_nombre, fecha_subida FROM fotos_galeria ORDER BY fecha_subida DESC");
                $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $base_url = get_base_url();
                $upload_path_segment = trim(UPLOAD_DIR_BASE, '/');
                foreach ($fotos as &$foto) {
                    $foto['imagenUrl'] = $base_url . '/' . $upload_path_segment . '/' . $foto['imagen_nombre'];
                }
                json_response($fotos);
            } catch (PDOException $e) {
                json_response(['error' => 'Failed to fetch photos', 'details' => $e->getMessage()], 500);
            }
        }
        break;

    case 'POST':
        // No se requiere autenticación para subir fotos
        if (empty($_POST['photoTitulo'])) {
            json_response(['error' => 'photoTitulo is required'], 400);
        }
        if (empty($_FILES['photoFile'])) {
            json_response(['error' => 'photoFile is required'], 400);
        }

        $titulo = $_POST['photoTitulo'];
        $descripcion = isset($_POST['photoDescripcion']) ? $_POST['photoDescripcion'] : null;
        $autor = isset($_POST['photoAutor']) ? $_POST['photoAutor'] : 'Anónimo';

        $image_file = $_FILES['photoFile'];
        $image_name = $image_file['name'];
        $image_tmp_name = $image_file['tmp_name'];
        $image_size = $image_file['size'];
        $image_error = $image_file['error'];
        $image_type = $image_file['type'];

        if ($image_error !== UPLOAD_ERR_OK) {
            json_response(['error' => 'File upload error: ' . $image_error], 400);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image_type, $allowed_types)) {
            json_response(['error' => 'Invalid image type. Allowed: JPG, PNG, GIF.'], 400);
        }

        if ($image_size > 2 * 1024 * 1024) {
            json_response(['error' => 'Image size exceeds 2MB limit.'], 400);
        }

        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $sanitized_original_name = preg_replace('/[^a-zA-Z0-9_.-]/', '_', pathinfo($image_name, PATHINFO_FILENAME));
        $unique_image_name = time() . '_' . $sanitized_original_name . '.' . $image_extension;
        $upload_path = UPLOAD_DIR_BASE . $unique_image_name;

        if (!move_uploaded_file($image_tmp_name, $upload_path)) {
            json_response(['error' => 'Failed to save uploaded image.'], 500);
        }

        try {
            $sql = "INSERT INTO fotos_galeria (titulo, descripcion, autor, imagen_nombre) VALUES (:titulo, :descripcion, :autor, :imagen_nombre)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':imagen_nombre', $unique_image_name);
            if ($stmt->execute()) {
                $new_id = $pdo->lastInsertId();
                json_response(['mensaje' => 'Foto subida correctamente', 'id' => $new_id, 'imagen_nombre' => $unique_image_name], 201);
            } else {
                if (file_exists($upload_path)) {
                    unlink($upload_path);
                }
                json_response(['error' => 'Failed to insert photo in database.'], 500);
            }
        } catch (PDOException $e) {
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            json_response(['error' => 'Database error', 'details' => $e->getMessage()], 500);
        }
        break;

    default:
        json_response(['error' => 'Method not allowed'], 405);
        break;
}
