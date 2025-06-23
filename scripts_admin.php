<?php
require_once __DIR__ . '/includes/session.php';
ensure_session_started();
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

// Gather list of scripts in scripts/ directory
$scriptsDir = __DIR__ . '/scripts';
$scripts = [];
$output = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['script'])) {
    $selected = basename($_POST['script']);
    $pathToRun = $scriptsDir . '/' . $selected;
    if (is_file($pathToRun)) {
        $output = shell_exec(escapeshellcmd($pathToRun) . ' 2>&1');
    } else {
        $output = 'Script no encontrado.';
    }
}
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
    <link rel="stylesheet" href="/assets/css/admin_theme.css">
    <title>Administrar Scripts</title>
</head>
<body class="alabaster-bg admin-page">
    <?php require_once __DIR__ . '/fragments/admin_header.php'; ?>
    <main class="container-epic">
        <h1 class="gradient-text">Gestión de Scripts</h1>
        <section class="help-link">
            <p>Antes de ejecutar, revisa el <a href="/docs/script_catalog.md">catálogo de scripts</a> para conocer la función de cada uno.</p>
        </section>
        <p style="color: var(--epic-purple-emperor);">Lista de scripts disponibles en el sistema.</p>
        <?php if ($scripts): ?>
            <ul>
                <?php foreach ($scripts as $script): ?>
                    <li>
                        <span class="script-name"><?php echo htmlspecialchars($script); ?></span>
                        <form method="POST" style="display:inline;margin-left:0.5rem;">
                            <input type="hidden" name="script" value="<?php echo htmlspecialchars($script); ?>">
                            <button type="submit" class="cta-button">Ejecutar</button>
                        </form>
                        <a class="cta-button" href="scripts/<?php echo urlencode($script); ?>" download>Descargar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No se encontraron scripts.</p>
        <?php endif; ?>
        <?php if ($output): ?>
            <h2 class="gradient-text">Resultado de la Ejecución</h2>
            <pre class="code-output"><?php echo htmlspecialchars($output); ?></pre>
        <?php endif; ?>
        <p><a href="/dashboard/index.php" class="cta-button">Volver al Panel</a></p>
    </main>
    <?php require_once __DIR__ . '/fragments/footer.php'; ?>
</body>
</html>
