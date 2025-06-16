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
        <div class="social-links">
            <a href="https://www.facebook.com/groups/1052427398664069" aria-label="Facebook" title="Síguenos en Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
            <a href="/en_construccion.php" aria-label="Instagram" title="Síguenos en Instagram (Próximamente)" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
            <a href="/en_construccion.php" aria-label="Twitter" title="Síguenos en Twitter (Próximamente)" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
            <a href="https://chat.whatsapp.com/JWJ6mWXPuekIBZ8HJSSsZx" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="Únete a nuestro grupo de WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
</div>
</footer>
<script src="/assets/js/main.js"></script>
