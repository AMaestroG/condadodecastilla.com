<button id="consolidated-menu-button" data-menu-target="consolidated-menu-items" aria-label="Abrir menú principal" aria-expanded="false">☰</button>

<!-- Left Sliding Panel for Main Menu -->
<div id="consolidated-menu-items" class="menu-panel left-panel vertical-tabs" role="navigation" aria-label="Secciones del menú">
    <button id="theme-toggle" class="menu-item-button" aria-label="Cambiar tema"><i class="fas fa-lightbulb"></i> <span>Tema</span></button>
    <button id="ai-chat-trigger" class="menu-item-button" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA"><i class="fas fa-comments"></i> <span>Chat IA</span></button>

    <div class="tab-container">
        <div id="menu-tablist" class="tab-headers" role="tablist">
            <button class="tab-header" role="tab" aria-controls="tab-main" data-tab-target="tab-main" id="tab-main-header" aria-selected="true">Navegación Principal</button>
            <button class="tab-header" role="tab" aria-controls="tab-tools" data-tab-target="tab-tools" id="tab-tools-header" aria-selected="false">Herramientas</button>
            <button class="tab-header" role="tab" aria-controls="tab-admin" data-tab-target="tab-admin" id="tab-admin-header" aria-selected="false">Admin</button>
            <button class="tab-header" role="tab" aria-controls="tab-social" data-tab-target="tab-social" id="tab-social-header" aria-selected="false">Social</button>
        </div>
        <div class="tab-panels">
            <div id="tab-main" class="tab-panel menu-section" role="tabpanel" aria-labelledby="tab-main-header">
                <h4 class="gradient-text">Navegación Principal</h4>
                <?php
                if (file_exists(__DIR__ . '/fragments/menus/main-menu.html')) {
                    echo file_get_contents(__DIR__ . '/fragments/menus/main-menu.html');
                }
                ?>
            </div>
            <div id="tab-tools" class="tab-panel menu-section" role="tabpanel" aria-labelledby="tab-tools-header">
                <h4 class="gradient-text">Herramientas</h4>
                <?php
                if (file_exists(__DIR__ . '/fragments/menus/tools-menu.html')) {
                    echo file_get_contents(__DIR__ . '/fragments/menus/tools-menu.html');
                }
                ?>
            </div>
            <div id="tab-admin" class="tab-panel menu-section" role="tabpanel" aria-labelledby="tab-admin-header">
                <h4 class="gradient-text">Admin</h4>
                <?php include __DIR__ . '/fragments/menus/admin-menu.php'; ?>
            </div>
            <div id="tab-social" class="tab-panel menu-section" role="tabpanel" aria-labelledby="tab-social-header">
                <h4 class="gradient-text">Social</h4>
                <?php
                if (file_exists(__DIR__ . '/fragments/menus/social-menu.html')) {
                    echo file_get_contents(__DIR__ . '/fragments/menus/social-menu.html');
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Add other menu items or buttons here as needed -->
</div>

<!-- Right Sliding Panel for AI Chat -->
<div id="ai-chat-panel" class="menu-panel right-panel">
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
