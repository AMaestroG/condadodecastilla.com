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
<?php require_once __DIR__ . '/fragments/header.php'; ?>
<main class="container page-content-block error-page">
    <h1 class="font-headings text-4xl">Página no encontrada</h1>
    <p class="font-body text-lg">Lo sentimos, la página que buscas no existe.</p>
    <p class="font-body text-lg"><a href="/index.php" class="cta-button">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/fragments/footer.php'; ?>
<script src="/js/layout.js"></script>
</body>
</html>

