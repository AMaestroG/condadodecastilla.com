<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Yacimientos - Empresa Básica</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <!-- Google Fonts, FontAwesome, and epic_theme.css are now in head_common.php -->
</head>
<body>
    <?php require_once __DIR__ . '/../_header.php'; ?>

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

    <?php require_once __DIR__ . '/../_footer.php'; ?>

    <script src="/js/layout.js" defer></script>
</body>
</html>
