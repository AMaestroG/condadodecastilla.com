<footer class="footer">
    <div class="container-epic">
        <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
        <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
        <?php
        require_once __DIR__ . '/includes/auth.php';
        if (is_admin_logged_in()) {
            echo '<p><a href="/dashboard/logout.php">Cerrar sesión</a></p>';
        } else {
            echo '<p><a href="/dashboard/login.php">Acceso Administrador</a></p>';
        }
        ?>
        <?php
        $social_fragment = __DIR__ . '/fragments/menus/social-menu.html';
        if (file_exists($social_fragment)) {
            echo file_get_contents($social_fragment);
        }
        ?>
</div>
</footer>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/hero.js"></script>
<script src="/assets/js/scroll-fade.js"></script>
<script src="/js/lang-bar.js"></script>
