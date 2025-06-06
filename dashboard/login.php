<?php
// It's crucial to start the session before any output and before including auth.php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/db_connect.php'; // Assumes db_connect.php is in the dashboard directory

$error_message = '';
$login_success = false;

// If already logged in as admin, redirect to homepage or admin panel
if (is_admin_logged_in()) {
    if (!headers_sent()) {
        header("Location: /index.php"); // Or an admin dashboard if one exists
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                    $redirect_to = '/index.html'; // Default redirect
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
                        }
                        header("Location: " . $redirect_to);
                        exit;
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Administración</title>
    <!-- Link to existing stylesheet, assuming one exists in assets/css/ -->
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5; /* Similar to dashboard */
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #333; /* Primary button color from dashboard example */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #555;
        }
        .error-message {
            color: #d9534f; /* Danger color */
            background-color: #f2dede;
            border: 1px solid #d9534f;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: left;
        }
        .success-message { /* For displaying post-login message if redirect fails */
            color: #3c763d;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
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
            <div>
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
