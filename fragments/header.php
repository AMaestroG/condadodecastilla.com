<header class="bg-imperial-purple text-old-gold texture-alabaster">
<div id="cave-mask"></div>
<img id="header-escudo-overlay" class="hero-escudo" src="/assets/img/escudo.jpg" alt="Escudo de Cerezo de Río Tirón">
<div id="fixed-header-elements" style="height: var(--header-footer-height);">
    <div class="header-action-buttons">
        <img loading="lazy" src="/assets/icons/columna.svg" class="header-icon" alt="Roman column icon" />
        <button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-haspopup="true" aria-expanded="false" role="button" aria-controls="consolidated-menu-items">☰</button>
        <button id="flag-toggle" data-menu-target="language-panel" aria-label="Seleccionar idioma" aria-haspopup="true" aria-expanded="false" role="button" aria-controls="language-panel"><i class="fas fa-flag"></i></button>
        <button id="mute-toggle" aria-pressed="false" aria-label="Silenciar">🔊</button>
        <button id="homonexus-toggle" aria-label="Activar Homonexus" aria-pressed="false">👥</button>
        <a href="/nuevaweb/index.php" class="cta-button cta-button-small">Nueva Web</a>
    </div>
</div>
<!-- Left Sliding Panel for Main Menu -->
<div id="consolidated-menu-items" class="menu-panel left-panel" role="navigation" aria-labelledby="consolidated-menu-button" tabindex="-1" aria-hidden="true">
    <button id="ai-chat-trigger" class="menu-item-button" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA" aria-haspopup="dialog"><i class="fas fa-comments"></i> <span>Chat IA</span></button>
    <button id="theme-toggle" class="menu-item-button" aria-label="Cambiar tema"><i class="fas fa-moon"></i> <span>Modo</span></button>
    <button id="moon-toggle" class="menu-item-button">🌙 Modo luna</button>
    <button id="palette-toggle" class="menu-item-button" aria-label="Cambiar paleta"><i class="fas fa-palette"></i> <span>Paleta</span></button>

    <div class="menu-section">
        <h4 class="gradient-text">Navegación Principal</h4>
        <?php
        if (file_exists(__DIR__ . '/menus/main-menu.php')) {
            include __DIR__ . '/menus/main-menu.php';
        }
        ?>
    </div>
    <div class="menu-section">
        <h4 class="gradient-text">Admin</h4>
        <?php
        echo '<div id="admin-menu-source-content">';
        include __DIR__ . '/menus/admin-menu.php';
        echo '</div>';
        ?>
    </div>
    <div class="menu-section">
        <h4 class="gradient-text">Social</h4>
        <?php
        echo '<div id="social-menu-source-content">';
        if (file_exists(__DIR__ . '/menus/social-menu.html')) {
            echo file_get_contents(__DIR__ . '/menus/social-menu.html');
        }
        echo '</div>';
        ?>
    </div>
    <!-- Add other menu items or buttons here as needed -->
</div>

<!-- Right Sliding Panel for AI Chat -->
<div id="ai-chat-panel" class="menu-panel right-panel" role="dialog" aria-modal="true" aria-labelledby="ai-chat-title" tabindex="-1" aria-hidden="true">
    <?php
    // Content from ai-drawer.html will go here
    // It includes the header, response area, input, and submit button for AI chat
    if (file_exists(__DIR__ . '/header/ai-drawer.html')) {
        // We might need to wrap or modify ai-drawer.html if its root element isn't suitable
        // For now, directly include it.
        echo file_get_contents(__DIR__ . '/header/ai-drawer.html');
    } else {
        echo '<p>Error: AI Chat interface not found.</p>';
    }
    ?>
</div>

<!-- Right Sliding Panel for Language Flags -->
<?php
    if (file_exists(__DIR__ . '/header/language-flags.html')) {
        echo file_get_contents(__DIR__ . '/header/language-flags.html');
    } else {
        echo '<div id="language-panel" class="menu-panel right-panel" role="dialog" aria-labelledby="flag-toggle" tabindex="-1" aria-hidden="true"><p>Flags not found.</p></div>';
    }
?>
</header>

<!-- Sidebar HTML Structure -->
<div id="sidebar" aria-hidden="true" tabindex="-1">
    <div class="sidebar-header" style="padding: 15px; text-align: center; border-bottom: 1px solid rgba(var(--epic-gold-main-rgb), 0.3);">
        <a href="/" class="logo-link">
            <img src="/assets/img/escudo.jpg" alt="Logo Condado de Castilla" style="max-width: 100px; height: auto; border-radius: var(--global-border-radius); border: 1px solid var(--epic-gold-secondary); background-color: rgba(var(--epic-alabaster-bg-rgb), 0.8);">
        </a>
        <button id="close-sidebar-button" aria-label="Cerrar menú" style="position: absolute; top: 15px; right: 15px; font-size: 2em; background: none; border: none; color: var(--epic-text-color); cursor: pointer; line-height: 1;">&times;</button>
    </div>
    <div class="sidebar-content" style="padding: 15px; overflow-y: auto; height: calc(100% - 70px);"> {/* Approx header height */}
        <div id="ai-chat-trigger-placeholder-mobile" class="menu-section" style="margin-bottom: 15px;">
            <!-- El botón de Chat IA se clonará aquí para móvil por main.js -->
        </div>
        <div class="menu-section" style="margin-bottom: 15px;">
            <h4 class="gradient-text" style="font-size: 0.9em; margin-bottom: 8px; text-transform: uppercase; color: var(--epic-purple-emperor);">Navegación</h4>
            <div id="main-menu-placeholder">
                <!-- El menú principal (ul#main-menu) se clonará aquí -->
            </div>
        </div>
        <div id="admin-menu-placeholder-container" class="menu-section" style="margin-bottom: 15px;">
            <h4 class="gradient-text" style="font-size: 0.9em; margin-bottom: 8px; text-transform: uppercase; color: var(--epic-purple-emperor);">Admin</h4>
            <div id="admin-menu-placeholder">
                <!-- El menú de admin (contenido de #admin-menu-source-content) se clonará aquí -->
            </div>
        </div>
        <div class="menu-section">
            <h4 class="gradient-text" style="font-size: 0.9em; margin-bottom: 8px; text-transform: uppercase; color: var(--epic-purple-emperor);">Social</h4>
            <div id="social-menu-placeholder">
                <!-- Los enlaces sociales (contenido de #social-menu-source-content) se clonará aquí -->
            </div>
        </div>
    </div>
</div>
<?php if (file_exists(__DIR__ . "/slider_menu.php")) { include __DIR__ . "/slider_menu.php"; } ?>
