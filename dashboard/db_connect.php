<?php
// Configuración de la base de datos
$db_host = "localhost";         // Host de la base de datos
$db_name = "condado_castilla_db"; // Nombre de tu base de datos
$db_user = "condado_user";        // Usuario de tu base de datos
$db_pass = "TU_CONTRASEÑA_REAL_AQUI"; // ¡¡¡REEMPLAZA ESTO CON LA CONTRASEÑA REAL DE 'condado_user'!!!
$db_port = "5432";                // Puerto estándar de PostgreSQL

// Opciones de PDO (pueden ser personalizadas)
$db_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Recomendado para manejo de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Obtiene arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,              // Usa sentencias preparadas nativas
];

// Construir el DSN (Data Source Name) para PostgreSQL
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";

try {
    // Establecer la conexión a la base de datos
    $pdo = new PDO($dsn, $db_user, $db_pass, $db_options);

    // Puedes descomentar la siguiente línea para una prueba rápida de conexión (eliminar en producción)
    // echo "Conectado a la base de datos PostgreSQL exitosamente!";

} catch (PDOException $e) {
    // Manejar errores de conexión
    // En un entorno de producción, registra este error en un archivo o sistema de logging.
    // Evita mostrar mensajes de error detallados al usuario final.
    error_log("Error de Conexión a la Base de Datos PostgreSQL: " . $e->getMessage(), 0);
    
    // Muestra un mensaje de error genérico al usuario
    die("ERROR: No se pudo conectar a la base de datos. Por favor, verifica la configuración.");
}

// El objeto $pdo ahora está listo para ser usado en operaciones de base de datos.
?>
