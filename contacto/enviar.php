<?php
session_start();

// If token requested via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'token') {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    header('Content-Type: text/plain');
    echo $token;
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $asunto  = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $token   = $_POST['csrf_token'] ?? '';

    if (!$nombre || !$email || !$asunto || !$mensaje) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        $error = 'Token CSRF inválido.';
    } else {
        unset($_SESSION['csrf_token']);
        $to      = 'info@condadodecastilla.com';
        $subject = "Formulario de Contacto: $asunto";
        $body    = "Nombre: $nombre\nEmail: $email\n\nMensaje:\n$mensaje";
        $headers = "From: $nombre <$email>\r\n" .
                   "Reply-To: $email\r\n" .
                   "Content-Type: text/plain; charset=UTF-8";

        if (mail($to, $subject, $body, $headers)) {
            $success = true;
        } else {
            $error = 'Hubo un problema al enviar el mensaje. Por favor, inténtelo de nuevo más tarde.';
        }
    }
} else {
    $error = 'Método de solicitud no válido.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Condado de Castilla</title>
    <link rel="icon" href="/assets/img/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
    <style>
        .message-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .success-message {
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            padding: 15px;
            border-radius: 5px;
        }
        .error-message {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #d9534f;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div id="header-placeholder"></div>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--color-primario-purpura-rgb), 0.75), rgba(var(--color-negro-contraste-rgb), 0.88)), url('/assets/img/hero_contacto_background.jpg');">
        <div class="hero-content">
            <img src="/assets/img/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Contacto</h1>
        </div>
    </header>

    <main>
        <section class="section contact-section">
            <div class="container page-content-block">
                <div class="message-container">
                    <?php if ($success): ?>
                        <p class="success-message">Tu mensaje ha sido enviado correctamente.</p>
                    <?php else: ?>
                        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                    <p><a href="/contacto/contacto.html">Volver al formulario</a></p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
            <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/groups/1052427398664069" aria-label="Facebook" title="Síguenos en Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="/en_construccion.html" aria-label="Instagram" title="Síguenos en Instagram"><i class="fab fa-instagram"></i></a>
                <a href="/en_construccion.html" aria-label="Twitter" title="Síguenos en Twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="/js/layout.js"></script>
</body>
</html>
