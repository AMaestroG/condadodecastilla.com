<?php
require_once __DIR__ . '/env_loader.php';
use League\CommonMark\CommonMarkConverter;

function markdown_get_converter(): CommonMarkConverter {
    static $converter = null;
    if ($converter === null) {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip'
        ]);
    }
    return $converter;
}

function render_markdown(string $markdown): string {
    return markdown_get_converter()->convert($markdown)->getContent();
}

function render_markdown_file(string $path): string {
    if (!is_readable($path)) {
        return '';
    }
    return render_markdown(file_get_contents($path));
}
