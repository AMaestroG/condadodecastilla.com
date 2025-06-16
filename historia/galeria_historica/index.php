<?php require_once __DIR__ . '/../../includes/head_common.php'; ?>
<head>
    <style>
        /* Basic gallery styling */
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Space between items */
            justify-content: center;
            padding: 20px;
        }
        .gallery-item {
            background-color: #fdfaf6; /* Fondo alabastro claro */
            border: 1px solid #ddd; /* Light grey border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Subtle shadow */
            width: calc(33.333% - 40px); /* Adjust for 3 items per row, considering gap */
            margin-bottom: 20px;
            overflow: hidden; /* Ensures content fits rounded corners */
            text-align: center;
        }
        .gallery-item img, .gallery-item .file-placeholder {
            width: 100%;
            height: 200px; /* Fixed height for image/placeholder area */
            object-fit: cover; /* Cover ensures image fills space, might crop */
            border-bottom: 1px solid #ddd;
        }
        .gallery-item .file-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0; /* Light background for placeholder */
            font-size: 3em; /* Larger icon */
            color: #aaa; /* Grey icon color */
        }
        .gallery-item-description {
            padding: 15px;
            text-align: left;
        }
        .gallery-item-description h3 {
            margin-top: 0;
            color: var(--color-primario-purpura);
        }
        .gallery-item-description p {
            font-size: 0.9em;
            color: #333; /* Darker grey for text */
            margin-bottom: 10px;
        }
        .gallery-item-description a {
            font-size: 0.9em;
            color: var(--color-secundario-dorado);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .gallery-item {
                width: calc(50% - 30px); /* 2 items per row */
            }
        }
        @media (max-width: 768px) {
            .gallery-item {
                width: calc(100% - 20px); /* 1 item per row */
            }
        }
    </style>
</head>
<body>

    <?php require_once __DIR__ . '/../../_header_template.php'; ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.7), rgba(var(--color-negro-contraste-rgb), 0.85)), url('/assets/img/hero_historia_background.jpg');">
        <div class="hero-content">
            <h1>Galería Histórica</h1>
            <p>Imágenes, documentos y artefactos visuales que narran la rica historia del Condado de Castilla y sus gentes.</p>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
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
                        <div class="gallery-item-description">
                            <h3>Iglesia de la Llana</h3>
                            <p>Fotografía de la Iglesia de la Llana, un importante hito arquitectónico y espiritual en la región, con posibles orígenes en una mezquita.</p>
                        </div>
                    </div>
                     <div class="gallery-item">
                        <img src="/assets/img/Muralla.jpg" alt="Restos de Muralla">
                        <div class="gallery-item-description">
                            <h3>Restos de Muralla</h3>
                            <p>Vestigios de las antiguas murallas que protegían asentamientos clave en el Condado de Castilla, testigos de un pasado defensivo.</p>
                        </div>
                    </div>
                </div>
                <p class="text-center" style="margin-top: 30px;"><em>Esta galería se irá enriqueciendo con más elementos visuales y documentales. Se recomienda usar Font Awesome para los iconos de archivo (PDF, TXT, DOC), por lo que el CSS de la página principal o _header_template.php debería incluir la biblioteca.</em></p>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../_footer.php'; ?>
    <script src="/js/layout.js"></script>
    <!-- Consider adding a link to Font Awesome if not already included globally, e.g., in _header_template.php or main CSS -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
</body>
</html>
