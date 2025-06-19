<?php
function load_page_css(): void
{
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    $root = dirname(__DIR__);
    $useMin = filter_var($_ENV['USE_MINIFIED_ASSETS'] ?? false, FILTER_VALIDATE_BOOLEAN);

    $sanitized = str_replace('/', '_', ltrim($scriptPath, '/'));
    $sanitized = preg_replace('/\.php$/', '', $sanitized);

    $bases = [
        '/assets/css/pages/' . $sanitized,
        '/assets/css/pages/' . basename($scriptPath, '.php'),
    ];

    foreach ($bases as $base) {
        if ($useMin && file_exists($root . $base . '.min.css')) {
            echo "<link rel=\"stylesheet\" href=\"{$base}.min.css\">\n";
            return;
        }
        if (file_exists($root . $base . '.css')) {
            echo "<link rel=\"stylesheet\" href=\"{$base}.css\">\n";
            return;
        }
    }
}
?>
