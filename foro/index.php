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
    if ($agent && $comment && isset($agents[$agent])) {
        $stmt = $pdo->prepare('INSERT INTO forum_comments (agent, comment) VALUES (:agent, :comment)');
        $stmt->execute([':agent' => $agent, ':comment' => $comment]);
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
    <style>
        body { background-color: rgba(245,235,220,0.95); }
        .agent-profile {
            background-color: rgba(255,255,255,0.6);
            padding: 1em;
            margin: 2em 0;
            border-left: 5px solid var(--color-primario-purpura, #4A0D67);
            backdrop-filter: blur(3px);
        }
        .agent-profile h2 {
            background: linear-gradient(45deg, var(--color-primario-purpura, #4A0D67), var(--color-secundario-dorado, #B8860B));
            -webkit-background-clip: text;
            color: transparent;
        }
        .agent-profile textarea { width: 100%; margin: 0.5em 0; }
        .slide-menu {
            position: fixed;
            left: -230px;
            top: 0;
            width: 220px;
            height: 100%;
            background-color: rgba(74,13,103,0.9);
            transition: left 0.3s ease;
            padding-top: 60px;
            z-index: 1000;
        }
        .slide-menu.open { left: 0; }
        .slide-menu a { display: block; padding: 10px; color: var(--color-secundario-dorado, #B8860B); text-decoration: none; font-weight: bold; }
        .menu-btn {
            position: fixed;
            left: 10px;
            top: 10px;
            z-index: 1010;
            background-color: var(--color-primario-purpura, #4A0D67);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const menu = document.getElementById('slide-menu');
        document.getElementById('menu-btn').addEventListener('click', function(){
            menu.classList.toggle('open');
        });
    });
    </script>
</head>
<body>
<?php require_once __DIR__ . '/../_header.php'; ?>
<button id="menu-btn" class="menu-btn">☰ Expertos</button>
<nav id="slide-menu" class="slide-menu">
<?php foreach ($agents as $id => $ag): ?>
    <a href="#<?php echo $id; ?>"><?php echo htmlspecialchars($ag['name']); ?></a>
<?php endforeach; ?>
</nav>
<main class="container page-content-block">
    <h1 style="text-align:center;">Foro de Expertos</h1>
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
<?php require_once __DIR__ . '/../_footer.php'; ?>
<script src="/js/layout.js"></script>
</body>
</html>
