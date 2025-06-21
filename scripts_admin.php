<?php
require_once __DIR__ . '/includes/session.php';
ensure_session_started();
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

// Gather list of scripts in scripts/ directory
$scriptsDir = __DIR__ . '/scripts';
$scripts = [];
if (is_dir($scriptsDir)) {
    $entries = scandir($scriptsDir);
    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        $path = $scriptsDir . '/' . $entry;
        if (is_file($path)) {
            $scripts[] = $entry;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/includes/head_common.php'; ?>
    <title>Administrar Scripts</title>
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/fragments/header.php'; ?>
    <main class="container-epic">
        <h1 class="gradient-text">Gestión de Scripts</h1>
        <p style="color: var(--epic-purple-emperor);">Lista de scripts disponibles en el sistema.</p>
        <?php if ($scripts): ?>
            <ul>
                <?php foreach ($scripts as $script): ?>
                    <li>
                        <a class="cta-button" href="scripts/<?php echo urlencode($script); ?>" download><?php echo htmlspecialchars($script); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No se encontraron scripts.</p>
        <?php endif; ?>
        <p><a href="/dashboard/index.php" class="cta-button">Volver al Panel</a></p>
    </main>
    <?php require_once __DIR__ . '/fragments/footer.php'; ?>
</body>
</html>
