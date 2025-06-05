<?php
header('Content-Type: application/json');
require_once 'db_connect.php'; // Establece la conexión $pdo

$response = ['success' => false, 'message' => 'Error desconocido.', 'data' => []];

if (!isset($pdo) || !$pdo instanceof PDO) {
    $response['message'] = "La conexión a la base de datos no está disponible. Revisa la configuración.";
    error_log("get_stats.php: el objeto PDO no está disponible desde db_connect.php.");
    http_response_code(500); // Internal Server Error
    echo json_encode($response);
    exit;
}

try {
    // Estadísticas de ejemplo: Conteo de piezas del museo y fotos de la galería
    // En una aplicación real, tendrías una tabla 'visit_stats' o similar.
    // Aquí adaptamos para contar los ítems que ya tienes.

    $stats_data = [];

    // Contar piezas del museo
    $stmt_museo = $pdo->query("SELECT COUNT(*) as total_items FROM piezas_museo");
    $count_museo = $stmt_museo->fetchColumn();
    if ($count_museo !== false) {
        $stats_data[] = ["section_name" => "Piezas del Museo", "total_visits" => (int)$count_museo];
    } else {
        $stats_data[] = ["section_name" => "Piezas del Museo", "total_visits" => 0];
        error_log("get_stats.php: No se pudo obtener el conteo de piezas_museo.");
    }

    // Contar fotos de la galería
    $stmt_galeria = $pdo->query("SELECT COUNT(*) as total_items FROM fotos_galeria");
    $count_galeria = $stmt_galeria->fetchColumn();
    if ($count_galeria !== false) {
        $stats_data[] = ["section_name" => "Fotos de Galería", "total_visits" => (int)$count_galeria];
    } else {
        $stats_data[] = ["section_name" => "Fotos de Galería", "total_visits" => 0];
        error_log("get_stats.php: No se pudo obtener el conteo de fotos_galeria.");
    }
    
    // Podrías añadir más "secciones" si tienes otras tablas o formas de medir visitas
    // Ejemplo de datos estáticos si tus tablas están vacías:
    if (empty($stats_data) || ($count_museo == 0 && $count_galeria == 0)) {
        // $stats_data[] = ["section_name" => "Página de Inicio", "total_visits" => 150]; // Dato de ejemplo
        // $stats_data[] = ["section_name" => "Historia", "total_visits" => 75];      // Dato de ejemplo
    }


    $response['success'] = true;
    $response['data'] = $stats_data;
    
    if (empty($stats_data)) {
        $response['message'] = "No hay estadísticas para mostrar todavía.";
    } else {
        $response['message'] = "Estadísticas cargadas correctamente.";
    }
    
} catch (PDOException $e) {
    error_log("Error de Consulta a BD en get_stats.php: " . $e->getMessage(), 0);
    $response['message'] = "Error al obtener estadísticas de la base de datos.";
    http_response_code(500); 
} catch (Exception $e) {
    error_log("Error General en get_stats.php: " . $e->getMessage(), 0);
    $response['message'] = "Ocurrió un error inesperado al procesar las estadísticas.";
    http_response_code(500);
}

$pdo = null; // Cerrar conexión
echo json_encode($response);
?>
