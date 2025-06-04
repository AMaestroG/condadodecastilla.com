<?php
/*
 * !!! IMPORTANT SECURITY WARNING !!!
 * This script is designed for one-time use to create the initial admin user.
 * AFTER SUCCESSFULLY RUNNING THIS SCRIPT, YOU MUST DELETE IT FROM YOUR SERVER.
 * Leaving this script on a live server poses a significant security risk,
 * as it could be re-run or exploited to create unwanted admin accounts
 * or reveal database connection details if errors are not handled carefully.
 */

require_once 'dashboard/db_connect.php'; // For $pdo database connection
require_once 'includes/auth.php';      // For ADMIN_ROLE constant, though not strictly needed here but good for consistency

// Admin user details
$admin_username = "Rodrigo Tabliega";
$admin_password_plain = "Rudericus"; // Password as requested
$admin_role = ADMIN_ROLE; // Defined in auth.php as 'admin'

// Hash the password securely
$admin_password_hashed = password_hash($admin_password_plain, PASSWORD_DEFAULT);

if ($admin_password_hashed === false) {
    die("Error: Failed to hash the password. Please check your PHP password hashing setup.");
}

echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Crear Admin</title></head><body>";
echo "<h1>Creando Usuario Administrador</h1>";
echo "<p>Intentando crear el usuario administrador...</p>";
echo "<ul>";
echo "<li>Usuario: " . htmlspecialchars($admin_username) . "</li>";
echo "<li>Contraseña (Plana): " . htmlspecialchars($admin_password_plain) . " (Esto es solo para confirmación, se guardará hasheada)</li>";
echo "<li>Rol: " . htmlspecialchars($admin_role) . "</li>";
echo "</ul>";

try {
    // Check if user already exists
    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt_check->bindParam(':username', $admin_username);
    $stmt_check->execute();

    if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {
        echo "<p style='color:orange;'><strong>Advertencia:</strong> El usuario '" . htmlspecialchars($admin_username) . "' ya existe en la base de datos.</p>";
    } else {
        // Insert the new admin user
        $stmt_insert = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)");
        $stmt_insert->bindParam(':username', $admin_username);
        $stmt_insert->bindParam(':password_hash', $admin_password_hashed);
        $stmt_insert->bindParam(':role', $admin_role);

        if ($stmt_insert->execute()) {
            echo "<p style='color:green;'><strong>Éxito:</strong> Usuario administrador '" . htmlspecialchars($admin_username) . "' creado satisfactoriamente.</p>";
            echo "<p style='color:red; font-weight:bold;'>POR FAVOR, ELIMINE ESTE SCRIPT (create_admin.php) DE SU SERVIDOR INMEDIATAMENTE.</p>";
        } else {
            $errorInfo = $stmt_insert->errorInfo();
            echo "<p style='color:red;'><strong>Error:</strong> No se pudo crear el usuario administrador. Código de error: " . htmlspecialchars($errorInfo[0]) . " - " . htmlspecialchars($errorInfo[2]) . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>Error de Base de Datos:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Por favor, verifique su conexión a la base de datos y la configuración en `dashboard/db_connect.php`.</p>";
    echo "<p>Asegúrese de que la tabla `users` exista y tenga la estructura correcta (id, username, password_hash, role).</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error General:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Recordatorio de Seguridad:</strong> Una vez que el usuario administrador haya sido creado y haya verificado que puede iniciar sesión, elimine este archivo (`create_admin.php`) del servidor. Dejar este archivo accesible es un riesgo de seguridad.</p>";
echo "</body></html>";

?>
