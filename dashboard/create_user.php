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
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';

        $allowed_roles = ['admin', 'editor'];

        if (empty($username) || empty($password) || empty($role)) {
            $error_message = 'Todos los campos son obligatorios.';
        } elseif (!in_array($role, $allowed_roles, true)) {
            $error_message = 'Rol inválido.';
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
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
</head>
<body class="admin-page centered">
    <div class="admin-container narrow">
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
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="editor" selected>Editor</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">Crear Usuario</button>
        </form>
        <div class="navigation-link">
            <a href="index.php">Volver al Panel de Administración</a>
        </div>
    </div>
</body>
</html>
