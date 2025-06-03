<?php
header('Content-Type: application/json');

$targetDir = "../uploads/museo_piezas/";
$response = ["success" => false, "message" => ""];

// Create target directory if it doesn't exist
if (!file_exists($targetDir)) {
    if (!mkdir($targetDir, 0777, true)) {
        $response["message"] = "Error: No se pudo crear el directorio de subida.";
        echo json_encode($response);
        exit;
    }
}

if (!is_writable($targetDir)) {
    $response["message"] = "Error: El directorio de subida no tiene permisos de escritura.";
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photoFile'])) {
        $photoFile = $_FILES['photoFile'];

        // Sanitize inputs
        $photoTitulo = isset($_POST['photoTitulo']) ? htmlspecialchars(strip_tags($_POST['photoTitulo'])) : 'Sin título';
        $photoDescripcion = isset($_POST['photoDescripcion']) ? htmlspecialchars(strip_tags($_POST['photoDescripcion'])) : 'Sin descripción';
        $photoAutor = isset($_POST['photoAutor']) ? htmlspecialchars(strip_tags($_POST['photoAutor'])) : 'Anónimo';
        $originalFilename = $photoFile['name'];

        // File validation
        if ($photoFile['error'] !== UPLOAD_ERR_OK) {
            $response["message"] = "Error en la subida del archivo: " . $photoFile['error'];
            echo json_encode($response);
            exit;
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = mime_content_type($photoFile['tmp_name']);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $response["message"] = "Error: Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF.";
            echo json_encode($response);
            exit;
        }

        $maxFileSize = 2 * 1024 * 1024; // 2MB
        if ($photoFile['size'] > $maxFileSize) {
            $response["message"] = "Error: El archivo excede el tamaño máximo permitido de 2MB.";
            echo json_encode($response);
            exit;
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $uniqueFilename = uniqid() . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $uniqueFilename;
        $webPathToImage = "/uploads/museo_piezas/" . $uniqueFilename; // Relative path from web root

        // Move uploaded file
        if (move_uploaded_file($photoFile['tmp_name'], $targetFilePath)) {
            // Create metadata JSON file
            $metadata = [
                "titulo" => $photoTitulo,
                "descripcion" => $photoDescripcion,
                "autor" => $photoAutor,
                "imagenUrl" => $webPathToImage,
                "originalFilename" => $originalFilename
            ];
            $metadataFilename = $targetDir . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.json';
            if (file_put_contents($metadataFilename, json_encode($metadata, JSON_PRETTY_PRINT))) {
                $response["success"] = true;
                $response["message"] = "Fotografía subida con éxito.";
                $response["filepath"] = $webPathToImage;
            } else {
                // If metadata creation fails, it's not ideal, but the image is uploaded.
                // Optionally, delete the uploaded image if metadata is critical.
                // For now, report success with a warning or handle as partial success.
                $response["success"] = true; // Or false, depending on how critical metadata is
                $response["message"] = "Fotografía subida, pero error al guardar metadatos.";
                $response["filepath"] = $webPathToImage;
                 // unlink($targetFilePath); // Optionally delete image
            }
        } else {
            $response["message"] = "Error: No se pudo mover el archivo subido.";
        }
    } else {
        $response["message"] = "Error: No se recibió ningún archivo.";
    }
} else {
    $response["message"] = "Error: Método de solicitud no válido. Se esperaba POST.";
}

echo json_encode($response);
?>
