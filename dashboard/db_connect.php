<?php
// Database configuration
$db_host = "localhost";
$db_name = "condado_castilla_db";
$db_user = "condado_user";
$db_pass = "TU_CONTRASEÑA_REAL_AQUI"; // IMPORTANT: Replace with your actual strong password
$db_port = "5432";

// PDO options (can be customized)
$db_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Recommended for error handling
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Fetches associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,              // Use native prepared statements
];

// Construct the DSN (Data Source Name) for PostgreSQL
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";

try {
    // Establish the database connection
    $pdo = new PDO($dsn, $db_user, $db_pass, $db_options);

    // You can uncomment the following line for a quick connection test (remove in production)
    // echo "Connected to PostgreSQL database successfully!";

} catch (PDOException $e) {
    // Handle connection errors
    // In a production environment, log this error to a file or a logging system.
    // Avoid displaying detailed error messages to the end-user.
    error_log("PostgreSQL Connection Error: " . $e->getMessage(), 0);
    
    // Display a generic error message to the user
    // You might want to redirect to an error page or display a more user-friendly message.
    die("ERROR: Could not connect to the database. Please check the server configuration and logs for more details.");
}

// The $pdo object is now ready to be used for database operations.
// For example:
// $stmt = $pdo->query("SELECT * FROM your_table_name LIMIT 10");
// while ($row = $stmt->fetch()) {
//     print_r($row);
// }

// It's good practice to close the connection when it's no longer needed,
// though PHP usually handles this automatically at the end of the script.
// To explicitly close: $pdo = null;
?>
