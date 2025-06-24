<footer class="site-footer bg-gray-800 text-gray-300 py-8 text-center">
    <div class="container-epic mx-auto px-4">
        <p class="mb-2">&copy; <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
        <p class="mb-2 text-sm">Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
        <div class="mb-3">
            <?php
            // Asegurarse que auth.php se incluye si no está ya globalmente disponible
            // require_once dirname(__DIR__) . '/includes/auth.php'; // Ya debería estar cargado por el header o index
            if (is_admin_logged_in()) {
                echo '<a href="/dashboard/logout.php" class="text-sm hover:text-old-gold underline">Cerrar sesión de administrador</a>';
            } else {
                echo '<a href="/dashboard/login.php" class="text-sm hover:text-old-gold underline">Acceso Administrador</a>';
            }
            ?>
        </div>
        <p class="text-xs text-gray-500 ai-notice">Este sitio incluye contenido generado con ayuda de IA. <a href="/docs/responsible-ai.md" class="underline hover:text-old-gold">Más información</a>.</p>
    </div>
</footer>

<!-- Panel de Ayuda Flotante -->
<div id="help-toggle" class="fixed bottom-4 right-4 bg-old-gold text-imperial-purple w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold cursor-pointer shadow-lg hover:bg-yellow-600 transition-colors z-40" aria-expanded="false" aria-controls="help-panel" title="Ayuda">?</div>
<div id="help-panel" class="fixed bottom-20 right-4 bg-white dark:bg-gray-700 p-6 rounded-lg shadow-xl w-80 border border-gray-300 dark:border-gray-600 hidden z-40" aria-hidden="true">
    <button id="help-close" class="absolute top-2 right-2 text-xl text-gray-600 dark:text-gray-300 hover:text-red-500" aria-label="Cerrar ayuda">&times;</button>
    <h2 class="text-lg font-headings text-gray-800 dark:text-white mb-3">Consejos Rápidos</h2>
    <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-200 space-y-1">
        <li>Explora las secciones desde el menú principal.</li>
        <li>Visita el foro para compartir con la comunidad.</li>
        <li>Consulta la agenda de eventos culturales.</li>
    </ul>
</div>

<!-- Scripts Esenciales y Funcionales -->
<!-- Nota: Se han eliminado scripts que parecían ligados al diseño anterior. Revisar y añadir los necesarios. -->
<script defer src="/assets/js/main.js"></script> <!-- Para tema, paleta, etc. Necesita ser adaptado a los nuevos botones del sidebar. -->
<script src="/assets/js/parallax.js"></script> <!-- Si se mantiene el parallax en index.php -->
<script src="/js/lang-bar.js"></script> <!-- Para la funcionalidad de cambio de idioma -->
<script src="/assets/js/audio-controller.js"></script> <!-- Si se mantiene la funcionalidad de audio -->
<script src="/assets/js/homonexus-toggle.js"></script> <!-- Para el toggle de Homonexus. Necesita ser adaptado. -->
<script src="/assets/js/help.js"></script> <!-- Para el panel de ayuda -->
<script defer src="/assets/js/custom-pointer.js"></script> <!-- Si se mantiene el cursor personalizado -->
<script src="/assets/js/scroll-fade.js"></script> <!-- Efecto visual, mantener si se desea -->
<!-- <script src="/js/layout.js"></script> --> <!-- Revisar si es necesario, podría ser del layout antiguo -->
<!-- <script src="/assets/js/hero.js"></script> --> <!-- Revisar, podría haber lógica útil o ser obsoleto para el hero antiguo -->

<!-- Nuevos archivos JS modularizados -->
<script defer src="/assets/js/ui-drawers.js"></script>
<script defer src="/assets/js/sidebar-menu.js"></script>
<script defer src="/assets/js/video-modal.js"></script>


<script>
// Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/service-worker.js').catch(function (e) {
            console.error('SW registration failed', e);
        });
    });
}

// Lista de scripts que podrían ser obsoletos o necesitar revisión más profunda:
// type="module" src="/assets/js/sliding-menu.js" -> Probablemente obsoleto por nuevo sistema de sidebar/drawers
// defer src="/assets/js/slider_menu.js" -> Probablemente obsoleto
// src="/assets/js/cave_mask.js" -> Relacionado con efectos visuales del header antiguo
</script>
