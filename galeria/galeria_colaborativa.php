<?php
require_once __DIR__ . '/../includes/session.php';
ensure_session_started();
require_once __DIR__ . '/../includes/auth.php';      // For is_admin_logged_in()
// Asegurar que se incluye el módulo de conexión para obtener $pdo
require_once __DIR__ . '/../includes/db_connect.php'; // Necesario para $pdo
/** @var PDO $pdo */
if (!$pdo) {
    echo "<p class='db-warning'>Contenido en modo lectura: base de datos no disponible.</p>";
}
require_once __DIR__ . '/../includes/text_manager.php'; // Necesario para editableText()
require_once __DIR__ . '/../includes/csrf.php';

$is_admin = is_admin_logged_in();
$gallery_images_data = [];
$gallery_dir = __DIR__ . '/../assets/img/galeria_colaborativa/';
$gallery_url_prefix = '/assets/img/galeria_colaborativa/';

if (is_dir($gallery_dir)) {
    $files = scandir($gallery_dir);
    if ($files) {
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && !is_dir($gallery_dir . $file)) {
                // Basic check for image extensions
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $allowed_extensions)) {
                    $gallery_images_data[] = [
                        'id' => htmlspecialchars($file), // Filename as ID
                        'titulo' => htmlspecialchars(ucfirst(str_replace(['_', '-'], ' ', pathinfo($file, PATHINFO_FILENAME)))),
                        'descripcion' => 'Imagen de la galería colaborativa.', // Generic description
                        'autor' => '', // No author info from filename
                        'imagenUrl' => htmlspecialchars($gallery_url_prefix . $file),
                        'altText' => htmlspecialchars(ucfirst(str_replace(['_', '-'], ' ', pathinfo($file, PATHINFO_FILENAME)))),
                        'showDeleteButton' => $is_admin // Add flag for delete button
                    ];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Galería Colaborativa - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">

    <?php
    require_once __DIR__ . '/../includes/load_page_css.php';
    load_page_css();
    ?>
</head>
<body class="alabaster-bg">
    <div id="linterna-condado" class="bg-linterna-gradient"></div> <!-- Para el efecto de linterna -->
    
    <?php require_once __DIR__ . '/../_header.php'; ?>

    <header class="page-header hero hero-galeria">
        <!-- IMPORTANTE: Asegúrate de tener /imagenes/hero_galeria_background.jpg -->
        <div class="hero-content">
            <img src="/imagenes/estrella.png" alt="Estrella de Venus decorativa" class="decorative-star-header">
            <?php editableText('galeria_colab_header_titulo', $pdo, 'Galería Colaborativa del Condado', 'h1', ''); ?>
            <?php editableText('galeria_colab_header_parrafo', $pdo, 'Un mosaico de miradas sobre la belleza, historia y rincones de nuestra tierra, creado por todos.', 'p', ''); ?>
        </div>
    </header>

    <main>
        <section class="section upload-section-galeria">
            <div class="container page-content-block">
                <h2 class="section-title"><?php editableText('galeria_colab_comparte_titulo', $pdo, 'Comparte tu Visión', 'span', ''); ?> <i class="fas fa-camera-retro"></i></h2>
                <p class="intro-paragraph">
                    <?php editableText('galeria_colab_comparte_parrafo', $pdo, '¿Has capturado la esencia del Condado de Castilla en una fotografía? ¿Un paisaje, un detalle arquitectónico, una escena cotidiana? ¡Súbela y forma parte de nuestra galería colectiva!', 'span', '', false); ?>
                    <br><small>(Las fotos subidas se añadirán a la galería si el backend está correctamente configurado).</small>
                </p>

                <form id="uploadPhotoForm" class="upload-form-container">
                    <input type="hidden" id="csrfGaleryToken" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
                    <div class="form-group">
                        <label for="photoTitulo"><i class="fas fa-heading"></i> Título de la Foto:</label>
                        <input type="text" id="photoTitulo" name="photoTitulo" required placeholder="Ej: Atardecer en el Alcázar">
                    </div>
                    <div class="form-group">
                        <label for="photoDescripcion"><i class="fas fa-pen-alt"></i> Descripción Breve (Opcional):</label>
                        <textarea id="photoDescripcion" name="photoDescripcion" rows="3" placeholder="Contexto, lugar, o lo que te inspire la foto..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="photoAutor"><i class="fas fa-user-circle"></i> Tu Nombre o Alias (Opcional):</label>
                        <input type="text" id="photoAutor" name="photoAutor" placeholder="Comparte tu autoría o déjalo anónimo">
                    </div>
                    <div class="form-group">
                        <label for="photoFile"><i class="fas fa-image"></i> Archivo de la Fotografía:</label>
                        <input type="file" id="photoFile" name="photoFile" accept="image/jpeg, image/png, image/gif" required>
                        <small>Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
                    </div>
                    <div class="form-group preview-container preview-hidden" id="photoPreviewContainer">
                        <img id="photoPreview" src="#" alt="Vista previa de la fotografía" class="photo-preview"/>
                    </div>
                    <button type="submit" class="cta-button submit-button"><i class="fas fa-share-square"></i> Compartir Fotografía</button>
                </form>
            </div>
        </section>

        <section class="section photo-gallery-section alternate-bg">
            <div class="container-epic"> 
                <h2 class="section-title"><?php editableText('galeria_colab_galeria_titulo', $pdo, 'Nuestra Galería Compartida', 'span', ''); ?> <i class="fas fa-images"></i></h2>
                <div id="photoGalleryGrid" class="photo-gallery-grid">
                    <p class="no-photos-message" id="noPhotosMessage">Cargando fotografías...</p>
                </div>
            </div>
        </section>
    </main>

    <div id="imageModal" class="modal">
        <span class="modal-close-button">&times;</span>
        <img class="modal-content" id="modalImage" alt="Imagen ampliada de la galería">
        <div id="modalCaption"></div>
    </div>

    <?php require_once __DIR__ . '/../_footer.php'; ?>

    <!-- Tu script layout.js para cargar el header/sidebar -->
    <script src="/js/layout.js"></script> 
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initializeLinterna === "function") {
                initializeLinterna();
            }

            // --- Lógica de la Galería Colaborativa ---
            const uploadForm = document.getElementById('uploadPhotoForm');
            const galleryGrid = document.getElementById('photoGalleryGrid');
            const noPhotosMsg = document.getElementById('noPhotosMessage');
            const photoPreview = document.getElementById('photoPreview');
            const photoPreviewContainer = document.getElementById('photoPreviewContainer');
            const photoFileInput = document.getElementById('photoFile');

            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            const modalCloseButton = document.querySelector('.modal-close-button');
            
            const API_BASE_URL_GALERIA = '/api/galeria';

            const phpGalleryPhotos = <?php echo json_encode($gallery_images_data); ?>;
            let localGalleryPhotos = [];

            async function fetchPhotos() {
                const url = `${API_BASE_URL_GALERIA}/fotos`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        let errorMsg = `Error HTTP: ${response.status} - ${response.statusText}. URL: ${url}`;
                        try {
                            const errData = await response.json();
                            errorMsg = errData.error || errorMsg;
                        } catch (e) { /* response not JSON */ }
                        throw new Error(errorMsg);
                    }
                    const photos = await response.json();
                    localGalleryPhotos = photos;
                } catch (error) {
                    console.error('Fallo al obtener fotos desde la API:', error);
                    localGalleryPhotos = phpGalleryPhotos;
                }
                renderPhotoGallery(localGalleryPhotos);
            }
            
            // Cargar fotos desde la API; se usará la lista generada por PHP si la llamada falla
            fetchPhotos();

            if (photoFileInput) {
                photoFileInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) { // 2MB
                            alert('La imagen es demasiado grande. El tamaño máximo es 2MB.');
                            this.value = ""; 
                            if(photoPreview) photoPreview.src = '#';
                            if(photoPreviewContainer) photoPreviewContainer.style.display = 'none';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if(photoPreview) photoPreview.src = e.target.result;
                            if(photoPreviewContainer) photoPreviewContainer.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        if(photoPreview) photoPreview.src = '#';
                        if(photoPreviewContainer) photoPreviewContainer.style.display = 'none';
                    }
                });
            }

            if (uploadForm) {
                uploadForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const titulo = document.getElementById('photoTitulo').value.trim();
                    const descripcion = document.getElementById('photoDescripcion').value.trim();
                    const autor = document.getElementById('photoAutor').value.trim() || 'Anónimo';
                    const imagenFile = photoFileInput.files[0];

                    if (!titulo || !imagenFile) {
                        alert('Por favor, completa el título y selecciona una imagen.');
                        return;
                    }
                     if (imagenFile.size > 2 * 1024 * 1024) { 
                        alert('La imagen es demasiado grande. El tamaño máximo es 2MB.');
                        return;
                    }

                    const formData = new FormData();
                    const csrfInput = document.getElementById('csrfGaleryToken');
                    if (csrfInput) {
                        formData.append('csrf_token', csrfInput.value);
                    }
                    formData.append('photoTitulo', titulo);
                    formData.append('photoDescripcion', descripcion);
                    formData.append('photoAutor', autor);
                    formData.append('photoFile', imagenFile);

                    try {
                        const url = `${API_BASE_URL_GALERIA}/fotos`;
                        const response = await fetch(url, { method: 'POST', body: formData });
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({ error: `Error del servidor: ${response.status} ${response.statusText}` }));
                            throw new Error(errorData.error || `Error del servidor: ${response.status} ${response.statusText}`);
                        }
                        const result = await response.json();
                        alert(result.mensaje || '¡Fotografía subida con éxito!');
                        // fetchPhotos();  // Descomentar si se desea refrescar la galería tras subir una foto
                        uploadForm.reset();
                        if(photoPreview) photoPreview.src = '#';
                        if(photoPreviewContainer) photoPreviewContainer.style.display = 'none';
                    } catch (error) {
                        console.error('Error al subir la fotografía:', error);
                        alert(`Error al subir la fotografía: ${error.message}. Asegúrate de que el backend esté corriendo y accesible.`);
                    }
                });
            }
            
            function renderPhotoGallery(photosArray) {
                if (!galleryGrid || !noPhotosMsg) return;
                galleryGrid.innerHTML = ''; 
                if (!photosArray || photosArray.length === 0) {
                    noPhotosMsg.style.display = 'block';
                    noPhotosMsg.textContent = 'Aún no se han compartido fotografías. ¡Anímate a ser el primero!';
                    return;
                }
                noPhotosMsg.style.display = 'none';

                photosArray.forEach(photo => {
                    const photoCard = document.createElement('div');
                    photoCard.classList.add('photo-card'); 
                    
                    const img = document.createElement('img');
                    let imageUrl = photo.imagenUrl;
                    if (API_BASE_URL_GALERIA && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:')) {
                        imageUrl = `${API_BASE_URL_GALERIA}${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}`;
                    } else if (!API_BASE_URL_GALERIA && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:') && !imageUrl.startsWith('/')) {
                        imageUrl = `/${imageUrl}`;
                    }

                    img.src = imageUrl; 
                    img.alt = photo.altText || `Fotografía: ${photo.titulo}`;
                    img.onerror = function() { 
                        this.onerror=null; 
                        this.src='https://placehold.co/400x300/D2B48C/2c1d12?text=Foto+no+disponible';
                        this.alt = `Error al cargar foto: ${photo.titulo}`;
                    };
                    img.addEventListener('click', () => openModal(img.src, `${photo.titulo}${photo.autor && photo.autor.toLowerCase() !== 'anónimo' ? ' - por ' + photo.autor : ''}`));

                    const captionDiv = document.createElement('div');
                    captionDiv.classList.add('photo-card-caption');
                    
                    const titleH4 = document.createElement('h4');
                    titleH4.textContent = photo.titulo;
                    titleH4.classList.add('blend-screen');
                    captionDiv.appendChild(titleH4);

                    if (photo.descripcion) {
                        const descP = document.createElement('p');
                        descP.textContent = photo.descripcion.substring(0,70) + (photo.descripcion.length > 70 ? '...' : '');
                        captionDiv.appendChild(descP);
                    }
                     if (photo.autor && photo.autor.toLowerCase() !== 'anónimo') {
                        const authorP = document.createElement('p');
                        authorP.classList.add('photo-author-gallery');
                        authorP.innerHTML = `<small>Por: ${photo.autor}</small>`;
                        captionDiv.appendChild(authorP);
                    }

                    if (photo.showDeleteButton) {
                        const deleteButton = document.createElement('button');
                        deleteButton.classList.add('delete-button', 'btn-condado', 'btn-condado-peligro'); // Use existing classes if available
                        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i> Borrar';
                        deleteButton.setAttribute('data-id', photo.id); // photo.id is the filename
                        deleteButton.addEventListener('click', handleDeleteFotoGaleria); // Existing handler
                        captionDiv.appendChild(deleteButton);
                    }
                    
                    photoCard.appendChild(img);
                    photoCard.appendChild(captionDiv);
                    galleryGrid.appendChild(photoCard);
                });
            }

            async function handleDeleteFotoGaleria(event) {
                const fotoId = event.target.closest('.delete-button').getAttribute('data-id');
                if (!confirm(`¿Estás seguro de que quieres borrar la fotografía con ID ${fotoId}? Esta acción no se puede deshacer.`)) {
                    return;
                }
                try {
                    const url = '/dashboard/delete_gallery_image.php'; // New endpoint
                    const formData = new FormData();
                    formData.append('filename', fotoId);
                    const csrfInput = document.getElementById('csrfGaleryToken');
                    if (csrfInput) {
                        formData.append('csrf_token', csrfInput.value);
                    }

                    const response = await fetch(url, { method: 'POST', body: formData }); // Changed to POST, can also be DELETE with FormData for some servers
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(errorText || `Error del servidor: ${response.status}`);
                    }
                    const resultText = await response.text(); // Expecting plain text or simple JSON
                    alert(resultText || 'Fotografía borrada con éxito.');

                    // Refresh gallery by re-assigning and re-rendering
                    // This is a simple way if not fetching new data from server after delete
                    localGalleryPhotos = localGalleryPhotos.filter(p => p.id !== fotoId);
                    renderPhotoGallery(localGalleryPhotos);
                    if (localGalleryPhotos.length === 0 && noPhotosMsg) {
                        noPhotosMsg.textContent = 'No hay fotografías en la galería todavía, o el directorio está vacío.';
                        noPhotosMsg.style.display = 'block';
                    }

                } catch (error) {
                    console.error('Error al borrar la fotografía:', error);
                    alert(`Error al borrar la fotografía: ${error.message}. Asegúrate de que el script delete_gallery_image.php exista y funcione.`);
                }
            }
            
            function openModal(src, caption) {
                if(modal && modalImage && modalCaption) {
                    modal.style.display = "block";
                    modalImage.src = src;
                    modalCaption.innerHTML = caption;
                }
            }

            if (modalCloseButton) {
                modalCloseButton.onclick = function() {
                    if(modal) modal.style.display = "none";
                }
            }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    if(modal) modal.style.display = "none";
                }
            }
        });
    </script>

</body>
</html>
