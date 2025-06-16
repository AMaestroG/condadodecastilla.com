<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../dashboard/db_connect.php';
/** @var PDO $pdo */
require_once __DIR__ . '/../includes/csrf.php';

require_admin_login();

$feedback_message = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $feedback_message = 'CSRF token inválido.';
        $feedback_type = 'error';
    } else {
        $id = intval($_POST['id'] ?? 0);
        $pos_x = ($_POST['pos_x'] !== '') ? (float)$_POST['pos_x'] : null;
        $pos_y = ($_POST['pos_y'] !== '') ? (float)$_POST['pos_y'] : null;
        $pos_z = ($_POST['pos_z'] !== '') ? (float)$_POST['pos_z'] : null;
        $escala = ($_POST['escala'] !== '') ? (float)$_POST['escala'] : null;
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare('UPDATE museo_piezas SET pos_x=:x, pos_y=:y, pos_z=:z, escala=:e WHERE id=:id');
                $stmt->bindValue(':x', $pos_x, PDO::PARAM_STR);
                $stmt->bindValue(':y', $pos_y, PDO::PARAM_STR);
                $stmt->bindValue(':z', $pos_z, PDO::PARAM_STR);
                $stmt->bindValue(':e', $escala, PDO::PARAM_STR);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $feedback_message = 'Pieza actualizada correctamente.';
                $feedback_type = 'success';
            } catch (PDOException $e) {
                $feedback_message = 'Error al actualizar la pieza.';
                $feedback_type = 'error';
            }
        }
    }
}

$piezas = [];
try {
    $stmt = $pdo->query('SELECT id, titulo, pos_x, pos_y, pos_z, escala FROM museo_piezas ORDER BY id DESC');
    $piezas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedback_message = 'Error al cargar piezas: ' . $e->getMessage();
    $feedback_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Piezas del Museo</title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <link rel="stylesheet" href="/assets/css/admin_overrides.css">
</head>
<body class="admin-body">
<nav>
    <a href="../index.php">Inicio</a>
    <a href="../dashboard/index.php">Dashboard</a>
    <a href="../dashboard/logout.php">Cerrar sesión</a>
</nav>
<h1>Editar Posición y Escala de Piezas</h1>
<?php if ($feedback_message): ?>
    <div class="feedback <?php echo htmlspecialchars($feedback_type); ?>">
        <?php echo htmlspecialchars($feedback_message); ?>
    </div>
<?php endif; ?>
<table class="admin-table">
    <tr><th>ID</th><th>Título</th><th>Pos X</th><th>Pos Y</th><th>Pos Z</th><th>Escala</th><th>Acción</th></tr>
<?php foreach ($piezas as $p): ?>
    <tr>
    <form method="post">
        <td><?php echo (int)$p['id']; ?><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>"></td>
        <td><?php echo htmlspecialchars($p['titulo']); ?></td>
        <td><input type="number" step="0.1" name="pos_x" value="<?php echo htmlspecialchars($p['pos_x']); ?>"></td>
        <td><input type="number" step="0.1" name="pos_y" value="<?php echo htmlspecialchars($p['pos_y']); ?>"></td>
        <td><input type="number" step="0.1" name="pos_z" value="<?php echo htmlspecialchars($p['pos_z']); ?>"></td>
        <td><input type="number" step="0.05" name="escala" value="<?php echo htmlspecialchars($p['escala']); ?>"></td>
        <td>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <button type="submit">Guardar</button>
        </td>
    </form>
    </tr>
<?php endforeach; ?>
</table>
</body>
</html>
