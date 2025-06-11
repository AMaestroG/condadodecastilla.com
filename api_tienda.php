<?php
require_once 'dashboard/db_connect.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

global $pdo;

function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$tiendaUpload = getenv('TIENDA_UPLOAD_DIR');
if (!$tiendaUpload) {
    $tiendaUpload = dirname(__DIR__) . '/uploads_storage/tienda_productos';
}
$tiendaUpload = rtrim($tiendaUpload, '/') . '/';
define('UPLOAD_DIR_BASE', $tiendaUpload);

if (!is_dir(UPLOAD_DIR_BASE)) {
    @mkdir(UPLOAD_DIR_BASE, 0775, true);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query("SELECT id, nombre, descripcion, precio, imagen_nombre, stock, created_at FROM tienda_productos ORDER BY id DESC");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response($items);
        } catch (PDOException $e) {
            json_response(['error' => 'DB error', 'details' => $e->getMessage()], 500);
        }
        break;
    case 'POST':
        if (!is_admin_logged_in()) {
            json_response(['error' => 'Admin authentication required'], 403);
        }
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            json_response(['error' => 'Invalid CSRF token'], 400);
        }
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        if ($nombre === '' || empty($_FILES['imagen']['name'])) {
            json_response(['error' => 'Nombre e imagen requeridos'], 400);
        }
        $file = $_FILES['imagen'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg','image/png','image/gif'];
        if ($file['error'] !== UPLOAD_ERR_OK || !in_array($mime, $allowed)) {
            json_response(['error' => 'Tipo de imagen no válido'], 400);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = time().'_'.$safe.'.'.$ext;
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR_BASE.$filename)) {
            json_response(['error' => 'No se pudo guardar la imagen'], 500);
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO tienda_productos (nombre, descripcion, precio, imagen_nombre, stock) VALUES (:n,:d,:p,:i,:s)");
            $stmt->execute([':n'=>$nombre, ':d'=>$descripcion, ':p'=>$precio, ':i'=>$filename, ':s'=>$stock]);
            $id = $pdo->lastInsertId();
            json_response(['message' => 'Producto creado', 'id' => $id, 'imagen_nombre' => $filename], 201);
        } catch (PDOException $e) {
            @unlink(UPLOAD_DIR_BASE.$filename);
            json_response(['error' => 'DB error', 'details' => $e->getMessage()], 500);
        }
        break;
    case 'DELETE':
        if (!is_admin_logged_in()) {
            json_response(['error' => 'Admin authentication required'], 403);
        }
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['error' => 'ID requerido'], 400);
        }
        try {
            $stmt = $pdo->prepare("SELECT imagen_nombre FROM tienda_productos WHERE id=:id");
            $stmt->execute([':id'=>$id]);
            $img = $stmt->fetchColumn();
            $stmt = $pdo->prepare("DELETE FROM tienda_productos WHERE id=:id");
            $stmt->execute([':id'=>$id]);
            if ($stmt->rowCount()) {
                if ($img && file_exists(UPLOAD_DIR_BASE.$img)) {
                    @unlink(UPLOAD_DIR_BASE.$img);
                }
                json_response(['message' => 'Producto eliminado']);
            } else {
                json_response(['error' => 'Producto no encontrado'], 404);
            }
        } catch (PDOException $e) {
            json_response(['error' => 'DB error', 'details' => $e->getMessage()], 500);
        }
        break;
    default:
        json_response(['error' => 'Método no permitido'], 405);
}
?>
