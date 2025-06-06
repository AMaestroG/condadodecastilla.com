<?php
// Start the session to access session variables.
// It's good practice to ensure it's started before trying to manipulate it.
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../includes/auth.php';

logout_user();

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
