<?php
require_once __DIR__ . '/../includes/env_loader.php';
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

$feedback_message = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['asunto'] ?? '');
    $message = trim($_POST['mensaje'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $feedback_message = 'Por favor completa todos los campos.';
        $feedback_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = 'Correo electrónico inválido.';
        $feedback_type = 'error';
    } else {
        $to = getenv('CONTACT_EMAIL') ?: 'info@condadodecastilla.com';
        $body = "Nombre: $name\nCorreo: $email\n\nMensaje:\n$message";
        $headers = 'From: ' . $email;
        $ok = true;
        if (empty($GLOBALS['TESTING'])) {
            $ok = mail($to, $subject, $body, $headers);
        }
        if ($ok) {
            $feedback_message = 'Mensaje enviado correctamente.';
            $feedback_type = 'success';
        } else {
            $feedback_message = 'Error al enviar el mensaje.';
            $feedback_type = 'error';
        }
    }
} else {
    $feedback_message = 'Método no permitido.';
    $feedback_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <meta charset="UTF-8">
    <title>Envío de Contacto</title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
<div class="container-epic">
    <p class="feedback <?php echo htmlspecialchars($feedback_type); ?>"><?php echo htmlspecialchars($feedback_message); ?></p>
    <p><a href="/contacto/contacto.php">Volver al formulario</a></p>
</div>
</body>
</html>
