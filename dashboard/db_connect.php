<?php
// dashboard/db_connect.php
// Configuración para conectar a la base de datos PostgreSQL
// La contraseña se obtiene desde la variable de entorno CONDADO_DB_PASSWORD

// --- IMPORTANTE PARA PRODUCCIÓN ---
// Las siguientes líneas habilitan la visualización de errores para desarrollo.
// DEBEN SER COMENTADAS O ELIMINADAS en un entorno de producción para no exponer información sensible.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// --- FIN DE SECCIÓN IMPORTANTE PARA PRODUCCIÓN ---

// Configuración de la base de datos PostgreSQL
$db_host = "localhost";         // Host de la base de datos (PostgreSQL está en el mismo servidor)
$db_name = "condado_castilla_db"; // Nombre de tu base de datos PostgreSQL
$db_user = "condado_user";        // Usuario de tu base de datos PostgreSQL
$db_pass = getenv('CONDADO_DB_PASSWORD'); // Definido vía variable de entorno
if ($db_pass === false) {
    // Generar un error claro si la variable de entorno no existe
    throw new RuntimeException('Environment variable CONDADO_DB_PASSWORD is not set');
}
$db_port = "5432";                // Puerto estándar de PostgreSQL

// Cadena de conexión (DSN) para PostgreSQL usando PDO
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass";

// Opciones de conexión PDO (recomendadas)
$db_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve resultados como arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa preparaciones nativas (más seguro)
];

$pdo = null; // Inicializar $pdo

try {
    // Establecer la conexión a la base de datos
    $pdo = new PDO($dsn);
    
    // Mensaje de éxito (puedes comentarlo o eliminarlo después de probar)
    // echo "<p style='color:green; font-family:monospace;'>Conexión a PostgreSQL ('$db_name') exitosa!</p>";

} catch (PDOException $e) {
    // Manejar errores de conexión
    $error_message = "Error de conexión a la Base de Datos: " . $e->getMessage();
    error_log($error_message, 0); // Loguear el error detallado en el log de errores del servidor
    
    // Mostrar un mensaje de error genérico al usuario en el HTML del dashboard si este script se incluye directamente
    // o dejar que el script que lo incluye maneje el error si $pdo es null.
    // Para el propósito del dashboard, si get_stats.php incluye esto, debería manejar el caso de $pdo nulo.
    // Si index.php lo incluye directamente, podrías mostrar un error aquí.
    // Por ahora, lo dejamos así y get_stats.php deberá verificar $pdo.
    // die("<p style='color:red; font-family:monospace;'>FALLO DE CONEXIÓN: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// El objeto $pdo estará disponible para los scripts que incluyan este archivo.
// Si la conexión falla, $pdo seguirá siendo null o la excepción habrá detenido el script si no se maneja.
?>
