<!DOCTYPE html>
<html lang="es">
<head>
<?php require_once __DIR__ . '/../../includes/head_common.php'; ?>
<?php
require_once __DIR__ . '/../../includes/load_page_css.php';
load_page_css();
?>
</head>
<body class="alabaster-bg">

    <?php require_once __DIR__ . '/../../_header.php'; ?>
    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.7), rgba(var(--color-negro-contraste-rgb), 0.85)), url('/assets/img/hero_historia_background.jpg');">
        <div class="hero-content">
            <h1>Galería Histórica</h1>
            <p>Imágenes, documentos y artefactos visuales que narran la rica historia del Condado de Castilla y sus gentes.</p>
        </div>
    </header>
    <main>
        <section class="section">
            <div class="container-epic">
                <h2 class="section-title">Tesoros Visuales de Nuestra Historia</h2>
                <p>Explora nuestra colección de imágenes históricas, fotografías de artefactos, mapas antiguos y representaciones de momentos clave. Cada elemento cuenta con una descripción para contextualizar su importancia.</p>
                <div class="gallery-container">
                    <!-- Example Gallery Item 1: Image -->
                    <div class="gallery-item">
                        <img src="/assets/img/galeria_colaborativa/ejemplo_atardecer_alcazar.jpg" alt="Ejemplo de Imagen Histórica 1">
                        <div class="gallery-item-description">
                            <h3>Atardecer en el Alcázar</h3>
                            <p>Una vista evocadora del Alcázar de Cerasio al atardecer. Esta fortaleza fue crucial en la defensa y consolidación temprana de Castilla.</p>
                        </div>
                    </div>
                     <div class="gallery-item">
                        <img src="/assets/img/Llana.jpg" alt="Iglesia de la Llana">
                            <h3>Iglesia de la Llana</h3>
                            <p>Fotografía de la Iglesia de la Llana, un importante hito arquitectónico y espiritual en la región, con posibles orígenes en una mezquita.</p>
                        <img src="/assets/img/Muralla.jpg" alt="Restos de Muralla">
                            <h3>Restos de Muralla</h3>
                            <p>Vestigios de las antiguas murallas que protegían asentamientos clave en el Condado de Castilla, testigos de un pasado defensivo.</p>
                </div>
                <p class="text-center" style="margin-top: 30px;"><em>Esta galería se irá enriqueciendo con más elementos visuales y documentales. Se recomienda usar Font Awesome para los iconos de archivo (PDF, TXT, DOC), por lo que el CSS de la página principal o _header.php debería incluir la biblioteca.</em></p>
            </div>
        </section>
    </main>
    <?php require_once __DIR__ . '/../../_footer.php'; ?>
    <script src="/js/layout.js"></script>
    <!-- Consider adding a link to Font Awesome if not already included globally, e.g., in _header.php or main CSS -->
</body>
</html>
