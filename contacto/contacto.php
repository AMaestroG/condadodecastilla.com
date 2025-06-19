<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';      // For is_admin_logged_in()
require_once __DIR__ . '/../includes/db_connect.php'; // Provides $pdo
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
}
require_once __DIR__ . '/../includes/text_manager.php';// For editableText()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Contacto - Condado de Castilla</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">


    <?php include __DIR__ . '/../includes/head_common.php'; ?>
    <?php
    require_once __DIR__ . '/../includes/load_page_css.php';
    load_page_css();
    ?>
</head>
<body>

    <?php
    require_once __DIR__ . '/../_header.php';
    ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.75), rgba(var(--color-negro-contraste-rgb), 0.88)), url('/assets/img/hero_contacto_background.jpg');">
        <div class="hero-content">
            <img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <?php editableText('contacto_hero_titulo', $pdo, 'Ponte en Contacto', 'h1'); ?>
            <?php editableText('contacto_hero_subtitulo', $pdo, '¿Preguntas, sugerencias o deseas colaborar? Estamos aquí para escucharte.', 'p'); ?>
        </div>
    </header>

    <main>
        <section class="section contact-section"> <div class="container page-content-block"> <div class="contact-info">
                    <?php editableText('contacto_info_intro', $pdo, 'Puedes enviarnos un mensaje directamente usando el formulario a continuación o escribirnos a nuestra dirección de correo electrónico:', 'p'); ?>
                    <p><i class="fas fa-envelope"></i> <?php editableText('contacto_info_email', $pdo, 'info@condadodecastilla.com', 'a', 'email-link', 'href="mailto:info@condadodecastilla.com"'); ?></p>
                    <p><i class="fas fa-phone"></i> <?php editableText('contacto_info_telefono', $pdo, '+34&nbsp;600&nbsp;123&nbsp;456', 'a', 'email-link', 'href="tel:+34600123456"'); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php editableText('contacto_info_direccion_texto', $pdo, 'Plaza Mayor&nbsp;1, 09270 Cerezo de Río Tirón, Burgos', 'span'); ?></p>
                </div>

                <div class="contact-form-container">
                    <?php editableText('contacto_form_titulo', $pdo, 'Formulario de Contacto', 'h2', 'section-title'); ?>
                    <form id="contactForm" method="post" action="submit.php">
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-user"></i> <?php editableText('contacto_form_label_nombre_texto', $pdo, 'Nombre Completo:', 'span'); ?></label>
                            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre y apellidos">
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-at"></i> <?php editableText('contacto_form_label_email_texto', $pdo, 'Correo Electrónico:', 'span'); ?></label>
                            <input type="email" id="email" name="email" required placeholder="tu.correo@ejemplo.com">
                        </div>
                        <div class="form-group">
                            <label for="asunto"><i class="fas fa-pen-nib"></i> <?php editableText('contacto_form_label_asunto_texto', $pdo, 'Asunto:', 'span'); ?></label>
                            <input type="text" id="asunto" name="asunto" required placeholder="Motivo de tu contacto">
                        </div>
                        <div class="form-group">
                            <label for="mensaje"><i class="fas fa-comment-dots"></i> <?php editableText('contacto_form_label_mensaje_texto', $pdo, 'Mensaje:', 'span'); ?></label>
                            <textarea id="mensaje" name="mensaje" rows="7" required placeholder="Escribe tu mensaje aquí..."></textarea>
                        </div>
                        <?php editableText('contacto_form_boton_enviar', $pdo, 'Enviar Mensaje', 'button', 'cta-button submit-button', 'type="submit"'); ?>
                    </form>
                </div>
                <div class="map-container" style="margin-top: 40px;">
                    <iframe title="Mapa de Cerezo de Río Tirón" width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=-3.1756369%2C42.4719916%2C-3.0956369%2C42.5119916&amp;layer=mapnik&amp;marker=42.4919916%2C-3.1356369"></iframe>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../_footer.php'; ?>

    <script src="/js/layout.js"></script>
    <script>
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(event) {
                let name = document.getElementById('nombre').value.trim();
                let email = document.getElementById('email').value.trim();
                let subject = document.getElementById('asunto').value.trim();
                let message = document.getElementById('mensaje').value.trim();

                if (!name || !email || !subject || !message) {
                    alert('Por favor, completa todos los campos requeridos.');
                    event.preventDefault(); 
                    return;
                }
                
                // Los datos se enviarán al servidor; solo validamos en el cliente.
            });
        }
    </script>
</body>
</html>
