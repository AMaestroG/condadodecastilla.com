<?php
require_once __DIR__ . '/includes/head_common.php';
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

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

use League\CommonMark\CommonMarkConverter;

function render_markdown(string $markdown): string {
    static $converter = null;
    if ($converter === null) {
        $converter = new CommonMarkConverter();
    }

    return $converter->convert($markdown)->getContent();
}

$posts = get_blog_posts();
$post_slug = isset($_GET['post']) ? $_GET['post'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Blog</title>
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="alabaster-bg">
<?php require_once __DIR__.'/fragments/header.php'; ?>
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
<?php require_once __DIR__.'/fragments/footer.php'; ?>

<?php if ($post_slug && isset($posts[$post_slug])): ?>
<script type="application/ld+json">
<?php
    $published = date('c', filemtime($posts[$post_slug]['file']));
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $posts[$post_slug]['title'],
        'datePublished' => $published,
        'author' => [
            '@type' => 'Organization',
            'name' => 'Condado de Castilla'
        ]
    ];
    echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
?>
</script>
<?php endif; ?>

</body>
</html>
