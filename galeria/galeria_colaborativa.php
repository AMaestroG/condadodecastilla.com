<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
require_once __DIR__ . '/../includes/auth.php';      // For is_admin_logged_in()
// dashboard/db_connect.php ya podría estar comentado, asegurarse que se incluye para $pdo
require_once __DIR__ . '/../dashboard/db_connect.php'; // Necesario para $pdo
/** @var PDO $pdo */
require_once __DIR__ . '/../includes/text_manager.php'; // Necesario para editableText()

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería Colaborativa - Condado de Castilla</title>
    <link rel="icon" href="/imagenes/escudo.jpg" type="image/jpeg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/assets/css/epic_theme.css">
</head>
<body>
    <div id="linterna-condado"></div> <!-- Para el efecto de linterna -->
    
    <?php require_once __DIR__ . '/../_header.html'; ?>

    <header class="page-header hero" style="background-image: linear-gradient(rgba(var(--condado-primario-rgb), 0.75), rgba(var(--condado-texto-rgb), 0.88)), url('/imagenes/hero_galeria_background.jpg');">
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
                    <div class="form-group preview-container" id="photoPreviewContainer" style="display:none;">
                        <img id="photoPreview" src="#" alt="Vista previa de la fotografía" style="max-height: 200px; margin-bottom: 10px; border: 1px solid var(--condado-piedra-media);"/>
                    </div>
                    <button type="submit" class="cta-button submit-button"><i class="fas fa-share-square"></i> Compartir Fotografía</button>
                </form>
            </div>
        </section>

        <section class="section photo-gallery-section alternate-bg">
            <div class="container"> 
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

    <!-- Tu script layout.js para cargar el header/sidebar -->
    <script src="/js/layout.js"></script> 
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Efecto Linterna ---
            const linterna = document.getElementById('linterna-condado');
            const seccionesConLinterna = document.querySelectorAll('.section'); 

            if (linterna) {
                function actualizarPosicionLinterna(e) {
                    requestAnimationFrame(() => {
                        linterna.style.setProperty('--mouse-x', e.clientX + 'px');
                        linterna.style.setProperty('--mouse-y', e.clientY + 'px');
                    });
                }
                function activarLinterna() {
                    requestAnimationFrame(() => {
                        linterna.style.setProperty('--linterna-opacity', '0.65'); 
                        linterna.style.setProperty('--linterna-radio', '250px'); 
                    });
                }
                function desactivarLinterna() {
                    requestAnimationFrame(() => {
                        linterna.style.setProperty('--linterna-opacity', '0');
                    });
                }
                document.body.addEventListener('mousemove', actualizarPosicionLinterna);
                seccionesConLinterna.forEach(section => {
                    section.addEventListener('mouseenter', activarLinterna);
                    section.addEventListener('mouseleave', desactivarLinterna);
                });
            } else {
                console.warn("Elemento #linterna-condado no encontrado.");
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

            // function loadSamplePhotos() { // Commented out or removed
            //     return [
            //         { id: 'sample_photo1', titulo: "Atardecer en el Alcázar (Ejemplo)", descripcion: "Vista del Alcázar de Cerezo bañado por la luz dorada del atardecer.", autor: "Fotógrafo Local", imagenUrl: "/imagenes/galeria_colaborativa/ejemplo_atardecer_alcazar.jpg", altText: "Atardecer sobre el Alcázar de Cerezo" },
            //         { id: 'sample_photo2', titulo: "Detalle Románico (Ejemplo)", descripcion: "Capitel historiado en la portada de una de las iglesias de la comarca.", autor: "Visitante Anónimo", imagenUrl: "/imagenes/galeria_colaborativa/ejemplo_detalle_romanico.jpg", altText: "Detalle de un capitel románico" },
            //         { id: 'sample_photo3', titulo: "Paisaje desde las Alturas (Ejemplo)", descripcion: "Panorámica de los campos de Castilla desde un mirador cercano a Lantarón.", autor: "Amante del Senderismo", imagenUrl: "/imagenes/galeria_colaborativa/ejemplo_paisaje_lantaron.jpg", altText: "Paisaje castellano desde las alturas" }
            //     ];
            // }

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
                    renderPhotoGallery(localGalleryPhotos);
                } catch (error) {

                    }
                }
            }
            
            // Cargar fotos desde la API; se usará la lista generada por PHP si la llamada falla
            fetchPhotos();

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
