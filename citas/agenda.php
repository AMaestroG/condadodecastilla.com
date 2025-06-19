<?php
require_once __DIR__ . '/../includes/csrf.php';
$appointmentsFile = __DIR__ . '/../datos/citas.json';
if (!file_exists($appointmentsFile)) {
    file_put_contents($appointmentsFile, json_encode([]));
}
$successMessage = "";
$errorMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Error de verificación CSRF.';
    } else {
        $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $comentarios = trim($_POST['comentarios'] ?? '');
    if ($nombre && $email && $fecha && $hora) {
        $appointments = json_decode(file_get_contents($appointmentsFile), true);
        if (!is_array($appointments)) {
            $appointments = [];
        }
        $appointments[] = [
            'nombre' => $nombre,
            'email' => $email,
            'fecha' => $fecha,
            'hora' => $hora,
            'comentarios' => $comentarios,
            'timestamp' => date('c')
        ];
        if (file_put_contents($appointmentsFile, json_encode($appointments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $successMessage = "Cita registrada correctamente.";
        } else {
            $errorMessage = "No se pudo guardar la cita.";
        }
    } else {
        $errorMessage = "Por favor completa todos los campos obligatorios.";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Programa de Citas para Visitas</title>
</head>
<body class="alabaster-bg">
    <?php require_once __DIR__ . '/../_header.php'; ?>
    <h1>Programa de Citas para Visitas</h1>
    <?php if ($successMessage): ?>
        <p class="notice-success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php elseif ($errorMessage): ?>
        <p class="notice-error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
        <label>Nombre:<br><input type="text" name="nombre" required></label><br>
        <label>Email:<br><input type="email" name="email" required></label><br>
        <label>Fecha:<br><input type="date" name="fecha" required></label><br>
        <label>Hora:<br><input type="time" name="hora" required></label><br>
        <label>Comentarios:<br><textarea name="comentarios" rows="4" cols="40"></textarea></label><br>
        <button type="submit">Reservar</button>
    </form>
    <?php
    $appointments = json_decode(file_get_contents($appointmentsFile), true);
    if ($appointments && is_array($appointments)) {
        echo "<h2>Citas registradas</h2><ul>";
        foreach ($appointments as $a) {
            echo "<li>" . htmlspecialchars($a['fecha']) . " " . htmlspecialchars($a['hora']) . " - " . htmlspecialchars($a['nombre']) . " (" . htmlspecialchars($a['email']) . ")" . "</li>";
        }
        echo "</ul>";
    }
    ?>
    <?php require_once __DIR__ . '/../_footer.php'; ?>
</body>
</html>
