<?php
if (session_status() == PHP_SESSION_NONE) {
    // Attempt to start the session, suppressing errors if headers already sent (though ideally this should be called before any output)
    @session_start();
}

define('ADMIN_ROLE', 'admin');

/**
 * Checks if a user is logged in and has an admin role.
 *
 * @return bool True if admin is logged in, false otherwise.
 */
function is_admin_logged_in(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === ADMIN_ROLE;
}

/**
 * If the user is not an admin, redirects to the login page and exits.
 * Call this at the beginning of any admin-only page.
 *
 * @param string $login_page_path The path to the login page, e.g., '/login.php' or '../login.php'
 */
function require_admin_login(string $login_page_path = '/login.php'): void {
    if (!is_admin_logged_in()) {
        // Ensure no output has been sent before header()
        if (!headers_sent()) {
            header("Location: " . $login_page_path);
        } else {
            // Fallback if headers are already sent (though this indicates a problem elsewhere)
            echo "<p>Access denied. Please <a href='" . htmlspecialchars($login_page_path) . "'>login</a>.</p>";
            // Optionally log this situation as it's not ideal.
        }
        exit;
    }
}

?>
