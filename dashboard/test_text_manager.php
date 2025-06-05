<?php
echo "Before including text_manager.php\n";
require_once __DIR__ . '/../includes/text_manager.php';
echo "After including text_manager.php\n";

// We still need $pdo for getText, db_connect will be included by text_manager.php if we modify it,
// or we can include it here. For now, let's assume $pdo might be null.
// The db_connect.php script is already modified to set $pdo to null on error.
require_once __DIR__ . '/db_connect.php';


if (function_exists('getText')) {
    echo "getText function IS available. Attempting to call...\n";
    // Attempt to call getText directly
    // This will likely fail if $pdo is null and getText doesn't handle it (it doesn't currently)
    // but the point is to see if the declaration itself causes an error.
    // getText('test_id_gt', $pdo, 'Default for getText');
    // Let's avoid calling it if $pdo is null, to prevent other errors.
    if ($pdo) {
        getText('test_id_gt', $pdo, 'Default for getText');
        echo "getText call attempted.\n";
    } else {
        echo "Skipping getText call as \$pdo is null.\n";
    }
} else {
    echo "getText function is NOT available.\n";
}

echo "Test script finished.\n";
?>
