<div id="cave-mask"></div>
<div id="fixed-header-elements">
    <div class="header-action-buttons">
        <button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-expanded="false" role="button" aria-controls="consolidated-menu-items">☰</button>
        <button id="flag-toggle" data-menu-target="language-panel" aria-label="Seleccionar idioma" aria-expanded="false" role="button" aria-controls="language-panel"><i class="fas fa-flag"></i></button>
        <button id="mute-toggle" aria-pressed="false" aria-label="Silenciar">🔊</button>
    </div>
</div>

<!-- Left Sliding Panel for Main Menu -->
<div id="consolidated-menu-items" class="menu-panel left-panel" role="navigation" aria-labelledby="consolidated-menu-button">
    <button id="ai-chat-trigger" class="menu-item-button transition-colors transition-transform transition-shadow" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA"><i class="fas fa-comments"></i> <span>Chat IA</span></button>
    <button id="theme-toggle" class="menu-item-button transition-colors transition-transform transition-shadow" aria-label="Cambiar tema"><i class="fas fa-moon"></i> <span>Modo</span></button>
    <button id="moon-toggle" class="menu-item-button transition-colors transition-transform transition-shadow">🌙 Modo luna</button>
    <button id="palette-toggle" class="menu-item-button transition-colors transition-transform transition-shadow" aria-label="Cambiar paleta"><i class="fas fa-palette"></i> <span>Paleta</span></button>

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
        <?php include __DIR__ . '/menus/admin-menu.php'; ?>
    </div>
    <div class="menu-section">
        <h4 class="gradient-text">Social</h4>
        <?php
        if (file_exists(__DIR__ . '/menus/social-menu.html')) {
            echo file_get_contents(__DIR__ . '/menus/social-menu.html');
        }
        ?>
    </div>
    <!-- Add other menu items or buttons here as needed -->
</div>

<!-- Right Sliding Panel for AI Chat -->
<div id="ai-chat-panel" class="menu-panel right-panel" role="dialog" aria-labelledby="ai-chat-title">
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
        echo '<div id="language-panel" class="menu-panel right-panel"><p>Flags not found.</p></div>';
    }
?>
