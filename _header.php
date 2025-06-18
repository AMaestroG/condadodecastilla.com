<div id="fixed-header-elements">
    <?php echo file_get_contents(__DIR__ . '/fragments/header/language-bar.html'); ?>
    <div class="header-action-buttons">
        <button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-expanded="false" role="button" aria-controls="consolidated-menu-items">☰</button>
        <button id="ia-chat-toggle" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA" aria-expanded="false" role="button"><i class="fas fa-comments"></i></button>
        <button id="homonexus-toggle" aria-label="Activar modo Homonexus" aria-expanded="false" role="button"><i class="fas fa-infinity"></i></button>
    </div>
</div>

<!-- Left Sliding Panel for Main Menu -->
<div id="consolidated-menu-items" class="menu-panel left-panel" role="navigation" aria-labelledby="consolidated-menu-button">
    <button id="ai-chat-trigger" class="menu-item-button" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA"><i class="fas fa-comments"></i> <span>Chat IA</span></button>
    <button id="theme-toggle" class="menu-item-button" aria-label="Cambiar tema"><i class="fas fa-moon"></i> <span>Modo</span></button>
    <button id="lang-bar-toggle" class="menu-item-button" aria-label="Mostrar u ocultar traducción"><i class="fas fa-globe"></i> <span>Idiomas</span></button>

    <div class="menu-section">
        <h4 class="gradient-text">Navegación Principal</h4>
        <?php
        if (file_exists(__DIR__ . '/fragments/menus/main-menu.html')) {
            echo file_get_contents(__DIR__ . '/fragments/menus/main-menu.html');
        }
        ?>
    </div>
    <div class="menu-section">
        <h4 class="gradient-text">Admin</h4>
        <?php include __DIR__ . '/fragments/menus/admin-menu.php'; ?>
    </div>
    <div class="menu-section">
        <h4 class="gradient-text">Social</h4>
        <?php
        if (file_exists(__DIR__ . '/fragments/menus/social-menu.html')) {
            echo file_get_contents(__DIR__ . '/fragments/menus/social-menu.html');
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
    if (file_exists(__DIR__ . '/fragments/header/ai-drawer.html')) {
        // We might need to wrap or modify ai-drawer.html if its root element isn't suitable
        // For now, directly include it.
        echo file_get_contents(__DIR__ . '/fragments/header/ai-drawer.html');
    } else {
        echo '<p>Error: AI Chat interface not found.</p>';
    }
    ?>
</div>
