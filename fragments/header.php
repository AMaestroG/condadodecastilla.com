<?php
// fragments/header.php
// Simplified Header Structure
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
            <button id="open-ai-chat" aria-label="Abrir Chat IA" class="text-old-gold hover:text-white transition-colors">
                <i class="fas fa-comments text-xl"></i>
            </button>
            <button id="open-main-sidebar" aria-label="Abrir Menú" aria-expanded="false" aria-controls="main-sidebar" class="text-old-gold hover:text-white transition-colors">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
</header>

<!-- Main Sidebar (Left) -->
<aside id="main-sidebar" class="fixed top-0 left-0 w-72 h-full bg-gray-800 text-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-[60]" role="navigation" aria-hidden="true" tabindex="-1">
    <div class="p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-headings">Menú</h2>
            <button id="close-main-sidebar" aria-label="Cerrar Menú" class="text-2xl hover:text-gray-300">&times;</button>
        </div>

        <nav id="sidebar-nav">
            <?php
            // Main Navigation
            if (file_exists(__DIR__ . '/menus/main-menu.php')) {
                echo '<div class="menu-section mb-6">';
                echo '<h4 class="text-sm font-semibold uppercase text-gray-400 mb-2">Navegación</h4>';
                include __DIR__ . '/menus/main-menu.php'; // Assumes this file outputs <ul>...</ul>
                echo '</div>';
            }

            // Admin Menu
            if (is_admin_logged_in()) {
                if (file_exists(__DIR__ . '/menus/admin-menu.php')) {
                    echo '<div class="menu-section mb-6">';
                    echo '<h4 class="text-sm font-semibold uppercase text-gray-400 mb-2">Admin</h4>';
                    include __DIR__ . '/menus/admin-menu.php'; // Assumes this file outputs <ul>...</ul>
                    echo '</div>';
                }
            }
            ?>
        </nav>

        <!-- Sección Personalizar Experiencia -->
        <div class="menu-section settings-section mb-6">
            <h4 class="text-sm font-semibold uppercase text-gray-400 mb-3">Personalizar Experiencia</h4>
            <ul class="space-y-2">
                <li><button id="sidebar-theme-toggle" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-moon mr-2 w-5 text-center"></i> <span class="flex-1">Tema (Claro/Oscuro)</span></button></li>
                <li><button id="sidebar-mute-toggle" aria-pressed="false" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-volume-up mr-2 w-5 text-center"></i> <span class="flex-1">Sonido (Silenciar/Activar)</span></button></li>
                <li class="pt-1">
                    <span class="block text-xs font-medium text-gray-500 mb-1 px-2">Idioma:</span>
                    <?php
                    if (file_exists(__DIR__ . '/header/language-flags.html')) {
                        echo '<div class="flex space-x-3 justify-around px-2 py-1 bg-gray-700 rounded">';
                        include __DIR__ . '/header/language-flags.html';
                        echo '</div>';
                    } else {
                        echo '<p class="text-sm text-gray-500 px-2">Selector no disponible.</p>';
                    }
                    ?>
                </li>
            </ul>
        </div>

        <!-- Sección Comunidad -->
        <div class="menu-section community-section mb-6">
            <h4 class="text-sm font-semibold uppercase text-gray-400 mb-3">Comunidad</h4>
            <ul class="space-y-1"> {/* Reduced space-y for tighter social icons list if preferred */}
                <li>
                    <?php
                    if (file_exists(__DIR__ . '/menus/social-menu.html')) {
                        echo '<div class="flex space-x-3 justify-around px-2 py-1 bg-gray-700 rounded">';
                        include __DIR__ . '/menus/social-menu.html';
                        echo '</div>';
                    } else {
                        echo '<p class="text-sm text-gray-500 px-2">Enlaces sociales no disponibles.</p>';
                    }
                    ?>
                </li>
                <!-- Considerar añadir enlace al foro aquí -->
                <!-- <li><a href="/foro/index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i class="fas fa-comments mr-2 w-5 text-center"></i> <span class="flex-1">Foro</span></a></li> -->
            </ul>
        </div>

        <!-- Sección Herramientas Especiales -->
        <div class="menu-section special-tools-section mb-6">
            <h4 class="text-sm font-semibold uppercase text-gray-400 mb-3">Herramientas Especiales</h4>
            <ul class="space-y-2">
                <li><button id="sidebar-homonexus-toggle" aria-pressed="false" class="w-full text-left flex items-center p-2 rounded hover:bg-gray-700"><i class="fas fa-users mr-2 w-5 text-center"></i> <span class="flex-1">Homonexus</span></button></li>
            </ul>
        </div>

    </div>
</aside>

