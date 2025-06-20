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
    <h1 class="font-headings">Página no encontrada</h1>
    <p class="font-body">Lo sentimos, la página que buscas no existe.</p>
    <p class="font-body"><a href="/index.php" class="cta-button">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/fragments/footer.php'; ?>
<script src="/js/layout.js"></script>
</body>
</html>

