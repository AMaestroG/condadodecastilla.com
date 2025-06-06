<?php
require_once __DIR__ . '/../dashboard/db_connect.php';
require_once __DIR__ . '/../includes/csrf.php';

try {
    $stmt = $pdo->query("SELECT id, title, content, image_filename, created_at FROM blog_posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $posts = [];
    error_log('Error fetching blog posts: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div class="container">
        <h1>Blog de la Comunidad</h1>
        <p><a href="new_post.php">Escribir nuevo artículo</a></p>
        <?php if (empty($posts)): ?>
            <p>No hay artículos aún.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article style="margin-bottom:20px; border-bottom:1px solid #ccc; padding-bottom:15px;">
                    <h2><a href="post.php?id=<?php echo urlencode($post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                    <small>Publicado el <?php echo htmlspecialchars(date('d/m/Y', strtotime($post['created_at']))); ?></small>
                    <?php if (!empty($post['image_filename'])): ?>
                        <div><img src="/serve_blog_image.php?file=<?php echo urlencode($post['image_filename']); ?>" alt="Imagen del artículo" style="max-width:200px;"></div>
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars(mb_substr(strip_tags($post['content']),0,200)); ?>...</p>
                    <p><a href="post.php?id=<?php echo urlencode($post['id']); ?>">Leer más</a></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
