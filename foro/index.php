<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
$agents = require __DIR__ . '/../config/forum_agents.php';
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
}

// Ensure comments table exists
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS forum_comments (
        id SERIAL PRIMARY KEY,
        agent VARCHAR(50) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Foro de Expertos</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="/assets/css/pages/foro.css">
    <link rel="stylesheet" href="/assets/forum-app/forum-index.css">
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/../fragments/header.php'; ?>
<div id="forum-root" class="container page-content-block"></div>
<script type="module" src="/assets/forum-app/forum.js"></script>
<?php require_once __DIR__ . '/../fragments/footer.php'; ?>

</body>
</html>
