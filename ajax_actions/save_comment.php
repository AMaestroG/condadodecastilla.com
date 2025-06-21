<?php
require_once __DIR__ . '/../includes/db_connect.php';

$agent = $argv[1] ?? '';
$comment = $argv[2] ?? '';
$agent = is_string($agent) ? trim($agent) : '';
$comment = is_string($comment) ? trim($comment) : '';

if ($agent === '' || $comment === '') {
    fwrite(STDERR, "Missing agent or comment\n");
    exit(1);
}

if (!$pdo) {
    fwrite(STDERR, "Database connection not available\n");
    exit(1);
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS forum_comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        agent VARCHAR(50) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $stmt = $pdo->prepare('INSERT INTO forum_comments (agent, comment) VALUES (:agent, :comment)');
    $stmt->execute([':agent' => $agent, ':comment' => $comment]);
    echo "success";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage());
    exit(1);
}
?>
