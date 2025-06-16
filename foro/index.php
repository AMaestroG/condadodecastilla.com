<?php require_once __DIR__ . '/../includes/head_common.php'; ?>
<body>
    <?php require_once __DIR__ . '/../_header.html'; ?>
    <main class="container page-content-block" style="padding: 2em 1em;">
        <h1>Foro Comunitario</h1>
        <form id="message-form" class="mb-4">
            <input type="text" id="author" placeholder="Tu nombre" required class="form-control mb-2">
            <textarea id="content" placeholder="Escribe tu mensaje" required class="form-control mb-2"></textarea>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <ul id="message-list" class="list-unstyled"></ul>
        <p><a href="/index.php" class="cta-button">Volver al inicio</a></p>
    </main>
    <?php require_once __DIR__ . '/../_footer.php'; ?>
    <script src="/js/layout.js"></script>
    <script src="/js/foro.js"></script>
</body>
</html>
