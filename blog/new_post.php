<?php
require_once __DIR__ . '/../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Artículo</title>
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div class="container">
        <h1>Crear nuevo artículo</h1>
        <form action="create_post.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
            <div>
                <label for="title">Título:</label><br>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="content">Contenido:</label><br>
                <textarea id="content" name="content" rows="10" required></textarea>
            </div>
            <div>
                <label for="image">Imagen (opcional):</label><br>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
            </div>
            <button type="submit">Publicar</button>
        </form>
        <p><a href="index.php">Volver al blog</a></p>
    </div>
</body>
</html>
