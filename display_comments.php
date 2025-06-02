<?php
// 1. Include a placeholder for database connection
// require_once 'db_connect.php'; // Will be uncommented when db_connect.php is ready

// 2. Assume $current_page_url is available in the scope where this script is included.
// For testing purposes, if it's not set, we can provide a default or show an error.
if (!isset($current_page_url)) {
    // echo "<p>Error: \$current_page_url is not defined. Cannot display comments.</p>";
    // For now, let's use a dummy URL for structure display if not provided
    $current_page_url = 'test_page.php'; 
    // In a real scenario, this script might not be included or would error out
    // if $current_page_url was essential and not set.
}

// Sanitize the $current_page_url before using it in a query (even if it's assumed to be safe)
$safe_page_url = htmlspecialchars($current_page_url, ENT_QUOTES, 'UTF-8');

echo "<h3>Comments</h3>";

// Database interaction (placeholders for now)
// Assume $pdo is available from db_connect.php
if (isset($pdo)) {
    try {
        // 3. Prepare an SQL SELECT statement
        $sql = "SELECT name, comment, created_at FROM comments WHERE page_url = :page_url ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);

        // Bind the $safe_page_url
        $stmt->bindParam(':page_url', $safe_page_url);

        // 4. Execute the prepared statement
        $stmt->execute();

        // 5. Fetch all comments
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. If comments are found, iterate and display
        if ($comments && count($comments) > 0) {
            echo '<div class="comments-list">';
            foreach ($comments as $comment) {
                echo '<div class="comment-item" style="border: 1px solid #eee; margin-bottom: 10px; padding: 10px;">';
                echo '<strong>Name:</strong> ' . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . '<br>';
                echo '<small>Posted on: ' . htmlspecialchars(date('Y-m-d H:i:s', strtotime($comment['created_at'])), ENT_QUOTES, 'UTF-8') . '</small>';
                echo '<p style="margin-top: 5px;">' . nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8')) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            // 7. If no comments are found
            echo "<p>No comments yet. Be the first to comment!</p>";
        }
    } catch (PDOException $e) {
        // 8. If there's an error fetching comments
        echo "<p>Error fetching comments: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
} else {
    // This else block is for when $pdo is not set (db_connect.php is not yet implemented)
    // Display placeholder content or a message indicating DB is not connected.
    echo "<p><em>Comments display is pending database connection.</em></p>";
    // You could also show some dummy comments for layout purposes:
    /*
    echo '<div class="comments-list">';
    echo '<div class="comment-item" style="border: 1px solid #eee; margin-bottom: 10px; padding: 10px;">';
    echo '<strong>Name:</strong> John Doe (Sample)<br>';
    echo '<small>Posted on: 2023-01-01 10:00:00</small>';
    echo '<p style="margin-top: 5px;">This is a sample comment to show the structure.<br>Line breaks are preserved.</p>';
    echo '</div>';
    echo '<div class="comment-item" style="border: 1px solid #eee; margin-bottom: 10px; padding: 10px;">';
    echo '<strong>Name:</strong> Jane Smith (Sample)<br>';
    echo '<small>Posted on: 2023-01-01 09:30:00</small>';
    echo '<p style="margin-top: 5px;">Another example comment here.</p>';
    echo '</div>';
    echo '</div>';
    */
     echo "<p>No comments yet. Be the first to comment! (DB not connected)</p>";
}

?>
