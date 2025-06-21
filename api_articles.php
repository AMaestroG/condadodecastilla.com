<?php
header('Content-Type: application/json');

$files = glob(__DIR__ . '/contenido/blog/*.md');
if (!$files) {
    echo json_encode([]);
    exit;
}

usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

$articles = [];
foreach (array_slice($files, 0, 3) as $file) {
    $lines = file($file);
    if (!$lines) continue;
    $title = trim(ltrim($lines[0], "# \t"));
    $slug = basename($file, '.md');
    $articles[] = [
        'title' => $title,
        'url' => '/blog.php?post=' . urlencode($slug)
    ];
}

echo json_encode($articles);

