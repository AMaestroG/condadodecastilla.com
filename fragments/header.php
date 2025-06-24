<?php
// fragments/header.php
// UNIFIED PANEL RIGHT STRUCTURE
require_once __DIR__ . '/../includes/auth.php'; // For is_admin_logged_in()
?>
<header class="site-header bg-imperial-purple text-old-gold shadow-md sticky top-0 z-50">
    <div class="container-epic mx-auto flex items-center justify-between p-4">
        <div class="flex items-center">
            <a href="/" class="logo-link flex items-center text-xl font-bold">
                <img src="/assets/img/escudo.jpg" alt="Logo Condado de Castilla" class="h-10 w-10 mr-2 rounded-full border border-old-gold">
                <span class="hidden sm:inline site-title">Condado de Castilla</span>
            </a>
        </div>

        <div class="flex items-center space-x-3">
            <!-- El botón de Chat IA separado se elimina -->
            <button id="open-unified-panel-button" aria-label="Abrir Menú y Herramientas" aria-expanded="false" aria-controls="unified-panel" class="text-old-gold hover:text-white transition-colors">
                <i class="fas fa-bars text-2xl"></i> {/* O un icono más tipo "puntos verticales" o "engranaje" si se prefiere */}
            </button>
        </div>
    </div>
</header>

<!-- Unified Sliding Panel (Right) -->
<aside id="unified-panel" class="fixed top-0 right-0 w-80 md:w-96 h-full bg-gray-900 text-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-[70]" role="dialog" aria-modal="true" aria-labelledby="unified-panel-title" tabindex="-1" aria-hidden="true">
    <div class="flex flex-col h-full">
        <!-- Panel Header -->
        <div class="flex justify-between items-center p-4 border-b border-gray-700 flex-shrink-0">
            <h2 id="unified-panel-title" class="text-xl font-headings text-old-gold">Menú y Herramientas</h2>
            <button id="close-unified-panel-button" aria-label="Cerrar Panel" class="text-2xl text-gray-300 hover:text-old-gold">&times;</button>
        </div>

        <!-- Panel Content (Scrollable) -->
        <div class="flex-grow p-4 overflow-y-auto">

            <!-- Navegación Principal -->
            <div class="menu-section mb-6">
                <h3 class="text-sm font-semibold uppercase text-old-gold mb-2 border-b border-gray-700 pb-1">Navegación</h3>
                <nav id="unified-main-nav"> {/* Nuevo ID para el contenedor de nav si sidebar-nav ya no aplica */}
                    <?php
                    if (file_exists(__DIR__ . '/menus/main-menu.php')) {
                        include __DIR__ . '/menus/main-menu.php'; // Esto llama a render_main_menu() que crea ul#main-menu
                    }
                    ?>
                </nav>
            </div>

            <!-- Herramientas del Sitio -->
            <div class="menu-section site-tools-section mb-6">
                <h3 class="text-sm font-semibold uppercase text-old-gold mb-2 border-b border-gray-700 pb-1">Herramientas del Sitio</h3>
                <ul class="space-y-2 text-sm">
                    <li><button id="unified-theme-toggle" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-moon mr-2 w-5 text-center"></i> <span class="flex-1">Tema (Claro/Oscuro)</span></button></li>
                    <li><button id="unified-mute-toggle" aria-pressed="false" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-volume-up mr-2 w-5 text-center"></i> <span class="flex-1">Sonido</span></button></li>
                    <li><button id="unified-homonexus-toggle" aria-pressed="false" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-users mr-2 w-5 text-center"></i> <span class="flex-1">Homonexus</span></button></li>
                    <li class="pt-1">
                        <span class="block text-xs font-medium text-gray-500 mb-1 px-2">Idioma:</span>
                        <?php
                        if (file_exists(__DIR__ . '/header/language-flags.html')) {
                            echo '<div class="flex space-x-3 justify-around px-2 py-1 bg-gray-700 rounded">';
                            include __DIR__ . '/header/language-flags.html';
                            echo '</div>';
                        } else {
                            echo '<p class="text-xs text-gray-500 px-2">Selector no disponible.</p>';
                        }
                        ?>
                    </li>
                </ul>
            </div>

            <!-- Chat IA -->
            <div class="menu-section ai-chat-section mb-6">
                <h3 class="text-sm font-semibold uppercase text-old-gold mb-2 border-b border-gray-700 pb-1">Asistente IA</h3>
                <div class="p-2 rounded bg-gray-800"> {/* Contenedor para el chat */}
                <?php
                if (file_exists(__DIR__ . '/header/ai-drawer.html')) {
                    // El contenido de ai-drawer.html puede necesitar ajustes de estilo para este nuevo contexto
                    // Específicamente, el header dentro de ai-drawer.html y el botón de cierre podrían ser redundantes.
                    // Por ahora, se incluye tal cual.
                    echo file_get_contents(__DIR__ . '/header/ai-drawer.html');
                } else {
                    echo '<p class="text-xs text-gray-500">Interfaz de Chat IA no encontrada.</p>';
                }
                ?>
                </div>
            </div>

            <!-- Comunidad -->
            <div class="menu-section community-links-section mb-6">
                <h3 class="text-sm font-semibold uppercase text-old-gold mb-2 border-b border-gray-700 pb-1">Comunidad</h3>
                <ul class="space-y-1 text-sm">
                    <li>
                        <?php
                        if (file_exists(__DIR__ . '/menus/social-menu.html')) {
                            echo '<div class="flex space-x-3 justify-around px-2 py-1 bg-gray-700 rounded">';
                            include __DIR__ . '/menus/social-menu.html';
                            echo '</div>';
                        } else {
                            echo '<p class="text-xs text-gray-500 px-2">Enlaces sociales no disponibles.</p>';
                        }
                        ?>
                    </li>
                    <li><a href="/foro/index.php" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-comments mr-2 w-5 text-center"></i> <span class="flex-1">Foro</span></a></li>
                    <li><a href="/contacto/contacto.php" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-envelope mr-2 w-5 text-center"></i> <span class="flex-1">Contacto</span></a></li>
                </ul>
            </div>

            <!-- Admin Menu (Condicional) -->
            <?php if (is_admin_logged_in()): ?>
            <div class="menu-section admin-section mb-6">
                <h3 class="text-sm font-semibold uppercase text-old-gold mb-2 border-b border-gray-700 pb-1">Admin</h3>
                <?php
                if (file_exists(__DIR__ . '/menus/admin-menu.php')) {
                    include __DIR__ . '/menus/admin-menu.php';
                }
                ?>
            </div>
            <?php endif; ?>

        </div> <!-- Fin de Panel Content Scrollable -->
    </div>
</aside>

<!-- Overlay for modals/drawers (se mantiene igual) -->
<div id="site-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[55]" aria-hidden="true"></div>

<?php
// Los scripts JS modularizados (ui-drawers.js, sidebar-menu.js) se cargarán desde el footer.
// Necesitarán ser actualizados para apuntar a los nuevos IDs:
// - #open-unified-panel-button
// - #close-unified-panel-button
// - #unified-panel
// - #unified-main-nav (o #main-menu dentro de él) para sidebar-menu.js
// - #unified-theme-toggle, #unified-mute-toggle, etc. para main.js
?>
