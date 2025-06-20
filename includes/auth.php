<?php

// /includes/auth.php

require_once __DIR__ . '/session.php';
ensure_session_started();

define('ADMIN_ROLE', 'admin'); // Define el rol de administrador

function is_admin_logged_in()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === ADMIN_ROLE;
}

function require_admin_login($redirect_url = '/dashboard/login.php')
{
    if (!is_admin_logged_in()) {
        // Guardar la URL a la que se intentaba acceder para redirigir después del login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        if (!headers_sent()) {
            header("Location: " . $redirect_url);
        } else {
            // Fallback si los headers ya se enviaron
            echo "<p>Acceso denegado. Por favor, <a href='" . htmlspecialchars($redirect_url) . "'>inicie sesión como administrador</a>.</p>";
            echo "<script>window.location.href='" . htmlspecialchars($redirect_url) . "';</script>";
        }
        exit;
    }
}

function logout_user()
{
    $_SESSION = array(); // Limpiar todas las variables de sesión

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}
