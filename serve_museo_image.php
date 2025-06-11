<?php
require_once __DIR__ . '/includes/csrf.php';
// Use same upload dir definition as API, configurable via env var
$museoUpload = getenv('MUSEO_UPLOAD_DIR');
if (!$museoUpload) {
    $museoUpload = dirname(__DIR__) . '/uploads_storage/museo_piezas';
}
$museoUpload = rtrim($museoUpload, '/') . '/';
define('UPLOAD_DIR_BASE', $museoUpload);

$filename = basename($_GET['file'] ?? '');
$path = UPLOAD_DIR_BASE . $filename;
if ($filename === '' || !preg_match('/^[a-zA-Z0-9_.-]+$/', $filename)) {
    http_response_code(400);
    echo 'Invalid file.';
    exit;
}
if (!file_exists($path)) {
    http_response_code(404);
    echo 'File not found';
    exit;
}
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
?>
