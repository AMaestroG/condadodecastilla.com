<?php require_once __DIR__ . '/includes/head_common.php'; ?>
<body>
    <?php require_once __DIR__ . '/_header.php'; ?>
    <section class="demo-hero">
        <h1>Efectos Visuales</h1>
        <div class="demo-card">
            <p>Esta tarjeta emplea un sutil efecto de vidrio esmerilado con movimiento de aparición.</p>
            <button class="demo-button" id="demo-info-trigger" data-menu-target="demo-info-panel" aria-controls="demo-info-panel" aria-expanded="false"><span>Información</span></button>
        </div>
    </section>

    <div id="demo-info-panel" class="menu-panel right-panel" role="dialog">
        <button id="close-demo-info" aria-label="Cerrar">×</button>
        <div class="menu-section">
            <h4 class="gradient-text">Detalles del efecto</h4>
            <p>Este panel demuestra la integración de transparencias y movimiento con colores morados y oro viejo.</p>
        </div>
    </div>
    <?php require_once __DIR__ . '/_footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <script src="/js/layout.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const demoBtn = document.querySelector('.demo-button');
        if (demoBtn) {
            demoBtn.addEventListener('click', function() {
                alert('Demostración de botón exitosa.');
            });
        }
    });
    </script>
</body>
</html>
