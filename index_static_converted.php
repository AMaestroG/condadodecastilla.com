<?php require_once __DIR__ . '/includes/head_common.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; url=/index.php">
    <title>Redirigiendo...</title>
    <script>
        // Fallback in case the meta refresh tag fails
        window.location.replace('/index.php');
    </script>
</head>
<body>
    <?php require_once __DIR__ . '/_header.html'; ?>
    <main class="container page-content-block" style="text-align: center; padding: 4em 1em;">
        <p>Si no es redirigido automáticamente, haga clic <a href="/index.php">aquí</a>.</p>
    </main>
    <?php require_once __DIR__ . '/_footer.php'; ?>
    <script src="/js/layout.js"></script>
</body>
</html>
