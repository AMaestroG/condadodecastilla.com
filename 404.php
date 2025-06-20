<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Página no encontrada</title>
    <?php include __DIR__ . '/includes/head_common.php'; ?>
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/_header.php'; ?>
<main class="container page-content-block error-page">
    <h1>Página no encontrada</h1>
    <p>Lo sentimos, la página que buscas no existe.</p>
    <p><a href="/index.php" class="cta-button">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/_footer.php'; ?>
<script src="/assets/js/layout.js"></script>
</body>
</html>

