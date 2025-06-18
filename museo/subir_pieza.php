<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Pieza al Museo - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div id="linterna-condado"></div>
    <?php
        require_once __DIR__ . '/../includes/csrf.php';
        require_once __DIR__ . '/../_header.php';
    ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--condado-primario-rgb), 0.75), rgba(var(--condado-texto-rgb), 0.88)), url('/imagenes/hero_museo_background.jpg');">
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Sube una Pieza al Museo</h1>
            <p>Comparte objetos o documentos históricos relacionados con el Alfoz y el Condado de Castilla.</p>
        </div>
    </header>

    <main>
        <section class="section upload-section">
            <div class="container page-content-block">
                <h2 class="section-title">Aporta tu Pieza <i class="fas fa-upload"></i></h2>
                <p class="intro-paragraph">
                    ¿Tienes una fotografía de un objeto, ruina o documento? ¡Compártelo con nosotros!
                    <br><small>(Las piezas se guardarán si el backend está configurado correctamente).</small>
                </p>

                <form id="uploadForm" class="upload-form-container">
                    <input type="hidden" id="csrfToken" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                    <div class="form-group">
                        <label for="piezaTitulo"><i class="fas fa-signature"></i> Título de la Pieza:</label>
                        <input type="text" id="piezaTitulo" name="piezaTitulo" required placeholder="Ej: Fragmento de cerámica romana">
                    </div>
                    <div class="form-group">
                        <label for="piezaDescripcion"><i class="fas fa-align-left"></i> Descripción Breve:</label>
                        <textarea id="piezaDescripcion" name="piezaDescripcion" rows="4" required placeholder="Describe la pieza..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="piezaAutor"><i class="fas fa-user-edit"></i> Nombre del Autor/Dueño (Opcional):</label>
                        <input type="text" id="piezaAutor" name="piezaAutor" placeholder="Tu nombre, 'Anónimo', o el dueño si se conoce">
                    </div>
                    <div class="form-group">
                        <label for="piezaImagen"><i class="fas fa-image"></i> Imagen de la Pieza:</label>
                        <input type="file" id="piezaImagen" name="piezaImagen" accept="image/jpeg, image/png, image/gif" required>
                        <small>Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
                    </div>
                    <div class="form-group preview-container" id="imagePreviewContainer" style="display:none;">
                        <img id="imagePreview" src="#" alt="Vista previa de la imagen" style="max-height: 200px; margin-bottom: 10px; border: 1px solid var(--condado-piedra-media);"/>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend><i class="fas fa-cube"></i> Opcional: Detalles de Posicionamiento 3D</legend>
                        <div class="form-group">
                            <label for="notasAdicionales"><i class="fas fa-pencil-alt"></i> Notas Adicionales (para 3D):</label>
                            <textarea id="notasAdicionales" name="notasAdicionales" rows="3" placeholder="Cualquier nota relevante para la visualización 3D..."></textarea>
                        </div>
                        <div class="form-grid-triplet">
                            <div class="form-group">
                                <label for="posX"><i class="fas fa-arrows-alt-h"></i> Posición X (3D):</label>
                                <input type="number" id="posX" name="posX" step="0.1" placeholder="0.0">
                            </div>
                            <div class="form-group">
                                <label for="posY"><i class="fas fa-arrows-alt-v"></i> Posición Y (3D):</label>
                                <input type="number" id="posY" name="posY" step="0.1" placeholder="0.0">
                            </div>
                            <div class="form-group">
                                <label for="posZ"><i class="fas fa-ruler-combined"></i> Posición Z (3D):</label>
                                <input type="number" id="posZ" name="posZ" step="0.1" placeholder="0.0">
                            </div>
                        </div>
                        <div class="form-grid-doublet">
                            <div class="form-group">
                                <label for="escala"><i class="fas fa-search-plus"></i> Escala (3D):</label>
                                <input type="number" id="escala" name="escala" step="0.05" placeholder="1.0">
                            </div>
                            <div class="form-group">
                                <label for="rotacionY"><i class="fas fa-sync-alt"></i> Rotación Y (3D Grados):</label>
                                <input type="number" id="rotacionY" name="rotacionY" step="0.1" placeholder="0.0">
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="cta-button submit-button"><i class="fas fa-cloud-upload-alt"></i> Subir Pieza</button>
                </form>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../_footer.php'; ?>
    <script src="/js/config.js"></script>
    <script src="/js/layout.js"></script>
    <script src="/js/museo-2d-gallery.js"></script>
</body>
</html>
