<?php
require_once __DIR__ . '/../includes/head_common.php';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
use League\CommonMark\CommonMarkConverter;

function render_markdown(string $file): string {
    static $converter = null;
    if ($converter === null) {
        $converter = new CommonMarkConverter();
    }
    return $converter->convert(file_get_contents($file))->getContent();
}

$catalog = __DIR__ . '/../docs/script_catalog.md';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Catálogo de Scripts</title>
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/../fragments/header.php'; ?>
<main class="container page-content-block">
    <h1 class="gradient-text">Catálogo de Scripts</h1>
    <section class="section">
        <?php if (file_exists($catalog)) echo render_markdown($catalog); ?>
    </section>
</main>
<?php require_once __DIR__ . '/../fragments/footer.php'; ?>
</body>
</html>
