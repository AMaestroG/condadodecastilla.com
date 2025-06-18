<?php
$scriptPath = $_SERVER['SCRIPT_NAME'];
$root = dirname(__DIR__);

// Allow CSS files that mirror the script path using underscores
$sanitized = str_replace('/', '_', ltrim($scriptPath, '/'));
$sanitized = preg_replace('/\.php$/', '', $sanitized);
$cssFile = '/assets/css/pages/' . $sanitized . '.css';

if (!file_exists($root . $cssFile)) {
    // Fallback to using only the basename for backwards compatibility
    $cssFile = '/assets/css/pages/' . basename($scriptPath, '.php') . '.css';
}

if (file_exists($root . $cssFile)) {
    echo "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
}
?>
