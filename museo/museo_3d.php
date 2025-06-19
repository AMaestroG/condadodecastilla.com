<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Museo 3D Interactivo - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/PointerLockControls.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/EffectComposer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/RenderPass.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/UnrealBloomPass.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/shaders/LuminosityHighPassShader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/shaders/CopyShader.js"></script>
    <!-- Google Fonts, FontAwesome, and epic_theme.css are now in head_common.php -->
</head>
<body class="alabaster-bg">
    <div id="crosshair" class="crosshair"></div>
    <div id="linterna-condado"></div>
    <?php require_once __DIR__ . '/../_header.php'; ?>

    <header class="page-header hero hero-museo">
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Explora el Museo en 3D</h1>
            <p>Recorre un entorno tridimensional con nuestras piezas destacadas.</p>
        </div>
    </header>

    <section id="museo-3d-section" class="section museo-3d-section alternate-bg">
        <div class="container page-content-block">
            <h2 class="section-title">Museo 3D <i class="fas fa-vr-cardboard"></i></h2>
            <p class="intro-paragraph">Usa las teclas WASD y el ratón para moverte por el museo virtual.</p>
            <div id="museo-3d-container"></div>
        </div>
    </section>

    <?php require_once __DIR__ . '/../_footer.php'; ?>
    <script src="/js/config.js"></script>
    <script src="/js/layout.js"></script>
    <script src="/js/museum-3d/utils.js"></script>
    <script src="/js/museum-3d/config.js"></script>
    <script src="/js/museum-3d/sceneManager.js"></script>
    <script src="/js/museum-3d/museumLayout.js"></script>
    <script src="/js/museum-3d/playerControls.js"></script>
    <script src="/js/museum-3d/exhibitManager.js"></script>
    <script src="/js/museo-3d-main.js"></script>

    <div id="pointer-lock-instructions" class="pointer-lock-instructions">
        <div>
            <h1>Exploración del Museo</h1>
            <p class="intro-big">Haz clic en esta pantalla para empezar a explorar.</p>
            <p>Usa las teclas <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> para moverte y el <kbd>RATÓN</kbd> para mirar alrededor.</p>
            <p class="ai-note">Presiona <kbd>ESC</kbd> para liberar el cursor.</p>
        </div>
    </div>

    <div id="pieza-info-overlay">
        <div id="overlay-content">
            <h3 id="overlay-titulo"></h3>
            <p id="overlay-autor"></p>
            <p id="overlay-descripcion"></p>
            <button id="overlay-cerrar" aria-label="Cerrar información de la pieza">&times; Cerrar</button>
        </div>
    </div>
</body>
</html>
