<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

require_admin_login();

$scripts = [
    'compress_images' => [
        'label' => 'Comprimir Imágenes',
        'path' => __DIR__ . '/../scripts/compress_images.sh'
    ],
    'accessibility_audit' => [
        'label' => 'Auditoría de Accesibilidad',
        'path' => __DIR__ . '/../scripts/run_accessibility_audit.sh'
    ],
];

$output = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'CSRF token inválido.';
    } else {
        $key = $_POST['script'] ?? '';
        if (isset($scripts[$key])) {
            $cmd = escapeshellcmd($scripts[$key]['path']);
            $output = shell_exec("$cmd 2>&1");
        } else {
            $error_message = 'Script no permitido.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejecutar Scripts</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
</head>
<body class="alabaster-bg admin-page centered">
    <?php require_once __DIR__ . '/../fragments/admin_header.php'; ?>
    <div class="admin-container narrow">
        <h1>Herramientas de Mantenimiento</h1>
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($output): ?>
            <pre><?php echo htmlspecialchars($output); ?></pre>
        <?php endif; ?>
        <?php foreach ($scripts as $key => $info): ?>
            <form action="scripts_admin.php" method="POST" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                <input type="hidden" name="script" value="<?php echo htmlspecialchars($key); ?>">
                <button type="submit" class="btn-primary"><?php echo htmlspecialchars($info['label']); ?></button>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>
