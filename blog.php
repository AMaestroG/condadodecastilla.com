<?php
require_once __DIR__ . '/includes/head_common.php';
require_once __DIR__ . '/_header.php';

function get_blog_posts() {
    $posts = [];
    foreach (glob(__DIR__ . '/contenido/blog/*.md') as $file) {
        $lines = file($file);
        if (!$lines) continue;
        $title = trim(ltrim($lines[0], "# \t"));
        $slug = basename($file, '.md');
        $posts[$slug] = ['title' => $title, 'file' => $file];
    }
    ksort($posts);
    return $posts;
}

function render_markdown($markdown) {
    $markdown = preg_replace('/\r\n?/', "\n", $markdown);
    $html = htmlspecialchars($markdown, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    foreach ([6,5,4,3,2,1] as $h) {
        $pattern = '/^' . str_repeat('#', $h) . '\s*(.+)$/m';
        $replace = '<h'.$h.'>$1</h'.$h.'>';
        $html = preg_replace($pattern, $replace, $html);
    }
    // paragraphs
    $html = preg_replace('/\n{2,}/', "</p><p>", $html);
    $html = '<p>' . $html . '</p>';
    return $html;
}

$posts = get_blog_posts();
$post_slug = isset($_GET['post']) ? $_GET['post'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php include __DIR__.'/_header.php'; ?>
<main class="container page-content-block">
<?php if ($post_slug && isset($posts[$post_slug])): ?>
    <article class="blog-post">
        <h1><?php echo htmlspecialchars($posts[$post_slug]['title']); ?></h1>
        <?php echo render_markdown(file_get_contents($posts[$post_slug]['file'])); ?>
        <p><a href="blog.php">&larr; Volver al listado</a></p>
    </article>
<?php else: ?>
    <h1>Blog</h1>
    <ul class="blog-list">
    <?php foreach ($posts as $slug => $info): ?>
        <li><a href="blog.php?post=<?php echo urlencode($slug); ?>"><?php echo htmlspecialchars($info['title']); ?></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
</main>
<?php include __DIR__.'/_footer.php'; ?>
<?php include __DIR__.'/fragments/ai-drawer.html'; ?>
<script src="/assets/js/main.js"></script>
<script src="/js/layout.js"></script>
</body>
</html>
