<?php require_once __DIR__ . '/includes/head_common.php'; ?>
<body>
    <?php require_once __DIR__ . '/_header.html'; ?>
    <section class="demo-hero">
        <h1>Efectos Visuales</h1>
        <div class="demo-card">
            <p>Esta tarjeta emplea un sutil efecto de vidrio esmerilado con movimiento de aparición.</p>
            <button class="demo-button">Acción</button>
        </div>
    </section>
    <?php require_once __DIR__ . '/_footer.php'; ?>
    <?php include __DIR__ . '/fragments/ai-drawer.html'; ?>
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
