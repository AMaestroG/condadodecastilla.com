<?php
$script = $_SERVER['SCRIPT_NAME'];
$root = dirname(__DIR__);

// Determine CSS file based on script path
$base = basename($script, '.php');
$dir  = trim(dirname($script), '/');

if ($base === 'index' && $dir !== '') {
    $cssFile = "/assets/css/pages/{$dir}.css";
} else {
    $cssFile = "/assets/css/pages/{$base}.css";
}

if (file_exists($root . $cssFile)) {
    echo "<link rel=\"stylesheet\" href=\"{$cssFile}\">\n";
}
?>
