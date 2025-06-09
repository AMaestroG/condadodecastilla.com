<?php
require_once __DIR__ . '/../../includes/auth.php';
echo '<ul id="admin-menu" class="nav-links">';
if (is_admin_logged_in()) {
    echo '<li><a href="/dashboard/logout.php">Cerrar sesión</a></li>';
    echo '<li><a href="/dashboard/index.php">Panel</a></li>';
} else {
    echo '<li><a href="/dashboard/login.php">Admin</a></li>';
}
echo '</ul>';
?>
