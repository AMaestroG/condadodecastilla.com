<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museo Colaborativo del Condado - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script> <!-- Will be commented out in JS -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/PointerLockControls.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <!-- Postprocessing -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/EffectComposer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/RenderPass.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/postprocessing/UnrealBloomPass.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/shaders/LuminosityHighPassShader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/shaders/CopyShader.js"></script>

    <div id="crosshair" style="position: fixed; top: 50%; left: 50%; width: 6px; height: 6px; background-color: rgba(255,255,255,0.6); border-radius: 50%; transform: translate(-50%, -50%); z-index: 9998; pointer-events: none; display: none;"></div>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div id="linterna-condado"></div> 
    
    <div id="header-placeholder"></div> 

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--condado-primario-rgb), 0.75), rgba(var(--condado-texto-rgb), 0.88)), url('/imagenes/hero_museo_background.jpg');">
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <h1>Museo Colaborativo del Alfoz</h1>
            <p>Un espacio para compartir y descubrir los tesoros históricos y arqueológicos de nuestra tierra, aportados por la comunidad.</p>
        </div>
    </header>

    <main>
        <section class="section upload-section">
            <div class="container page-content-block">
                <h2 class="section-title">Aporta tu Pieza al Museo <i class="fas fa-upload"></i></h2>
                <p class="intro-paragraph">
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
                    <div class="form-group preview-container" id="imagePreviewContainer" style="display:none;">
                        <img id="imagePreview" src="#" alt="Vista previa de la imagen subida" style="max-height: 200px; margin-bottom: 10px; border: 1px solid var(--condado-piedra-media);"/>
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

        <div class="view-switch-controls container" style="text-align: center; margin-bottom: 20px; padding-top: 20px;">
            <button id="show-2d-gallery-btn" class="cta-button view-switch-button">Ver Galería 2D</button>
            <button id="show-3d-museum-btn" class="cta-button view-switch-button">Explorar Museo 3D</button>
        </div>

        <section class="section museum-gallery-section alternate-bg" id="gallery-2d-section">
            <div class="container">
                <h2 class="section-title">Galería del Museo <i class="fas fa-landmark"></i></h2>
                <div id="museumGalleryGrid" class="card-grid museum-gallery-grid">
                    <p class="no-pieces-message" id="noPiecesMessage">Cargando piezas del museo...</p>
                </div>
            </div>
        </section>
    </main>

    <section id="museo-3d-section" class="section museo-3d-section alternate-bg">
        <div class="container page-content-block">
            <h2 class="section-title">Explora el Museo en 3D <i class="fas fa-vr-cardboard"></i></h2>
            <p class="intro-paragraph">Visualiza algunas piezas destacadas en un entorno tridimensional interactivo. Usa el ratón para orbitar, hacer zoom y seleccionar piezas para ver sus detalles. (Funcionalidad en desarrollo)</p>
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

    <footer class="footer">
        <div class="container">
            <p>© <script>document.write(new Date().getFullYear());</script> CondadoDeCastilla.com - Todos los derechos reservados.</p>
            <p>Un proyecto para la difusión del patrimonio histórico de Cerezo de Río Tirón y el Alfoz de Cerasio y Lantarón.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/groups/1052427398664069" aria-label="Facebook" title="Síguenos en Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="/en_construccion.html" aria-label="Instagram" title="Síguenos en Instagram"><i class="fab fa-instagram"></i></a>
                <a href="/en_construccion.html" aria-label="Twitter" title="Síguenos en Twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="/js/config.js"></script>
    <script src="/js/layout.js"></script>
    <script src="/js/museo-2d-gallery.js"></script>
    <!-- Museo 3D Modules -->
    <script src="/js/museum-3d/utils.js"></script>
    <script src="/js/museum-3d/config.js"></script>
    <script src="/js/museum-3d/sceneManager.js"></script>
    <script src="/js/museum-3d/museumLayout.js"></script>
    <script src="/js/museum-3d/playerControls.js"></script>
    <script src="/js/museum-3d/exhibitManager.js"></script>
    <script src="/js/museo-3d-main.js"></script>

    <div id="pointer-lock-instructions" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); color: white; display: flex; justify-content: center; align-items: center; text-align: center; font-size: 20px; z-index: 10000; cursor: pointer;">
        <div>
            <h1>Exploración del Museo</h1>
            <p style="font-size: 1.2em; margin-top: 1em;">Haz clic en esta pantalla para empezar a explorar.</p>
            <p style="margin-top: 0.5em;">Usa las teclas <kbd>W</kbd> <kbd>A</kbd> <kbd>S</kbd> <kbd>D</kbd> para moverte y el <kbd>RATÓN</kbd> para mirar alrededor.</p>
            <p style="font-size: 0.8em; margin-top: 2em;">Presiona <kbd>ESC</kbd> para liberar el cursor.</p>
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
