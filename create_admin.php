<?php
/*
 * !!! IMPORTANT SECURITY WARNING !!!
 * This script is designed for one-time use to create the initial admin user.
 * AFTER SUCCESSFULLY RUNNING THIS SCRIPT, YOU MUST DELETE IT FROM YOUR SERVER.
 */

// Este script debería estar en el directorio 'dashboard'
require_once __DIR__ . '/db_connect.php'; // Para $pdo
require_once __DIR__ . '/../includes/auth.php'; // Para ADMIN_ROLE

// Admin user details
$admin_username = "Rodrigo"; // Nombre de usuario solicitado
$admin_password_plain = "Rudericus";   // Contraseña solicitada
$admin_role = ADMIN_ROLE;              // Definido en auth.php

// Verificar si $pdo se estableció correctamente en db_connect.php
if (!$pdo) {
    die("<p style='color:red;'><strong>Error Crítico:</strong> No se pudo establecer la conexión a la base de datos. Revisa 'dashboard/db_connect.php' y los logs del servidor.</p></body></html>");
}

// Hash the password securely
$admin_password_hashed = password_hash($admin_password_plain, PASSWORD_DEFAULT);

if ($admin_password_hashed === false) {
    die("Error: Falló el hasheo de la contraseña. Revisa tu configuración de PHP.");
}

echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Crear Admin</title><style>body{font-family:sans-serif;padding:20px;} ul{list-style:none;padding-left:0;}</style></head><body>";
echo "<h1>Creando Usuario Administrador</h1>";
echo "<p>Intentando crear el usuario administrador...</p>";
echo "<ul>";
echo "<li>Usuario: " . htmlspecialchars($admin_username) . "</li>";
echo "<li>Contraseña (Plana): [NO MOSTRAR EN PRODUCCIÓN]</li>"; // No mostrar la contraseña plana
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
            echo "<p style='color:red;'><strong>Error:</strong> No se pudo crear el usuario administrador. Código de error SQLSTATE: " . htmlspecialchars($errorInfo[0]) . " - Driver error code: " . htmlspecialchars($errorInfo[1]) . " - Message: " . htmlspecialchars($errorInfo[2]) . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>Error de Base de Datos:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Por favor, verifique su conexión a la base de datos y la configuración en `dashboard/db_connect.php`.</p>";
    echo "<p>Asegúrese de que la tabla `users` exista y tenga la estructura correcta (id SERIAL PRIMARY KEY, username VARCHAR(50) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL).</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error General:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Recordatorio de Seguridad:</strong> Una vez que el usuario administrador haya sido creado y haya verificado que puede iniciar sesión, elimine este archivo (`create_admin.php`) del servidor. Dejar este archivo accesible es un riesgo de seguridad.</p>";
echo "</body></html>";
?>

