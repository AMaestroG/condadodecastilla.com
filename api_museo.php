<?php
// api_museo.php

// Include necessary files
require_once 'dashboard/db_connect.php'; // Adjust path as necessary
require_once 'includes/auth.php'; // Adjust path as necessary
require_once 'includes/csrf.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Database connection (assuming $pdo is established in db_connect.php)
global $pdo;

// Helper function to send JSON response
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

// Helper function to get base URL
function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host;
}

// Define the upload directory outside the web root
define('UPLOAD_DIR_BASE', dirname(__DIR__) . '/uploads_storage/museo_piezas/');
define('IMAGE_ENDPOINT', '/serve_museo_image.php');

// Ensure the upload directory exists
if (!is_dir(UPLOAD_DIR_BASE)) {
    if (!mkdir(UPLOAD_DIR_BASE, 0775, true)) {
        json_response(['error' => 'Failed to create upload directory.', 'details' => error_get_last()], 500);
    }
}


// Routing
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string from URI and base path if your script is not in the root
$base_path = ''; // Adjust if your script is in a subdirectory e.g., /api
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace($base_path, '', $path);
$path_segments = explode('/', trim($path, '/'));


// Check if the request is for our API
if ($path_segments[0] !== 'api' || $path_segments[1] !== 'museo' || $path_segments[2] !== 'piezas') {
    json_response(['error' => 'Endpoint not found'], 404);
}

$piece_id = isset($path_segments[3]) ? intval($path_segments[3]) : null;

