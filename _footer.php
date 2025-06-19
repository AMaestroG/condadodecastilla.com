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
        require_once __DIR__ . '/includes/env_loader.php';
        $useMin = filter_var($_ENV['USE_MINIFIED_ASSETS'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $min = $useMin ? '.min' : '';
        ?>
        <?php
        $social_fragment = __DIR__ . '/fragments/menus/social-menu.html';
        if (file_exists($social_fragment)) {
            echo file_get_contents($social_fragment);
        }
        ?>
</div>
</footer>
<script src="/assets/js/main{$min}.js"></script>
<script src="/js/lang-bar.js"></script>
