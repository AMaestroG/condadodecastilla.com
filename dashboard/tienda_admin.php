<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/db_connect.php';
/** @var PDO $pdo */
require_once __DIR__ . '/../includes/csrf.php';

require_admin_login();

$feedback_message = '';
$feedback_type = '';

// Directory for product images (outside web root)
$upload_dir = dirname(__DIR__) . '/uploads_storage/tienda_productos/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0775, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $feedback_message = 'CSRF token inválido.';
        $feedback_type = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio'] ?? 0);
            $stock = intval($_POST['stock'] ?? 0);
            if ($nombre === '' || empty($_FILES['imagen']['name'])) {
                $feedback_message = 'Nombre e imagen son obligatorios.';
                $feedback_type = 'error';
            } else {
                $file = $_FILES['imagen'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                $allowed = ['image/jpeg','image/png','image/gif'];
                if ($file['error'] !== UPLOAD_ERR_OK || !in_array($mime, $allowed)) {
                    $feedback_message = 'Error o tipo de imagen no válido.';
                    $feedback_type = 'error';
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-zA-Z0-9_.-]/','_', pathinfo($file['name'], PATHINFO_FILENAME));
                    $filename = time().'_'.$safe.'.'.$ext;
                    if (move_uploaded_file($file['tmp_name'], $upload_dir.$filename)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO tienda_productos (nombre, descripcion, precio, imagen_nombre, stock) VALUES (:n,:d,:p,:i,:s)");
                            $stmt->execute([':n'=>$nombre, ':d'=>$descripcion, ':p'=>$precio, ':i'=>$filename, ':s'=>$stock]);
                            $feedback_message = 'Producto añadido correctamente.';
                            $feedback_type = 'success';
                        } catch (PDOException $e) {
                            @unlink($upload_dir.$filename);
                            $feedback_message = 'Error al guardar en la base de datos.';
                            $feedback_type = 'error';
                        }
                    } else {
                        $feedback_message = 'No se pudo guardar la imagen.';
                        $feedback_type = 'error';
                    }
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $pdo->prepare("SELECT imagen_nombre FROM tienda_productos WHERE id=:id");
                    $stmt->execute([':id'=>$id]);
                    $img = $stmt->fetchColumn();
                    $stmt = $pdo->prepare("DELETE FROM tienda_productos WHERE id=:id");
                    $stmt->execute([':id'=>$id]);
                    if ($stmt->rowCount()) {
                        if ($img && file_exists($upload_dir.$img)) {
                            @unlink($upload_dir.$img);
                        }
                        $feedback_message = 'Producto eliminado.';
                        $feedback_type = 'success';
                    }
                } catch (PDOException $e) {
                    $feedback_message = 'Error al eliminar el producto.';
                    $feedback_type = 'error';
                }
            }
        }
    }
}

$productos = [];
try {
    $stmt = $pdo->query("SELECT id, nombre, descripcion, precio, imagen_nombre, stock, created_at FROM tienda_productos ORDER BY id DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedback_message = 'Error al cargar productos: ' . $e->getMessage();
    $feedback_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Tienda</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .feedback.success { background:#d4edda; padding:10px; margin-bottom:10px; }
        .feedback.error { background:#f8d7da; padding:10px; margin-bottom:10px; }
    </style>
</head>
<body>
<div class="container">
    <nav>
        <a href="../index.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
    <h1>Administrar Productos</h1>
    <?php if ($feedback_message): ?>
        <div class="feedback <?php echo htmlspecialchars($feedback_type); ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>
    <h2>Añadir Producto</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
        <input type="hidden" name="action" value="add">
        <div>
            <label>Nombre:</label><br>
            <input type="text" name="nombre" required>
        </div>
        <div>
            <label>Descripción:</label><br>
            <textarea name="descripcion"></textarea>
        </div>
        <div>
            <label>Precio:</label><br>
            <input type="number" name="precio" step="0.01" required>
        </div>
        <div>
            <label>Stock:</label><br>
            <input type="number" name="stock" value="0" required>
        </div>
        <div>
            <label>Imagen:</label><br>
            <input type="file" name="imagen" accept="image/*" required>
        </div>
        <button type="submit">Agregar</button>
    </form>
    <h2>Productos Existentes</h2>
    <table>
        <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td><?php echo number_format($p['precio'],2); ?>€</td>
                <td><?php echo (int)$p['stock']; ?></td>
                <td>
                    <form action="" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar producto?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
