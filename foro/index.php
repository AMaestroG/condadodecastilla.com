<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../dashboard/db_connect.php';
/** @var PDO $pdo */

// Ensure comments table exists
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS forum_comments (
        id SERIAL PRIMARY KEY,
        agent VARCHAR(50) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$agents = [
    'historian' => [
        'name' => 'Alicia la Historiadora',
        'bio' => 'Especialista en la historia de Castilla y de Cerezo de Río Tirón.'
    ],
    'archaeologist' => [
        'name' => 'Bruno el Arqueólogo',
        'bio' => 'Explora y protege nuestro valioso patrimonio arqueológico.'
    ],
    'guide' => [
        'name' => 'Clara la Guía Turística',
        'bio' => 'Conoce cada rincón y te orienta en tus visitas.'
    ],
    'manager' => [
        'name' => 'Diego el Gestor Cultural',
        'bio' => 'Coordina eventos y proyectos para dinamizar la cultura local.'
    ],
    'technologist' => [
        'name' => 'Elena la Tecnóloga',
        'bio' => 'Aplica la innovación tecnológica al servicio del patrimonio.'
    ],
];

function fetchComments(string $agent, ?PDO $pdo): array {
    if (!$pdo) return [];
    $stmt = $pdo->prepare('SELECT comment, created_at FROM forum_comments WHERE agent = :agent ORDER BY created_at DESC');
    $stmt->execute([':agent' => $agent]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $agent = $_POST['agent'] ?? '';
    $comment = trim($_POST['comment'] ?? '');
    $maxLength = 500;
    $rateLimit = 60; // seconds

    if (strlen($comment) > $maxLength) {
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
<?php require_once __DIR__ . '/../includes/components/header/header.php'; ?>
<button id="menu-btn" class="menu-btn">☰ Expertos</button>
<nav id="slide-menu" class="slide-menu">
<?php foreach ($agents as $id => $ag): ?>
    <a href="#<?php echo $id; ?>"><?php echo htmlspecialchars($ag['name']); ?></a>
<?php endforeach; ?>
</nav>
<main class="container page-content-block">
    <h1 style="text-align:center;">Foro de Expertos</h1>
    <?php if (!empty($_SESSION['forum_error'])): ?>
        <p class="feedback error"><?php echo htmlspecialchars($_SESSION['forum_error']); unset($_SESSION['forum_error']); ?></p>
    <?php endif; ?>
    <?php foreach ($agents as $id => $ag): ?>
    <section id="<?php echo $id; ?>" class="agent-profile">
        <h2><?php echo htmlspecialchars($ag['name']); ?></h2>
        <p><?php echo htmlspecialchars($ag['bio']); ?></p>
        <form method="post">
            <input type="hidden" name="agent" value="<?php echo $id; ?>">
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
<?php require_once __DIR__ . '/../includes/components/footer/footer.php'; ?>
<script src="/js/layout.js"></script>
<script src="/assets/js/foro.js"></script>
</body>
</html>
