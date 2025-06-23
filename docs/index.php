<?php
require_once __DIR__ . '/../includes/head_common.php';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
use League\CommonMark\CommonMarkConverter;

function render_markdown_file(string $path): string {
    static $converter = null;
    if ($converter === null) {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip'
        ]);
    }
    $markdown = file_get_contents($path);
    return $converter->convert($markdown)->getContent();
}

$readme = __DIR__ . '/README.md';
$docs = array_filter(glob(__DIR__ . '/*.md'), function($f) use ($readme) { return $f !== $readme; });
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Documentación - Condado de Castilla</title>
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/../fragments/header.php'; ?>
<main class="container page-content-block">
    <h1 class="gradient-text">Documentación</h1>
    <section class="section">
        <?php if (file_exists($readme)): ?>
            <?php echo render_markdown_file($readme); ?>
        <?php endif; ?>
    </section>
    <section class="section">
        <h2 class="section-title">Archivos</h2>
        <ul class="doc-list">
            <?php foreach ($docs as $doc): $name = basename($doc); ?>
                <li><a href="<?php echo $name; ?>"><?php echo $name; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>
<?php require_once __DIR__ . '/../fragments/footer.php'; ?>
</body>
</html>
