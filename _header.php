<?php
echo file_get_contents(__DIR__ . "/fragments/header/language-bar.html");
?>
<button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-expanded="false" role="button" aria-controls="consolidated-menu-items">☰</button>
<button id="theme-toggle" aria-label="Cambiar tema"><i class="fas fa-moon"></i></button>

<!-- Left Sliding Panel for Main Menu -->
<div id="consolidated-menu-items" class="menu-panel left-panel" role="navigation" aria-labelledby="consolidated-menu-button">
    <button id="ai-chat-trigger" class="menu-item-button" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA"><i class="fas fa-comments"></i> <span>Chat IA</span></button>
    <button id="notes-trigger" class="menu-item-button" data-menu-target="notes-panel" aria-label="Abrir notas"><i class="fas fa-sticky-note"></i> <span>Notas</span></button>

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

<!-- Right Sliding Panel for Notes -->
<div id="notes-panel" class="menu-panel right-panel" role="dialog" aria-labelledby="notes-title">
    <div class="ai-drawer-header">
        <h3 id="notes-title">Notas personales</h3>
        <button id="close-notes-panel" aria-label="Cerrar notas">✕</button>
    </div>
    <textarea id="user-notes" rows="10" style="flex-grow:1;"></textarea>
</div>
