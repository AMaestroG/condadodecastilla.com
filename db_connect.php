<?php
/*
 * Database Connection Settings
 *
 * Please update the following variables with your database credentials.
 */
$db_host = 'YOUR_DATABASE_HOST';        // e.g., 'localhost' or '127.0.0.1'
$db_name = 'YOUR_DATABASE_NAME';        // The name of your database
$db_user = 'YOUR_DATABASE_USER';        // Your database username
$db_pass = 'YOUR_DATABASE_PASSWORD';    // Your database password
$db_charset = 'utf8mb4';                // Character set (optional, utf8mb4 is recommended for MySQL)

/*
 * Attempt to connect to the database using PDO.
 */
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Turn on errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Make the default fetch be an associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Turn off emulation mode for real prepared statements
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // For a production environment, you might want to log this error and display a generic message.
    // For development, you can display the actual error.
    error_log("Database Connection Error: " . $e->getMessage());
    // Display a user-friendly message or die with an error.
    // Make sure this doesn't expose sensitive information in a production environment.
    die("Sorry, there was an error connecting to the database. Please try again later.");
    // Or, for debugging during development:
    // throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// The $pdo object is now ready to be used by other scripts.
?>
