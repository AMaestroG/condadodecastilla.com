<?php
require_once __DIR__ . '/includes/session.php';
ensure_session_started();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Misión - Cerezo de Río Tirón</title>
    <?php require_once __DIR__ . '/includes/head_common.php'; ?>
    <?php require_once __DIR__ . '/includes/load_page_css.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/_header.php'; ?>
<main class="container page-content-block">
    <h1 class="gradient-text">Nuestra Misión</h1>
    <p class="mission-text">Promocionar el turismo en <strong>Cerezo de Río Tirón</strong> y gestionar de forma activa su patrimonio arqueológico y cultural.</p>
</main>
<?php require_once __DIR__ . '/_footer.php'; ?>
<script src="/js/layout.js"></script>
</body>
</html>
