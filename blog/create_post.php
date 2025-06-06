<?php
require_once __DIR__ . '/../dashboard/db_connect.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('CSRF token inválido');
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$image_filename = null;

if ($title === '' || $content === '') {
    die('Título y contenido son obligatorios.');
}

$upload_dir = dirname(__DIR__) . '/uploads_storage/blog_photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

if (!empty($_FILES['image']['tmp_name'])) {
    $file = $_FILES['image'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg','image/png','image/gif'];
        if (in_array($type, $allowed) && $file['size'] <= 2*1024*1024) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/','_', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $upload_dir . $safe_name);
            $image_filename = $safe_name;
        }
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, image_filename) VALUES (:title, :content, :image)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':image', $image_filename);
    $stmt->execute();
    $post_id = $pdo->lastInsertId();
    header('Location: post.php?id=' . urlencode($post_id));
} catch (PDOException $e) {
    error_log('Error creating post: ' . $e->getMessage());
    die('Error al guardar el artículo');
}
