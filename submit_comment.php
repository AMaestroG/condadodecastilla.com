<?php
session_start();

// 1. Include a placeholder for database connection
require_once 'db_connect.php';

// 2. Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Retrieve page_url, name, and comment from the $_POST superglobal
    $page_url = $_POST['page_url'] ?? 'index.php'; // Default to index.php if not provided
    $name = $_POST['name'] ?? '';
    $comment = $_POST['comment'] ?? '';

    // 4. Perform basic validation
    if (empty($name) || empty($comment)) {
        $_SESSION['comment_error'] = "Name and comment fields are required.";
        header("Location: " . $page_url);
        exit;
    }

    // 5. Sanitize inputs
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
    $page_url = htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8');


    // Database interaction (placeholders for now)
    // Assume $pdo is available from db_connect.php
    if (isset($pdo)) {
        try {
            // 6. Prepare an SQL INSERT statement
            $sql = "INSERT INTO comments (page_url, name, comment) VALUES (:page_url, :name, :comment)";
            $stmt = $pdo->prepare($sql);

            // 7. Bind the sanitized page_url, name, and comment
            $stmt->bindParam(':page_url', $page_url);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':comment', $comment);

            // 8. Execute the statement
            if ($stmt->execute()) {
                // 9. If the insertion is successful, store a success message
                $_SESSION['comment_success'] = "Comment submitted successfully!";
            } else {
                // 10. If there's an error during insertion, store an error message
                $_SESSION['comment_error'] = "Error submitting comment. Please try again.";
            }
        } catch (PDOException $e) {
            $_SESSION['comment_error'] = "Database error: " . $e->getMessage();
        }
    } else {
        // This else block is for when $pdo is not set (db_connect.php is not yet implemented)
        // For now, we can simulate a successful submission for testing purposes,
        // or indicate that DB connection is missing.
        // $_SESSION['comment_success'] = "Comment submitted (simulated - DB not connected).";
         $_SESSION['comment_error'] = "Database connection not available.";
    }

    // 11. Redirect the user back to the original page_url
    header("Location: " . $page_url);
    exit;

} else {
    // 12. If the request method is not POST, redirect to homepage or an error page
    // For now, redirecting to a generic index.php or a hypothetical error page
    // header("Location: index.php"); 
    // Or, display an error message directly if preferred for non-POST access:
    // echo "Invalid request method.";
    $_SESSION['comment_error'] = "Invalid request method.";
    // It's generally better to redirect to the page where the form was,
    // or a designated error page if the page_url isn't available.
    // Since page_url isn't available here, redirecting to a default.
    header("Location: index.php"); 
    exit;
}
?>
