<?php
// require_once __DIR__ . '/_header.html';
// Load AI assistant drawer so it is available on every page
// echo file_get_contents(__DIR__ . '/fragments/header/ai-drawer.html'); // AI drawer is loaded via js/layout.js or specific pages.
// We need to ensure the actual AI drawer HTML is loaded on the page for the toggle to work.
// For now, let's assume fragments/header/ai-drawer.html is loaded elsewhere or add it if necessary.
// One option is to include it here conditionally or ensure it's in a global include.
if (file_exists(__DIR__ . '/fragments/header/ai-drawer.html')) {
    echo file_get_contents(__DIR__ . '/fragments/header/ai-drawer.html');
}
?>
<button id="consolidated-menu-button" class="menu-btn dark-mode menu-open-right menu-open-left" aria-label="Abrir menú consolidado" aria-expanded="false">☰</button>
<div id="consolidated-menu-items" style="display: none;">
    <button id="theme-toggle" class="menu-item-button" aria-label="Toggle theme"><i class="fas fa-moon"></i> <span>Tema</span></button>
    <button id="ai-drawer-toggle" class="menu-item-button" aria-label="Toggle AI Assistant"><i class="fas fa-robot"></i> <span>Asistente IA</span></button>

    <div class="menu-section">
        <h3>Principal</h3>
        <?php echo file_get_contents(__DIR__ . '/fragments/menus/main-menu.html'); ?>
    </div>

    <div class="menu-section">
        <h3>Admin</h3>
        <?php include __DIR__ . '/fragments/menus/admin-menu.php'; ?>
    </div>

    <div class="menu-section">
        <h3>Social</h3>
        <?php echo file_get_contents(__DIR__ . '/fragments/menus/social-menu.html'); ?>
    </div>

    <div class="menu-section">
        <h3>Herramientas</h3>
        <?php echo file_get_contents(__DIR__ . '/fragments/menus/tools-menu.html'); ?>
    </div>
</div>
