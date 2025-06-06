<?php
/**
 * Simple tests for includes/text_manager.php
 *
 * Run with: php dashboard/test_text_manager.php
 */

require_once __DIR__ . '/../includes/text_manager.php';

function assertEqual($expected, $actual, $message)
{
    if ($expected === $actual) {
        echo "[PASS] $message\n";
        return true;
    } else {
        echo "[FAIL] $message\n";
        echo "  Expected: $expected\n";
        echo "  Got: $actual\n";
        return false;
    }
}

$tests = 0;
$passed = 0;

// Create a PDO connection to an in-memory SQLite DB with no tables to simulate
// an unavailable database/table.
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Test: getTextContentFromDB should return the default text when the query fails
$tests++;
$defaultText = 'Default text';
$result = getTextContentFromDB('nonexistent', $pdo, $defaultText);
if (assertEqual($defaultText, $result, 'getTextContentFromDB returns default when DB unavailable')) {
    $passed++;
}

// Test: editableText should output the expected HTML string
$tests++;
ob_start();
editableText('sample_id', $pdo, 'Hello', 'span', '', false);
$output = ob_get_clean();
$expected = '<span data-text-id="sample_id">Hello</span>';
if (assertEqual($expected, $output, 'editableText outputs correct HTML')) {
    $passed++;
}

echo "\n$passed/$tests tests passed\n";

// Exit with error code if any test failed
exit($passed === $tests ? 0 : 1);
?>
