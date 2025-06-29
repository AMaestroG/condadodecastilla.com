<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_connect.php';
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
    return;
}
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
    <title>Administrar Tienda</title>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
</head>
<body class="alabaster-bg admin-page">
    <?php require_once __DIR__ . '/../fragments/admin_header.php'; ?>
<div class="admin-container wide">
    <h1 class="gradient-text font-headings">Administrar Productos</h1>
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
        <button type="submit" class="btn-primary">Agregar</button>
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
                    <form action="" method="POST" class="inline-form" onsubmit="return confirm('¿Eliminar producto?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
