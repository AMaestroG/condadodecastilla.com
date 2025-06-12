<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda</title>
    <?php require_once __DIR__ . '/../includes/load_page_css.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../_header.html'; ?>
<main class="container page-content-block">
    <h1 class="section-title">Tienda</h1>
    <div class="card-grid">
        <div class="card">
            <img src="/assets/img/AlfozCerasioLantaron.jpg" alt="Producto 1">
            <div class="card-content">
                <h3>Producto 1</h3>
                <p>Descripción breve del producto 1.</p>
                <p><strong>Precio: 10€</strong></p>
            </div>
        </div>
        <div class="card">
            <img src="/assets/img/escudo.jpg" alt="Producto 2">
            <div class="card-content">
                <h3>Producto 2</h3>
                <p>Descripción breve del producto 2.</p>
                <p><strong>Precio: 15€</strong></p>
            </div>
        </div>
        <div class="card">
            <img src="/assets/img/Yanna.jpg" alt="Producto 3">
            <div class="card-content">
                <h3>Producto 3</h3>
                <p>Descripción breve del producto 3.</p>
                <p><strong>Precio: 20€</strong></p>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../_footer.php'; ?>
</body>
</html>
