<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
$geminiNotice = $_SESSION['gemini_api_key_notice'] ?? '';
if ($geminiNotice) {
    echo "<div class='notice-error' role='alert'>" . htmlspecialchars($geminiNotice) . "</div>";
}
?>
<div id="cave-mask"></div>
<img id="header-escudo-overlay" class="hero-escudo" src="/assets/img/escudo.jpg" alt="Escudo de Cerezo de Río Tirón">
<div id="fixed-header-elements" style="height: var(--header-footer-height);">
    <div class="header-action-buttons">
        <img loading="lazy" src="/assets/icons/columna.svg" class="header-icon" alt="Roman column icon" />
        <button id="admin-menu-button" data-menu-target="admin-menu-items" aria-label="Abrir menú administrador" aria-expanded="false" role="button" aria-controls="admin-menu-items">☰</button>
        <button id="homonexus-toggle" aria-label="Activar Homonexus" aria-pressed="false">👥</button>
    </div>
</div>

<!-- Sliding panel with admin links -->
<div id="admin-menu-items" class="menu-panel left-panel" role="navigation" aria-labelledby="admin-menu-button">
    <button id="ai-chat-trigger" class="menu-item-button" data-menu-target="ai-chat-panel" aria-label="Abrir chat IA" aria-haspopup="dialog"><i class="fas fa-comments"></i> <span>Chat IA</span></button>
    <div class="menu-section">
        <h4 class="gradient-text">Administración</h4>
        <ul class="nav-links">
            <li><a href="/dashboard/index.php">Dashboard</a></li>
            <li><a href="/dashboard/edit_texts.php">Textos</a></li>
            <li><a href="/dashboard/tienda_admin.php">Tienda</a></li>
            <li><a href="/museo/editar_pieza.php">Piezas Museo</a></li>
            <li><a href="/foro/manage_comments.php">Comentarios</a></li>
            <li><a href="/dashboard/create_user.php">Crear Usuario</a></li>
            <li><a href="/scripts_admin.php">Scripts</a></li>
            <li><a href="/dashboard/logout.php">Cerrar sesión</a></li>
            <li><a href="/index.php">Inicio Sitio</a></li>
        </ul>
    </div>
</div>

<!-- Right Sliding Panel for AI Chat -->
<div id="ai-chat-panel" class="menu-panel right-panel" role="dialog" aria-modal="true" aria-labelledby="ai-chat-title" tabindex="-1" aria-hidden="true">
    <?php
    if (file_exists(__DIR__ . '/header/ai-drawer.html')) {
        echo file_get_contents(__DIR__ . '/header/ai-drawer.html');
    } else {
        echo '<p>Error: AI Chat interface not found.</p>';
    }
    ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('admin-menu-button');
    const menu = document.getElementById('admin-menu-items');
    if (!btn || !menu) return;
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const open = menu.classList.toggle('active');
        btn.setAttribute('aria-expanded', open);
        document.body.classList.toggle('menu-open-left', open);
    });
    document.addEventListener('click', function(e) {
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-open-left');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            menu.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-open-left');
        }
    });
});
</script>
