<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

// Optional catalog file listing scripts. Falls back to directory scan
$catalog = __DIR__ . '/scripts_catalog.json';
$catalog_missing = false;
$files = [];
if (file_exists($catalog)) {
    $json = file_get_contents($catalog);
    $decoded = json_decode($json, true);
    if (is_array($decoded)) {
        $files = $decoded;
    }
} else {
    $catalog_missing = true;
    $dir = __DIR__;
    $files = array_filter(scandir($dir), function ($f) {
        return is_file(__DIR__ . '/' . $f) && $f !== 'index.php';
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Scripts Disponibles</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/../fragments/header.php'; ?>
<main class="container-epic page-content-block">
    <h1 class="gradient-text">Listado de Scripts</h1>
    <?php if ($catalog_missing): ?>
        <p class="warning">Archivo de catálogo no encontrado.</p>
    <?php endif; ?>
    <ul>
        <?php foreach ($files as $f): ?>
            <li><a href="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <p><a class="cta-button" href="/">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/../fragments/footer.php'; ?>
</body>
</html>
