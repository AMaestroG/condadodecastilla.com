<?php require_once __DIR__ . '/includes/head_common.php'; ?>
<body>
    <?php require_once __DIR__ . '/_header.php'; ?>
    <section class="demo-hero">
        <h1>Efectos Visuales</h1>
        <div class="demo-card">
            <p>Esta tarjeta emplea un sutil efecto de vidrio esmerilado con movimiento de aparición.</p>
            <button class="demo-button" data-menu-target="demo-info-panel"><span>Acción</span></button>
        </div>
    </section>
    <div id="demo-info-panel" class="menu-panel right-panel">
        <p>Demo interactiva del patrimonio de Cerezo de Río Tirón.</p>
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
