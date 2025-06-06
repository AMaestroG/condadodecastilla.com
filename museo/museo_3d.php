<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div id="crosshair" style="position: fixed; top: 50%; left: 50%; width: 6px; height: 6px; background-color: rgba(255,255,255,0.6); border-radius: 50%; transform: translate(-50%, -50%); z-index: 9998; pointer-events: none; display: none;"></div>
    <div id="linterna-condado"></div>
    <?php require_once __DIR__ . '/../_header.html'; ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--condado-primario-rgb), 0.75), rgba(var(--condado-texto-rgb), 0.88)), url('/imagenes/hero_museo_background.jpg');">
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

    <?php require_once __DIR__ . '/../_footer.html'; ?>
    <script src="/js/config.js"></script>
    <script src="/js/layout.js"></script>
    <script src="/js/museum-3d/utils.js"></script>
    <script src="/js/museum-3d/config.js"></script>
    <script src="/js/museum-3d/sceneManager.js"></script>
    <script src="/js/museum-3d/museumLayout.js"></script>
    <script src="/js/museum-3d/playerControls.js"></script>
    <script src="/js/museum-3d/exhibitManager.js"></script>
    <script src="/js/museo-3d-main.js"></script>

    <div id="pointer-lock-instructions" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); color: white; display: flex; justify-content: center; align-items: center; text-align: center; font-size: 20px; z-index: 10000; cursor: pointer;">
        <div>
            <h1>Exploración del Museo</h1>
            <p style="font-size: 1.2em; margin-top: 1em;">Haz clic en esta pantalla para empezar a explorar.</p>
            <p style="margin-top: 0.5em;">Usa las teclas <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> para moverte y el <kbd>RATÓN</kbd> para mirar alrededor.</p>
            <p style="font-size: 0.8em; margin-top: 2em;">Presiona <kbd>ESC</kbd> para liberar el cursor.</p>
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
