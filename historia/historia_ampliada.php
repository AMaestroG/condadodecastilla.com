<?php
require_once __DIR__ . '/../includes/head_common.php';
require_once __DIR__ . '/../includes/markdown_utils.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php
    require_once __DIR__ . '/../includes/load_page_css.php';
    load_page_css();
    ?>
    <title>Historia Ampliada - Cerezo de Río Tirón</title>
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>
    <main class="container page-content-block">
        <h1 class="gradient-text">Historia Ampliada</h1>
        <section class="section article-content">
            <?php echo render_markdown_file(__DIR__ . '/../docs/historia_ampliada_nuevo4.md'); ?>
        </section>
    </main>
    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>
</body>
</html>
