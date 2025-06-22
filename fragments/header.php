<div id="cave-mask"></div>
<img id="header-escudo-overlay" class="hero-escudo" src="/assets/img/escudo.jpg" alt="Escudo de Cerezo de Río Tirón">
<div id="fixed-header-elements" style="height: var(--header-footer-height);">
    <div class="site-branding">
        <a href="/" class="logo-link-header" aria-label="Página de inicio de Condado de Castilla">
            <span class="site-title-header">Condado de Castilla</span>
        </a>
    </div>
    <div class="header-action-buttons">
        <img src="/assets/icons/columna.svg" class="header-icon" alt="Roman column icon" />
        <button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-haspopup="true" aria-expanded="false" role="button" aria-controls="consolidated-menu-items">☰</button>
        <button id="flag-toggle" data-menu-target="language-panel" aria-label="Seleccionar idioma" aria-haspopup="true" aria-expanded="false" role="button" aria-controls="language-panel"><i class="fas fa-flag"></i></button>
        <button id="mute-toggle" aria-pressed="false" aria-label="Silenciar">🔊</button>
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
