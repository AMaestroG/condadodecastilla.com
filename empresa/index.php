<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Yacimientos - Empresa Básica</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div id="header-placeholder"></div>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.75), rgba(var(--color-negro-contraste-rgb), 0.88)), url('/assets/img/hero_contacto_background.jpg');">
        <div class="hero-content">
            <img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Empresa de Gestión de Yacimientos</h1>
            <p>Descubre cómo administramos y conservamos el patrimonio arqueológico de la región.</p>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container page-content-block">
                <h2 class="section-title">Nuestra Misión</h2>
                <p>Somos una empresa local dedicada a la coordinación y mantenimiento de los yacimientos arqueológicos del Condado de Castilla y alrededores. Trabajamos de la mano con las autoridades municipales y con expertos en patrimonio para preservar estos tesoros históricos.</p>

                <h2 class="section-title">Servicios</h2>
                <ul>
                    <li>Supervisión de excavaciones y tareas de conservación.</li>
                    <li>Programas educativos y visitas guiadas para escuelas y turistas.</li>
                    <li>Colaboración con universidades y equipos de investigación.</li>
                    <li>Asesoría a ayuntamientos sobre la gestión del patrimonio cultural.</li>
                </ul>

                <h2 class="section-title">Contáctanos</h2>
                <p>Para más información o para colaborar con nosotros, escríbenos a <a href="mailto:info@condadodecastilla.com">info@condadodecastilla.com</a>.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container-epic">
            <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
            <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/groups/1052427398664069" aria-label="Facebook" title="Síguenos en Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                <a href="/en_construccion.html" aria-label="Instagram" title="Síguenos en Instagram (Próximamente)" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                <a href="/en_construccion.html" aria-label="Twitter" title="Síguenos en Twitter (Próximamente)" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
                <a href="https://chat.whatsapp.com/JWJ6mWXPuekIBZ8HJSSsZx" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="Únete a nuestro grupo de WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>

    <script src="/js/layout.js" defer></script>
</body>
</html>
