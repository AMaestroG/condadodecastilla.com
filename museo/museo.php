<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Museo Colaborativo del Condado - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">
    <script src="https://cdn.jsdelivr.net/npm/three@0.177.0/build/three.core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script type="module">
        import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/controls/OrbitControls.js';
        import { PointerLockControls } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/controls/PointerLockControls.js';
        import { EffectComposer } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/postprocessing/EffectComposer.js';
        import { RenderPass } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/postprocessing/RenderPass.js';
        import { UnrealBloomPass } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/postprocessing/UnrealBloomPass.js';
        import { LuminosityHighPassShader } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/shaders/LuminosityHighPassShader.js';
        import { CopyShader } from 'https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/shaders/CopyShader.js';

        window.THREE.OrbitControls = OrbitControls;
        window.THREE.PointerLockControls = PointerLockControls;
        window.THREE.EffectComposer = EffectComposer;
        window.THREE.RenderPass = RenderPass;
        window.THREE.UnrealBloomPass = UnrealBloomPass;
        window.THREE.LuminosityHighPassShader = LuminosityHighPassShader;
        window.THREE.CopyShader = CopyShader;
    </script>

    <!-- Google Fonts, FontAwesome, and epic_theme.css are now in head_common.php -->
</head>
<body class="alabaster-bg">
    <div id="crosshair" class="crosshair"></div>
    <div id="linterna-condado" class="bg-linterna-gradient"></div>
    
    <?php require_once __DIR__ . '/../fragments/header.php'; ?>

    <header class="page-header hero hero-museo">
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1 class="text-4xl font-headings">Museo Colaborativo del Alfoz</h1>
            <p class="text-lg font-body">Un espacio para compartir y descubrir los tesoros históricos y arqueológicos de nuestra tierra, aportados por la comunidad.</p>
        </div>
    </header>

    <main>
        <section class="section upload-section">
            <div class="container page-content-block">
                <h2 class="section-title text-2xl font-headings">Aporta tu Pieza al Museo <i class="fas fa-upload"></i></h2>
                <p class="intro-paragraph text-lg font-body">
                    ¿Tienes una fotografía de un objeto, ruina o documento histórico relacionado con el Alfoz de Cerasio y Lantarón o el Condado de Castilla? ¡Compártelo con nosotros! 
                    <br><small>(Las piezas subidas se guardarán si el backend está correctamente configurado y en funcionamiento).</small>
                </p>

                <form id="uploadForm" class="upload-form-container">
                    <input type="hidden" id="csrfToken" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                    <div class="form-group">
                        <label for="piezaTitulo"><i class="fas fa-signature"></i> Título de la Pieza:</label>
                        <input type="text" id="piezaTitulo" name="piezaTitulo" required placeholder="Ej: Fragmento de cerámica romana">
                    </div>
                    <div class="form-group">
                        <label for="piezaDescripcion"><i class="fas fa-align-left"></i> Descripción Breve:</label>
                        <textarea id="piezaDescripcion" name="piezaDescripcion" rows="4" required placeholder="Describe la pieza, su posible origen, datación, o cualquier detalle relevante..."></textarea>
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
                    <div class="form-group preview-container preview-hidden" id="imagePreviewContainer">
                        <img id="imagePreview" src="#" alt="Vista previa de la imagen subida" class="photo-preview"/>
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

                    <button type="submit" class="cta-button submit-button"><i class="fas fa-cloud-upload-alt"></i> Subir Pieza al Museo</button>
                </form>
            </div>
        </section>

        <div class="view-switch-controls container text-center">
            <button id="show-2d-gallery-btn" class="cta-button view-switch-button">Ver Galería 2D</button>
            <button id="show-3d-museum-btn" class="cta-button view-switch-button">Explorar Museo 3D</button>
        </div>

        <section class="section museum-gallery-section alternate-bg" id="gallery-2d-section">
            <div class="container-epic">
                <h2 class="section-title text-2xl font-headings">Galería del Museo <i class="fas fa-landmark"></i></h2>
                <div id="museumGalleryGrid" class="card-grid museum-gallery-grid">
                    <p class="no-pieces-message text-lg font-body" id="noPiecesMessage">Cargando piezas del museo...</p>
                </div>
            </div>
        </section>
    </main>

    <section id="museo-3d-section" class="section museo-3d-section alternate-bg">
        <div class="container page-content-block">
            <h2 class="section-title text-2xl font-headings">Explora el Museo en 3D <i class="fas fa-vr-cardboard"></i></h2>
            <p class="intro-paragraph text-lg font-body">Visualiza algunas piezas destacadas en un entorno tridimensional interactivo. Usa el ratón para orbitar, hacer zoom y seleccionar piezas para ver sus detalles. (Funcionalidad en desarrollo)</p>
            <div id="museo-3d-container">
                <!-- Three.js canvas will be injected here -->
            </div>
        </div>
    </section>

    <div id="imageModal" class="modal">
        <span class="modal-close-button">&times;</span>
        <img class="modal-content" id="modalImage" alt="Imagen ampliada de la pieza del museo">
        <div id="modalCaption"></div>
    </div>

    <?php require_once __DIR__ . '/../fragments/footer.php'; ?>

    <script src="/js/config.js"></script>
    
    <script src="/js/museo-2d-gallery.js"></script>
    <!-- Museo 3D Modules -->
    <script src="/js/museum-3d/utils.js"></script>
    <script src="/js/museum-3d/config.js"></script>
    <script src="/js/museum-3d/sceneManager.js"></script>
    <script src="/js/museum-3d/museumLayout.js"></script>
    <script src="/js/museum-3d/playerControls.js"></script>
    <script src="/js/museum-3d/exhibitManager.js"></script>
    <script src="/js/museo-3d-main.js"></script>

    <div id="pointer-lock-instructions" class="pointer-lock-instructions">
        <div>
            <h1 class="text-4xl font-headings">Exploración del Museo</h1>
            <p class="intro-big text-lg font-body">Haz clic en esta pantalla para empezar a explorar.</p>
            <p class="text-lg font-body">Usa las teclas <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> para moverte y el <kbd>RATÓN</kbd> para mirar alrededor.</p>
            <p class="ai-note text-lg font-body">Presiona <kbd>ESC</kbd> para liberar el cursor.</p>
        </div>
    </div>

    <div id="pieza-info-overlay">
        <div id="overlay-content">
            <h3 id="overlay-titulo"></h3>
            <p id="overlay-autor"></p>
            <p id="overlay-descripcion"></p>
            <button id="overlay-cerrar" aria-label="Cerrar información de la pieza">&times; Cerrar</button>
        </div>
    </div>

</body>
</html>
