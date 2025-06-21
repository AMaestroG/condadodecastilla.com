<!DOCTYPE html>
<html lang="es">
<head>
<?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Mapa Interactivo del Alfoz - Condado de Castilla</title>
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>

    <main>
        <section class="section">
            <div class="container-epic">
                <h1 class="gradient-text">Mapa Interactivo</h1>
                <div id="interactive-map" style="height: 600px;"></div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>
    <script src="/js/lugares-data.js"></script>
</body>
</html>
