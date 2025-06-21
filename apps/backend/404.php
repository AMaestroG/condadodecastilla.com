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
    <h1 class="text-4xl font-headings">Página no encontrada</h1>
    <p class="text-lg font-body">Lo sentimos, la página que buscas no existe.</p>
    <p class="text-lg font-body"><a href="/index.php" class="cta-button">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/fragments/footer.php'; ?>

</body>
</html>

