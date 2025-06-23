<?php
$json_path = __DIR__ . '/../../data/historia/indice_detallado.json';
$page_data = null;
$error_message = '';

if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $decoded_data = json_decode($json_content, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $page_data = $decoded_data;
    } else {
        $error_message = 'Error al decodificar el archivo JSON de datos: ' . json_last_error_msg();
    }
} else {
    $error_message = 'Error: No se pudo encontrar el archivo JSON de datos: ' . $json_path;
}
// Valores por defecto por si el JSON falla o no tiene todos los campos
$titulo_pagina = $page_data['titulo_pagina'] ?? 'Índice de Temas Históricos';
$descripcion_general = $page_data['descripcion_general'] ?? 'No se pudo cargar la descripción.';
$temas_detallados = $page_data['temas_detallados'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php require_once __DIR__ . '/../includes/head_common.php'; ?>
<?php
require_once __DIR__ . '/../includes/load_page_css.php';
load_page_css();
?>
    <title><?php echo htmlspecialchars($titulo_pagina); ?> - Cerezo de Río Tirón</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">
    <!-- Google Fonts, FontAwesome, and epic_theme.css are now in head_common.php -->
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>
    <header class="page-header hero min-h-[30vh] bg-[url('/imagenes/mapa_antiguo_detalle.jpg')] bg-cover bg-center md:bg-center">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>
        </div>
    </header>
    <main>
        <section class="section">
            <div class="container-epic">
                <h2>Explorando la Historia de Cerezo de Río Tirón en Detalle</h2>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php else: ?>
                    <p class="intro-centered"><?php echo htmlspecialchars($descripcion_general); ?></p>
                    <p class="text-center mb-25"><a href="/historia/historia.php" class="read-more" style="background-color: var(--epic-purple-emperor);">&laquo; Volver a la página principal de Nuestra Historia</a></p>
                    <?php if (!empty($temas_detallados)): ?>
                        <ul class="subpage-index-list">
                            <?php foreach ($temas_detallados as $tema): ?>
                                <li class="subpage-index-item">
                                    <h3><?php echo htmlspecialchars($tema['titulo_tema'] ?? 'Título no disponible'); ?></h3>
                                    <p><?php echo htmlspecialchars($tema['descripcion_corta'] ?? 'Descripción no disponible.'); ?></p>
                                    <?php if (isset($tema['ruta_html_original'])): ?>
                                        <a href="/<?php echo htmlspecialchars(ltrim($tema['ruta_html_original'], '/')); ?>" class="read-more">Leer más...</a>
                                    <?php else: ?>
                                        <span class="read-more" style="background-color: var(--epic-alabaster-medium);">Enlace no disponible</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay temas detallados disponibles en este momento.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>
    
</body>
</html>
