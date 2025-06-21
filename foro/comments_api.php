<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
$agents = require __DIR__ . '/../config/forum_agents.php';
header('Content-Type: application/json');

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'DB unavailable']);
    exit;
}

function fetchComments(string $agent, PDO $pdo): array {
    $stmt = $pdo->prepare('SELECT comment, created_at FROM forum_comments WHERE agent = :agent ORDER BY created_at DESC');
    $stmt->execute([':agent' => $agent]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $agent = $_GET['agent'] ?? null;
    if ($agent) {
        if (!isset($agents[$agent])) {
            http_response_code(400);
            echo json_encode(['error' => 'invalid agent']);
            exit;
        }
        $comments = fetchComments($agent, $pdo);
        echo json_encode([
            'agents' => [$agent => $agents[$agent]],
            'comments' => [$agent => $comments],
            'csrf' => get_csrf_token(),
        ]);
    } else {
        $data = ['agents' => $agents, 'comments' => [], 'csrf' => get_csrf_token()];
        foreach ($agents as $id => $_ag) {
            $data['comments'][$id] = fetchComments($id, $pdo);
        }
        echo json_encode($data);
    }
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $agent = $input['agent'] ?? '';
    $comment = trim($input['comment'] ?? '');
    $token = $input['csrf_token'] ?? '';
    $maxLength = 500;
    $rateLimit = FORUM_COMMENT_COOLDOWN;

    if (!verify_csrf_token($token)) {
        http_response_code(400);
        echo json_encode(['error' => 'bad csrf']);
        exit;
    }
    if (strlen($comment) > $maxLength || !$comment || !isset($agents[$agent])) {
        http_response_code(400);
        echo json_encode(['error' => 'invalid data']);
        exit;
    }
    if (isset($_SESSION['last_comment_time']) && (time() - $_SESSION['last_comment_time']) < $rateLimit) {
        http_response_code(429);
        echo json_encode(['error' => 'rate limit']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO forum_comments (agent, comment) VALUES (:agent, :comment)');
    $stmt->execute([':agent' => $agent, ':comment' => $comment]);
    $_SESSION['last_comment_time'] = time();
    $created_at = $pdo->query('SELECT created_at FROM forum_comments WHERE id = LASTVAL()')->fetchColumn();
    echo json_encode(['comment' => $comment, 'created_at' => $created_at]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'method not allowed']);
