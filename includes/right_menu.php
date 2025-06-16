<?php
$baseDir = dirname(__DIR__);
// Output tools menu
echo file_get_contents($baseDir . '/fragments/menus/tools-menu.html');
?>
<div style="padding:1rem;">
    <button id="theme-toggle" title="Cambiar tema" style="margin-bottom:1rem;">Tema</button>
    <button id="ai-drawer-toggle" title="Asistente IA">IA</button>
</div>
