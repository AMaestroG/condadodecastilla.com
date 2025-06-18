<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin_login();

$agents = [
    'historian' => 'Alicia la Historiadora',
    'archaeologist' => 'Bruno el Arqueólogo',
    'guide' => 'Clara la Guía Turística',
    'manager' => 'Diego el Gestor Cultural',
    'technologist' => 'Elena la Tecnóloga',
];

$feedback = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $feedback = 'CSRF token inválido.';
        $feedback_type = 'error';
    } else {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM forum_comments WHERE id = :id');
            if ($stmt->execute([':id' => $id])) {
                $feedback = 'Comentario eliminado correctamente.';
                $feedback_type = 'success';
            } else {
                $feedback = 'Error al eliminar el comentario.';
                $feedback_type = 'error';
            }
        }
    }
}

$comments = [];
if ($pdo) {
    $stmt = $pdo->query('SELECT id, agent, comment, created_at FROM forum_comments ORDER BY created_at DESC');
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Administrar Comentarios</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <style>
        body { background-color: #f4f4f4; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        .feedback { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .feedback.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .feedback.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Administrar Comentarios del Foro</h1>
    <nav>
        <a href="../index.php">Inicio</a>
        <a href="index.php">Volver al Foro</a>
        <a href="../dashboard/logout.php">Cerrar sesión</a>
    </nav>
    <?php if ($feedback): ?>
        <div class="feedback <?php echo htmlspecialchars($feedback_type); ?>"><?php echo htmlspecialchars($feedback); ?></div>
    <?php endif; ?>
    <?php if (empty($comments)): ?>
        <p>No hay comentarios para mostrar.</p>
    <?php else: ?>
        <table>
            <tr><th>Agente</th><th>Comentario</th><th>Fecha</th><th>Acciones</th></tr>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($agents[$c['agent']] ?? $c['agent']); ?></td>
                    <td><?php echo htmlspecialchars($c['comment']); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($c['created_at']))); ?></td>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirm('¿Eliminar este comentario?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
