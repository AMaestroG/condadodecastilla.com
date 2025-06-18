<?php
// It's crucial to start the session before any output and before including auth.php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/db_connect.php';
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
    return;
}

$error_message = '';
$login_success = false;
$redirect_to = '';

// If already logged in as admin, redirect to homepage or admin panel
if (is_admin_logged_in()) {
    if (!headers_sent()) {
        header("Location: /index.php"); // Or an admin dashboard if one exists
    }
    if (empty($GLOBALS['TESTING'])) {
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'CSRF token inválido.';
    } else {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = "Por favor, ingrese usuario y contraseña.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['role'] === ADMIN_ROLE) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];

                    // Determine redirect URL
                    $redirect_to = '/dashboard/index.php'; // Default redirect to dashboard
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect_to = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']); // Clear it after use
                    }

                    $login_success = true;
                    if (!headers_sent()) {
                         // Redirect to a meaningful page, e.g., the main site index or an admin panel
                        // Ensure $redirect_to is not inadvertently /index.html if that was the stored session value
                        if ($redirect_to === '/index.html') {
                            $redirect_to = '/index.php';
                        } elseif ($redirect_to === '/dashboard/index.html') {
                            $redirect_to = '/dashboard/index.php';
                        }
                        header("Location: " . $redirect_to);
                        if (empty($GLOBALS['TESTING'])) {
                            exit;
                        }
                    }
                } else {
                    $error_message = "El usuario no tiene permisos de administrador.";
                }
            } else {
                $error_message = "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            error_log("Login PDOException: " . $e->getMessage());
            $error_message = "Error al conectar con la base de datos. Inténtelo más tarde.";
            // In a development environment, you might want to display more details
            // $error_message = "Database Error: " . $e->getMessage();
        }
    }
}

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login - Administración</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
</head>
<body class="admin-page centered">
    <div class="admin-container narrow login-container">
        <h1>Acceso de Administrador</h1>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($login_success): // Should not be visible if redirect works ?>
            <p class="success-message">Login exitoso. Redirigiendo...</p>
            <script>
                // Fallback redirect if header() failed
                setTimeout(function() { window.location.href = '<?php echo $redirect_to; ?>'; }, 1000);
            </script>
        <?php endif; ?>

        <form action="login.php" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <div>
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Entrar</button>
        </form>
    </div>
</body>
</html>
