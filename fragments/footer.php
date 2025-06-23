<footer class="footer bg-imperial-purple text-old-gold texture-alabaster" style="height: var(--header-footer-height);">
    <div class="container-epic">
        <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
        <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
        <?php
        require_once dirname(__DIR__) . '/includes/auth.php';
        if (is_admin_logged_in()) {
            echo '<p><a href="/dashboard/logout.php">Cerrar sesión</a></p>';
        } else {
            echo '<p><a href="/dashboard/login.php">Acceso Administrador</a></p>';
        }
        ?>
        <?php
        $social_fragment = __DIR__ . '/menus/social-menu.html';
        if (file_exists($social_fragment)) {
            echo file_get_contents($social_fragment);
        }
        ?>
        <p class="ai-notice">Este sitio incluye contenido generado con ayuda de IA. <a href="/docs/responsible-ai.md">Más información</a>.</p>
</div>
</footer>
<div id="help-toggle" aria-expanded="false" aria-controls="help-panel" title="Ayuda">?</div>
<div id="help-panel" class="hidden" aria-hidden="true">
    <button id="help-close" aria-label="Cerrar ayuda">&times;</button>
    <h2>Consejos rápidos</h2>
    <ul>
        <li>Explora las secciones desde el menú principal.</li>
        <li>Visita el foro para compartir con la comunidad.</li>
        <li>Consulta la agenda de eventos culturales.</li>
    </ul>
</div>
<script type="module" src="/assets/js/sliding-menu.js"></script>
<script defer src="/assets/js/main.js"></script>
<script src="/assets/js/cave_mask.js"></script>
<script src="/assets/js/hero.js"></script>
<script src="/assets/js/scroll-fade.js"></script>
<script src="/assets/js/parallax.js"></script>
<script src="/js/lang-bar.js"></script>
<script src="/assets/js/audio-controller.js"></script>
<script src="/assets/js/escudo-reveal.js"></script>
<script src="/assets/js/escudo-drag.js"></script>
<script defer src="/assets/js/custom-pointer.js"></script>
<script src="/assets/js/homonexus-toggle.js"></script>
<script src="/js/layout.js"></script>
<script src="/assets/js/help.js"></script>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/service-worker.js').catch(function (e) {
            console.error('SW registration failed', e);
        });
    });
}
</script>