switch ($request_method) {
    case 'GET':
        if ($piece_id !== null) {
            // GET /api/museo/piezas/{id} - Retrieve a specific piece (Not explicitly in requirements, but good practice)
            // For now, let's stick to the requirement of fetching all.
            // If you want to implement get by ID, this is where it would go.
            json_response(['error' => 'Fetching a single piece by ID is not implemented in this version. Use /api/museo/piezas to get all pieces.'], 404);
        } else {
            // GET /api/museo/piezas - Fetch all pieces
            try {
                $stmt = $pdo->query("SELECT id, titulo, descripcion, autor, imagen_nombre, fecha_subida, notas_adicionales, pos_x, pos_y, pos_z, escala, rotacion_y FROM museo_piezas ORDER BY fecha_subida DESC");
                $piezas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $base_url = get_base_url();
                foreach ($piezas as &$pieza) {
                    $pieza['imagenUrl'] = $base_url . IMAGE_ENDPOINT . '?file=' . urlencode($pieza['imagen_nombre']);
                }

                json_response($piezas);
            } catch (PDOException $e) {
                json_response(['error' => 'Failed to fetch pieces', 'details' => $e->getMessage()], 500);
            }
        }
        break;

    case 'POST':
        // POST /api/museo/piezas - Create a new piece
        if (!is_admin_logged_in()) {
            json_response(['error' => 'Admin authentication required'], 403);
        }
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            json_response(['error' => 'Invalid CSRF token'], 400);
        }

        // Validate inputs
        if (empty($_POST['piezaTitulo'])) {
            json_response(['error' => 'piezaTitulo is required'], 400);
        }
        if (empty($_FILES['piezaImagen'])) {
            json_response(['error' => 'piezaImagen is required'], 400);
        }

        $titulo = $_POST['piezaTitulo'];
        $descripcion = isset($_POST['piezaDescripcion']) ? $_POST['piezaDescripcion'] : null;
        $autor = isset($_POST['piezaAutor']) ? $_POST['piezaAutor'] : null;
        $notas_adicionales = isset($_POST['notasAdicionales']) ? $_POST['notasAdicionales'] : null;
        $pos_x = isset($_POST['posX']) && is_numeric($_POST['posX']) ? (float)$_POST['posX'] : null;
        $pos_y = isset($_POST['posY']) && is_numeric($_POST['posY']) ? (float)$_POST['posY'] : null;
        $pos_z = isset($_POST['posZ']) && is_numeric($_POST['posZ']) ? (float)$_POST['posZ'] : null;
        $escala = isset($_POST['escala']) && is_numeric($_POST['escala']) ? (float)$_POST['escala'] : 1.0;
        $rotacion_y = isset($_POST['rotacionY']) && is_numeric($_POST['rotacionY']) ? (float)$_POST['rotacionY'] : 0.0;


        // Handle file upload
        $image_file = $_FILES['piezaImagen'];
        $image_name = $image_file['name'];
        $image_tmp_name = $image_file['tmp_name'];
        $image_size = $image_file['size'];
        $image_error = $image_file['error'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $image_type = finfo_file($finfo, $image_tmp_name);
        finfo_close($finfo);

        if ($image_error !== UPLOAD_ERR_OK) {
            json_response(['error' => 'File upload error: ' . $image_error], 400);
        }

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image_type, $allowed_types)) {
            json_response(['error' => 'Invalid image type. Allowed: JPG, PNG, GIF.'], 400);
        }

        // Validate file size (max 2MB)
        if ($image_size > 2 * 1024 * 1024) {
            json_response(['error' => 'Image size exceeds 2MB limit.'], 400);
        }

        // Generate unique filename
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $sanitized_original_name = preg_replace("/[^a-zA-Z0-9_.-]/", "_", pathinfo($image_name, PATHINFO_FILENAME));
        $unique_image_name = time() . '_' . $sanitized_original_name . '.' . $image_extension;
        $upload_path = UPLOAD_DIR_BASE . $unique_image_name;

        if (!move_uploaded_file($image_tmp_name, $upload_path)) {
            json_response(['error' => 'Failed to save uploaded image.'], 500);
        }

        // Insert into database
        try {
            $sql = "INSERT INTO museo_piezas (titulo, descripcion, autor, imagen_nombre, notas_adicionales, pos_x, pos_y, pos_z, escala, rotacion_y)
                    VALUES (:titulo, :descripcion, :autor, :imagen_nombre, :notas_adicionales, :pos_x, :pos_y, :pos_z, :escala, :rotacion_y)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':imagen_nombre', $unique_image_name);
            $stmt->bindParam(':notas_adicionales', $notas_adicionales);
            $stmt->bindParam(':pos_x', $pos_x);
            $stmt->bindParam(':pos_y', $pos_y);
            $stmt->bindParam(':pos_z', $pos_z);
            $stmt->bindParam(':escala', $escala);
            $stmt->bindParam(':rotacion_y', $rotacion_y);

            if ($stmt->execute()) {
                $new_piece_id = $pdo->lastInsertId();
                json_response([
                    'message' => 'Piece created successfully',
                    'id' => $new_piece_id,
                    'imagen_nombre' => $unique_image_name
                ], 201);
            } else {
                // If insert fails, attempt to delete uploaded image
                if (file_exists($upload_path)) {
                    unlink($upload_path);
                }
                json_response(['error' => 'Failed to create piece in database.'], 500);
            }
        } catch (PDOException $e) {
            // If DB error, attempt to delete uploaded image
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            json_response(['error' => 'Database error', 'details' => $e->getMessage()], 500);
        }
        break;

    case 'DELETE':
        if ($piece_id === null) {
            json_response(['error' => 'Piece ID is required for deletion.'], 400);
        }

        if (!is_admin_logged_in()) {
            json_response(['error' => 'Admin authentication required'], 403);
        }

        try {
            // First, get the image filename to delete it later
            $stmt_select = $pdo->prepare("SELECT imagen_nombre FROM museo_piezas WHERE id = :id");
            $stmt_select->bindParam(':id', $piece_id, PDO::PARAM_INT);
            $stmt_select->execute();
            $image_info = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if (!$image_info) {
                json_response(['error' => 'Piece not found.'], 404);
            }
            $image_to_delete = $image_info['imagen_nombre'];

            // Delete the piece record from the database
            $stmt_delete = $pdo->prepare("DELETE FROM museo_piezas WHERE id = :id");
            $stmt_delete->bindParam(':id', $piece_id, PDO::PARAM_INT);

            if ($stmt_delete->execute()) {
                if ($stmt_delete->rowCount() > 0) {
                    // If database deletion was successful, delete the image file
                    $image_path_to_delete = UPLOAD_DIR_BASE . $image_to_delete;
                    if (file_exists($image_path_to_delete)) {
                        if (!unlink($image_path_to_delete)) {
                            // Log this error, but don't necessarily fail the whole request if DB part was ok
                            error_log("Failed to delete image file: " . $image_path_to_delete);
                             json_response([
                                'message' => 'Piece deleted from database, but failed to delete image file.',
                                'id' => $piece_id
                            ], 207); // Multi-Status
                        } else {
                             json_response(['message' => 'Piece and image deleted successfully', 'id' => $piece_id]);
                        }
                    } else {
                         json_response(['message' => 'Piece deleted from database, but image file not found.', 'id' => $piece_id], 200);
                    }
                } else {
                    json_response(['error' => 'Piece not found or already deleted.'], 404);
                }
            } else {
                json_response(['error' => 'Failed to delete piece from database.'], 500);
            }
        } catch (PDOException $e) {
            json_response(['error' => 'Database error during deletion', 'details' => $e->getMessage()], 500);
        }
        break;

    default:
        json_response(['error' => 'Method not allowed'], 405);
        break;
}

?>
