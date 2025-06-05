<?php
// Start the session to access session variables.
// It's good practice to ensure it's started before trying to manipulate it.
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// Unset all session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the login page or homepage.
// Redirecting to login.php is a common choice after logout.
if (!headers_sent()) {
    header("Location: login.php");
} else {
    // Fallback if headers are already sent
    echo "<p>Logout successful. Please <a href='login.php'>click here to login again</a>.</p>";
    // JavaScript redirect as a further fallback
    echo "<script>window.location.href = 'login.php';</script>";
}
exit;
?>
