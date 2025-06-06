<?php
require_once __DIR__ . '/../dashboard/db_connect.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/ai_utils.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    die('Artículo no válido');
}

try {
    $stmt = $pdo->prepare("SELECT id, title, content, image_filename, created_at FROM blog_posts WHERE id = :id");
    $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        die('Artículo no encontrado');
    }

    $stmtC = $pdo->prepare("SELECT author, comment, created_at FROM blog_comments WHERE post_id = :id ORDER BY created_at ASC");
    $stmtC->bindParam(':id', $post_id, PDO::PARAM_INT);
    $stmtC->execute();
    $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching post: ' . $e->getMessage());
    die('Error al cargar el artículo');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <script src="js/post.js" defer></script>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <small>Publicado el <?php echo htmlspecialchars(date('d/m/Y', strtotime($post['created_at']))); ?></small>
        <?php if ($post['image_filename']): ?>
            <div><img src="/serve_blog_image.php?file=<?php echo urlencode($post['image_filename']); ?>" alt="Imagen del artículo" style="max-width:400px;"></div>
        <?php endif; ?>
        <div id="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
        <button id="btn-summary">Resumen IA</button>
        <button id="btn-correct">Corrección IA</button>
        <button class="lang-btn" data-lang="en-ai">Traducir a inglés</button>
        <button class="lang-btn" data-lang="fr-ai">Traducir a francés</button>
        <div id="ai-result" style="margin-top:15px;"></div>

        <h2>Comentarios</h2>
        <?php if (empty($comments)): ?>
            <p>No hay comentarios.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <div style="border-bottom:1px solid #ccc; margin-bottom:10px;">
                    <strong><?php echo htmlspecialchars($c['author']); ?></strong> - <?php echo htmlspecialchars(date('d/m/Y', strtotime($c['created_at']))); ?>
                    <p><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <h3>Nuevo comentario</h3>
        <form action="add_comment.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <div>
                <label for="author">Nombre:</label><br>
                <input type="text" id="author" name="author">
            </div>
            <div>
                <label for="comment">Comentario:</label><br>
                <textarea id="comment" name="comment" rows="4" required></textarea>
            </div>
            <button type="submit">Enviar</button>
        </form>
        <p><a href="index.php">Volver al blog</a></p>
    </div>
</body>
</html>
