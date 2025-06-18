<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../dashboard/db_connect.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
$agents = require __DIR__ . '/../config/forum_agents.php';
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>El foro est\xc3\xa1 en modo solo lectura.</p>";
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



function fetchComments(string $agent, ?PDO $pdo): array {
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT comment, created_at FROM forum_comments WHERE agent = :agent ORDER BY created_at DESC');
    $stmt->execute([':agent' => $agent]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $agent = $_POST['agent'] ?? '';
    $comment = trim($_POST['comment'] ?? '');
    $token = $_POST['csrf_token'] ?? '';
    $maxLength = 500;
    $rateLimit = FORUM_COMMENT_COOLDOWN; // seconds

    if (!verify_csrf_token($token)) {
        $_SESSION['forum_error'] = 'CSRF token inválido.';
    } elseif (strlen($comment) > $maxLength) {
        $_SESSION['forum_error'] = 'Comentario demasiado largo.';
    } elseif (isset($_SESSION['last_comment_time']) && (time() - $_SESSION['last_comment_time']) < $rateLimit) {
        $_SESSION['forum_error'] = 'Espera unos segundos antes de comentar de nuevo.';
    } elseif ($agent && $comment && isset($agents[$agent])) {
        $stmt = $pdo->prepare('INSERT INTO forum_comments (agent, comment) VALUES (:agent, :comment)');
        $stmt->execute([':agent' => $agent, ':comment' => $comment]);
        $_SESSION['last_comment_time'] = time();
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '#' . $agent);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Foro de Expertos</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="/assets/css/pages/foro.css">
</head>
<body>
<?php require_once __DIR__ . '/../_header.php'; ?>
<button id="menu-btn" class="menu-btn" data-menu-target="agents-menu">☰ Expertos</button>
<nav id="agents-menu" class="slide-menu left">
<?php foreach ($agents as $id => $ag): ?>
    <a href="#<?php echo $id; ?>" class="gradient-title"><?php echo htmlspecialchars($ag['name']); ?></a>
<?php endforeach; ?>
</nav>
<main class="container page-content-block">
    <h1 style="text-align:center;">Foro de Expertos</h1>
    <?php if (!empty($_SESSION['forum_error'])): ?>
        <p class="feedback error"><?php echo htmlspecialchars($_SESSION['forum_error']); unset($_SESSION['forum_error']); ?></p>
    <?php endif; ?>
    <?php foreach ($agents as $id => $ag): ?>
    <section id="<?php echo $id; ?>" class="agent-profile">
        <h2 class="gradient-title"><?php echo htmlspecialchars($ag['name']); ?></h2>
        <p><?php echo htmlspecialchars($ag['bio']); ?></p>
        <form method="post">
            <input type="hidden" name="agent" value="<?php echo $id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <textarea name="comment" rows="3" required placeholder="Comparte tu consejo o comentario"></textarea>
            <button type="submit" class="cta-button submit-button">Publicar</button>
        </form>
        <?php $comments = fetchComments($id, $pdo); ?>
        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p style="font-style: italic;">Aún no hay comentarios.</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                <div class="comment-item">
                    <p><?php echo htmlspecialchars($c['comment']); ?></p>
                    <small><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($c['created_at']))); ?></small>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php endforeach; ?>
</main>
<?php require_once __DIR__ . '/../_footer.php'; ?>
<script src="/assets/js/foro.js"></script>
<script src="/js/layout.js"></script>
</body>
</html>
