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

$post_id = intval($_POST['post_id'] ?? 0);
$author = trim($_POST['author'] ?? 'Anónimo');
$comment = trim($_POST['comment'] ?? '');

if ($post_id <= 0 || $comment === '') {
    die('Datos de comentario inválidos');
}

try {
    $stmt = $pdo->prepare("INSERT INTO blog_comments (post_id, author, comment) VALUES (:post_id, :author, :comment)");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':comment', $comment);
    $stmt->execute();
    header('Location: post.php?id=' . urlencode($post_id));
} catch (PDOException $e) {
    error_log('Error adding comment: ' . $e->getMessage());
    die('Error al guardar el comentario');
}
