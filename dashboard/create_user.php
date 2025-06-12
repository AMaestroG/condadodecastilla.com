<?php
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../includes/db_connect.php';

ensure_session_started();
require_admin_login();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_message = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = trim($_POST['role']);

        if (empty($username) || empty($password) || empty($role)) {
            $error_message = 'Todos los campos son obligatorios.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password_hash', $password_hash);
                $stmt->bindParam(':role', $role);

                if ($stmt->execute()) {
                    $success_message = 'Usuario creado exitosamente.';
                } else {
                    $error_message = 'Error al crear el usuario.';
                }
            } catch (PDOException $e) {
                // Check for duplicate username error (SQLSTATE 23000 - Integrity constraint violation)
                if ($e->getCode() == '23000') {
                    $error_message = 'El nombre de usuario ya existe. Por favor, elija otro.';
                } else {
                    $error_message = 'Error de base de datos: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; flex-direction: column; }
        .container { background-color: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button[type="submit"]:hover { background-color: #0056b3; }
        .navigation-link { text-align: center; margin-top: 15px; }
        .navigation-link a { color: #007bff; text-decoration: none; }
        .navigation-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crear Nuevo Usuario</h2>

        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="create_user.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">

            <div>
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <label for="role">Rol:</label>
                <input type="text" id="role" name="role" required value="editor"> <!-- Default to editor -->
                <!-- Or use a select dropdown:
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="editor" selected>Editor</option>
                </select>
                -->
            </div>

            <button type="submit">Crear Usuario</button>
        </form>
        <div class="navigation-link">
            <a href="index.php">Volver al Panel de Administración</a>
        </div>
    </div>
</body>
</html>
