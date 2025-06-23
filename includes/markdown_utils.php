<?php
// includes/markdown_utils.php
// Shared utility to render markdown files using League/CommonMark

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use League\CommonMark\CommonMarkConverter;

if (!function_exists('render_markdown_file')) {
    /**
     * Render the markdown file at the given path and return HTML.
     */
    function render_markdown_file(string $path): string {
        static $converter = null;
        if ($converter === null) {
            $converter = new CommonMarkConverter();
        }
        $markdown = file_get_contents($path);
        return $converter->convert($markdown)->getContent();
    }
}

?>
