<?php
require_once __DIR__ . '/../includes/head_common.php';
require_once __DIR__ . '/../includes/markdown_utils.php';

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
