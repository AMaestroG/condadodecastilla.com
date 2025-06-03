<?php
header('Content-Type: application/json');

$targetDir = "../uploads/museo_piezas/";
$photosData = [];

if (!is_dir($targetDir)) {
    // Directory doesn't exist, which might be normal if no uploads yet.
    // galeria_upload.php is responsible for creating it.
    echo json_encode($photosData); // Return empty array
    exit;
}

$files = scandir($targetDir);
if ($files === false) {
    // Failed to scan directory
    // Optionally log an error or return a specific error response
    echo json_encode(["error" => "Failed to scan directory."]);
    exit;
}

foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $filePath = $targetDir . $file;
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExtension, $allowedImageExtensions)) {
        $metadataFilename = pathinfo($filePath, PATHINFO_FILENAME) . '.json';
        $metadataFilePath = $targetDir . $metadataFilename;

        if (file_exists($metadataFilePath)) {
            $metadataJson = file_get_contents($metadataFilePath);
            if ($metadataJson === false) {
                // Optionally log error: Failed to read metadata file
                continue; // Skip this image
            }
            $metadata = json_decode($metadataJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Optionally log error: Invalid JSON in metadata file
                continue; // Skip this image
            }

            // Ensure imagenUrl is correct (it should be from the upload script)
            // Add an 'id' if not present, using the filename without extension
            if (!isset($metadata['id'])) {
                $metadata['id'] = pathinfo($file, PATHINFO_FILENAME);
            }

            // Ensure essential fields are present, provide defaults if necessary
            $metadata['titulo'] = $metadata['titulo'] ?? 'Sin título';
            $metadata['descripcion'] = $metadata['descripcion'] ?? 'Sin descripción';
            $metadata['autor'] = $metadata['autor'] ?? 'Anónimo';
            $metadata['imagenUrl'] = $metadata['imagenUrl'] ?? '/uploads/museo_piezas/' . $file;


            $photosData[] = $metadata;
        }
        // If metadata file doesn't exist, we skip this image as per instructions.
    }
}

echo json_encode($photosData);
?>
