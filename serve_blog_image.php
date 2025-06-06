<?php
require_once __DIR__ . '/dashboard/db_connect.php';

$filename = basename($_GET['file'] ?? '');
$path = __DIR__ . '/uploads_storage/blog_photos/' . $filename;
if (!preg_match('/^[A-Za-z0-9_.-]+$/', $filename) || !is_file($path)) {
    http_response_code(404);
    exit('Imagen no encontrada');
}
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$type = finfo_file($finfo, $path);
finfo_close($finfo);
header('Content-Type: ' . $type);
readfile($path);
