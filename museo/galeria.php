<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería del Museo - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">
    <!-- Google Fonts, FontAwesome, and epic_theme.css are now in head_common.php -->
</head>
<body>
    <div id="linterna-condado"></div>
    <?php require_once __DIR__ . '/../_header.html'; ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--condado-primario-rgb), 0.75), rgba(var(--condado-texto-rgb), 0.88)), url('/imagenes/hero_museo_background.jpg');">
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Galería del Museo</h1>
            <p>Explora las piezas compartidas por nuestra comunidad.</p>
        </div>
    </header>

    <main>
        <section class="section museum-gallery-section alternate-bg" id="gallery-2d-section">
            <div class="container">
                <h2 class="section-title">Galería del Museo <i class="fas fa-landmark"></i></h2>
                <div id="museumGalleryGrid" class="card-grid museum-gallery-grid">
                    <p class="no-pieces-message" id="noPiecesMessage">Cargando piezas del museo...</p>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../_footer.php'; ?>
    <script src="/js/config.js"></script>
    <script src="/js/layout.js"></script>
    <script src="/js/museo-2d-gallery.js"></script>
</body>
</html>
