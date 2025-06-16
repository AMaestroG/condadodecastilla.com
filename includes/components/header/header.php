<header id="fixed-header" role="banner">
    <button id="menu-button" aria-label="Abrir menú" data-menu-target="slide-menu-left">☰</button>
    <a href="/index.php" class="site-title">Condado de Castilla</a>
    <button id="tools-button" aria-label="Herramientas" data-menu-target="slide-menu-right">⚙</button>
</header>
<nav id="slide-menu-left" class="slide-menu left" role="navigation">
    <?php require dirname(__DIR__, 2) . '/header.php'; ?>
</nav>
<nav id="slide-menu-right" class="slide-menu right" aria-label="Herramientas">
    <div style="padding:1rem;">
        <button id="theme-toggle" title="Cambiar tema" style="margin-bottom:1rem;">Tema</button>
        <button id="ai-drawer-toggle" title="Asistente IA">IA</button>
    </div>
</nav>
<script defer src="/js/menu-controller.js"></script>
