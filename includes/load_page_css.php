<?php
$cssFile = '/assets/css/pages/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '.css';
$root = dirname(__DIR__);
if (file_exists($root . $cssFile)) {
    echo "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
}
?>