<!-- AI Chat Drawer (Right) -->
<aside id="ai-chat-drawer" class="fixed top-0 right-0 w-96 h-full bg-white dark:bg-gray-900 shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-[70]" role="dialog" aria-modal="true" aria-labelledby="ai-chat-title" tabindex="-1" aria-hidden="true">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="ai-chat-title" class="text-xl font-headings text-gray-800 dark:text-white">Asistente IA</h3>
            <button id="close-ai-chat" aria-label="Cerrar Chat IA" class="text-2xl text-gray-600 dark:text-gray-300 hover:text-red-500">&times;</button>
        </div>
        <div class="flex-grow p-4 overflow-y-auto">
            <?php
            // Content from ai-drawer.html (or similar structure)
            if (file_exists(__DIR__ . '/header/ai-drawer.html')) {
                // This content needs to be styled appropriately for the drawer
                // It might be better to rebuild this part for better integration
                echo file_get_contents(__DIR__ . '/header/ai-drawer.html');
            } else {
                echo '<p class="text-gray-700 dark:text-gray-300">Interfaz de Chat IA no encontrada.</p>';
            }
            ?>
        </div>
    </div>
</aside>

<!-- Overlay for modals/drawers -->
<div id="site-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[55]" aria-hidden="true"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mainSidebar = document.getElementById('main-sidebar');
    const openMainSidebarButton = document.getElementById('open-main-sidebar');
    const closeMainSidebarButton = document.getElementById('close-main-sidebar');

    const aiChatDrawer = document.getElementById('ai-chat-drawer');
    const openAiChatButton = document.getElementById('open-ai-chat');
    const closeAiChatButton = document.getElementById('close-ai-chat');

    const siteOverlay = document.getElementById('site-overlay');

    function openDrawer(drawer) {
        if (drawer) {
            drawer.classList.remove('translate-x-full', '-translate-x-full');
            drawer.classList.add('translate-x-0');
            drawer.setAttribute('aria-hidden', 'false');
            siteOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }
    }

    function closeDrawer(drawer) {
        if (drawer) {
            if(drawer.id === 'main-sidebar') {
                drawer.classList.add('-translate-x-full');
            } else {
                drawer.classList.add('translate-x-full');
            }
            drawer.classList.remove('translate-x-0');
            drawer.setAttribute('aria-hidden', 'true');

            // Hide overlay only if no other drawer is open
            const otherDrawerOpen = Array.from(document.querySelectorAll('[role="dialog"], [role="navigation"]'))
                                      .some(d => d !== drawer && d.getAttribute('aria-hidden') === 'false' && (d.id === 'main-sidebar' || d.id === 'ai-chat-drawer'));
            if (!otherDrawerOpen) {
                siteOverlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scroll
            }
        }
    }

    openMainSidebarButton?.addEventListener('click', () => openDrawer(mainSidebar));
    closeMainSidebarButton?.addEventListener('click', () => closeDrawer(mainSidebar));

    openAiChatButton?.addEventListener('click', () => openDrawer(aiChatDrawer));
    closeAiChatButton?.addEventListener('click', () => closeDrawer(aiChatDrawer));

    siteOverlay?.addEventListener('click', () => {
        closeDrawer(mainSidebar);
        closeDrawer(aiChatDrawer);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (mainSidebar && mainSidebar.getAttribute('aria-hidden') === 'false') {
                closeDrawer(mainSidebar);
            }
            if (aiChatDrawer && aiChatDrawer.getAttribute('aria-hidden') === 'false') {
                closeDrawer(aiChatDrawer);
            }
        }
    });

    // Placeholder for theme, palette, mute, homonexus toggles inside sidebar
    // These buttons will need their respective JS logic from assets/js/main.js or similar
    // to be connected or refactored.
    document.getElementById('sidebar-theme-toggle')?.addEventListener('click', () => {
        // Logic for theme toggle, potentially calling functions from main.js or dedicated module
        console.log('Sidebar Theme Toggle Clicked');
        document.getElementById('theme-toggle')?.click(); // Example: trigger original if still exists
    });
    document.getElementById('sidebar-palette-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Palette Toggle Clicked');
        document.getElementById('palette-toggle')?.click();
    });
    document.getElementById('sidebar-mute-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Mute Toggle Clicked');
        document.getElementById('mute-toggle')?.click();
    });
     document.getElementById('sidebar-homonexus-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Homonexus Toggle Clicked');
        document.getElementById('homonexus-toggle')?.click();
    });

});
</script>
<?php
// Remove or comment out slider_menu.php inclusion if it's part of the old complex menu system
// if (file_exists(__DIR__ . "/slider_menu.php")) { include __DIR__ . "/slider_menu.php"; }

// Remove cave-mask and header-escudo-overlay if not used in the new design
// <div id="cave-mask"></div>
// <img id="header-escudo-overlay" class="hero-escudo" src="/assets/img/escudo.jpg" alt="Escudo de Cerezo de Río Tirón">
?>
