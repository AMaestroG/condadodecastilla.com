<?php
header('Content-Type: application/json');
$file = __DIR__ . '/data/upcoming_visits.json';
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$contents = file_get_contents($file);
if ($contents === false) {
    echo json_encode([]);
    exit;
}

echo $contents;

