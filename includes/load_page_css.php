<?php
$cssFile = '/assets/css/pages/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '.css';
$root = dirname(__DIR__);

// Ensure consolidated navigation styles are loaded
echo "<link rel=\"stylesheet\" href=\"/assets/css/header.css\">\n";

if (file_exists($root . $cssFile)) {
    echo "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
}
?>
