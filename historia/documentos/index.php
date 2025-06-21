<!DOCTYPE html>
<html lang="es">
<head>
<?php $load_aos = true; require_once __DIR__ . '/../../includes/head_common.php'; ?>
</head>
<body class="alabaster-bg">

    <?php require_once __DIR__ . '/../../fragments/header.php'; ?>

    <header class="page-header hero bg-[url('/assets/img/hero_historia_background.jpg')] bg-cover bg-center md:bg-center">
        <div class="hero-content">
            <h1>Documentos Históricos</h1>
            <p>Una colección de documentos, manuscritos y archivos relacionados con la historia del Condado de Castilla y Cerezo de Río Tirón.</p>
        </div>
    </header>

    <main>
        <section class="section alternate-bg">
            <div class="container-epic">
                <h2 class="section-title">Archivos y Manuscritos</h2>
                <p class="scroll-fade opacity-0 transition-opacity duration-700 hover:opacity-100 focus:opacity-100" data-aos="fade-up" tabindex="0">Esta sección alberga diversos documentos históricos disponibles para consulta. Los archivos pueden incluir PDFs, documentos de texto, e imágenes de manuscritos originales.</p>

                <div class="document-list">
                    <!-- Example of how a document might be listed. Repeat for each document. -->
                    <!-- This will be dynamically populated or manually updated later. -->

                    <p class="scroll-fade opacity-0 transition-opacity duration-700 hover:opacity-100 focus:opacity-100" data-aos="fade-up" tabindex="0" style="margin-top: 30px;"><em>Actualmente, esta es una lista de ejemplos. Se añadirán más documentos próximamente.</em></p>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../fragments/footer.php'; ?>
    
</body>
</html>
