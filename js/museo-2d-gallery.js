document.addEventListener('DOMContentLoaded', () => {
    // --- Efecto Linterna ---
    // This part seems to be a general effect, not specific to the 2D gallery.
    // Consider if it should be in a more global JS file if used on many pages.
    // For now, keeping it here as per extraction from museo.html's main script.
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

    // --- Lógica del Museo Colaborativo (2D Gallery) ---

    // --- Variables y DOM Element Selections ---
    const uploadForm = document.getElementById('uploadForm');
    const gallery2DSection = document.getElementById('gallery-2d-section');
    const museumGalleryGrid = document.getElementById('museumGalleryGrid');
    const noPiecesMessage = document.getElementById('noPiecesMessage');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const piezaImagenInput = document.getElementById('piezaImagen');

    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const modalCloseButton = document.querySelector('.modal-close-button');

    const museum3DSection = document.getElementById('museo-3d-section');
    const show2DGalleryBtn = document.getElementById('show-2d-gallery-btn');
    const show3DMuseumBtn = document.getElementById('show-3d-museum-btn');

    const API_BASE_URL = window.API_BASE_URL || '';

    let localMuseumPieces = []; // Cache for fetched pieces

    // --- View Switching Logic ---
    function switchTo2DView() {
        if (gallery2DSection) gallery2DSection.style.display = 'block';
        if (museum3DSection) museum3DSection.style.display = 'none';
        if (show2DGalleryBtn) show2DGalleryBtn.disabled = true;
        if (show3DMuseumBtn) show3DMuseumBtn.disabled = false;
        // Consider stopping 3D animations if any are running globally
    }

    function switchTo3DView() {
        if (gallery2DSection) gallery2DSection.style.display = 'none';
        if (museum3DSection) museum3DSection.style.display = 'block';
        if (show2DGalleryBtn) show2DGalleryBtn.disabled = false;
        if (show3DMuseumBtn) show3DMuseumBtn.disabled = true;
        // Trigger resize for 3D canvas, as it might initialize based on current dimensions
        window.dispatchEvent(new Event('resize'));
    }

    if (show2DGalleryBtn && show3DMuseumBtn && gallery2DSection && museum3DSection) {
        show2DGalleryBtn.addEventListener('click', switchTo2DView);
        show3DMuseumBtn.addEventListener('click', switchTo3DView);
        switchTo2DView(); // Initial state
    } else {
        console.warn("View switching elements not found. Defaulting to 2D view shown, 3D view hidden if it exists.");
        if (gallery2DSection) gallery2DSection.style.display = 'block'; // Ensure 2D is visible
        if (museum3DSection && !(show2DGalleryBtn && show3DMuseumBtn)) { // If buttons missing, hide 3D
            museum3DSection.style.display = 'none';
       }
    }

    // --- API Interaction Functions ---
    async function fetchPieces() {
        try {
            const url = `${API_BASE_URL}/api/museo/piezas`;
            const response = await fetch(url);
            if (!response.ok) {
                let errorMsg = `Error HTTP: ${response.status} - ${response.statusText}. URL: ${url}`;
                try {
                    const errData = await response.json();
                    errorMsg = errData.error || errorMsg;
                } catch (e) { /* response not JSON */ }
                throw new Error(errorMsg);
            }
            const pieces = await response.json();
            localMuseumPieces = pieces;
            renderGallery(localMuseumPieces);
        } catch (error) {
            console.error('Error fetching pieces from backend:', error);
            localMuseumPieces = loadSamplePieces(); // Fallback to sample data
            renderGallery(localMuseumPieces);
            if(noPiecesMessage) {
                noPiecesMessage.innerHTML = `Could not load pieces from server. Displaying examples. <br><small>Error: ${error.message}</small>`;
                noPiecesMessage.style.display = 'block';
                noPiecesMessage.style.color = 'orange';
                noPiecesMessage.style.textAlign = 'center';
            }
        }
    }

    async function handleSubmitUploadForm(event) {
        event.preventDefault();
        const titulo = document.getElementById('piezaTitulo').value.trim();
        const descripcion = document.getElementById('piezaDescripcion').value.trim();
        const autor = document.getElementById('piezaAutor').value.trim() || 'Anónimo'; // Default to Anónimo
        const imagenFile = piezaImagenInput.files[0];

        // Client-side validation (basic)
        if (!titulo || !descripcion || !imagenFile) {
            alert('Please complete title, description, and select an image.');
            return;
        }
         if (imagenFile.size > 2 * 1024 * 1024) { // 2MB check
            alert('Image is too large. Maximum size is 2MB.');
            return;
        }

        const formData = new FormData();
        const csrfField = document.getElementById('csrfToken');
        if (csrfField) {
            formData.append('csrf_token', csrfField.value);
        }
        formData.append('piezaTitulo', titulo);
        formData.append('piezaDescripcion', descripcion);
        formData.append('piezaAutor', autor);
        formData.append('piezaImagen', imagenFile);

        // Add optional 3D positioning fields
        const notasAdicionales = document.getElementById('notasAdicionales').value;
        if (notasAdicionales.trim() !== '') formData.append('notasAdicionales', notasAdicionales);

        const posX = document.getElementById('posX').value;
        if (posX.trim() !== '') formData.append('posX', posX);

        const posY = document.getElementById('posY').value;
        if (posY.trim() !== '') formData.append('posY', posY);

        const posZ = document.getElementById('posZ').value;
        if (posZ.trim() !== '') formData.append('posZ', posZ);

        const escala = document.getElementById('escala').value;
        if (escala.trim() !== '') formData.append('escala', escala);

        const rotacionY = document.getElementById('rotacionY').value;
        if (rotacionY.trim() !== '') formData.append('rotacionY', rotacionY);

        try {
            const url = `${API_BASE_URL}/api/museo/piezas`;
            const response = await fetch(url, { method: 'POST', body: formData });
            if (!response.ok) {
                let errorMsg = `Server error: ${response.status} ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    errorMsg = errorData.error || errorData.message || errorMsg;
                } catch(e) { /* response not JSON */ }
                throw new Error(errorMsg);
            }
            const result = await response.json();
            alert(result.message || result.mensaje || 'Piece uploaded successfully!'); // 'mensaje' from existing HTML
            fetchPieces(); // Refresh gallery
            if(uploadForm) uploadForm.reset();
            if(imagePreview) imagePreview.src = '#';
            if(imagePreviewContainer) imagePreviewContainer.style.display = 'none';
        } catch (error) {
            console.error('Error uploading piece:', error);
            alert(`Error uploading piece: ${error.message}. Ensure backend is running.`);
        }
    }

    async function handleDeletePiezaMuseo(event) {
        const piezaId = event.target.closest('.delete-button').getAttribute('data-id');
        if (!piezaId) {
            alert('Could not find piece ID to delete.');
            return;
        }
        if (!confirm(`Are you sure you want to delete museum piece with ID ${piezaId}? This action cannot be undone.`)) {
            return;
        }
        try {
            // Note: The API created expects /api/museo/piezas/{id}
            const url = `${API_BASE_URL}/api/museo/piezas/${piezaId}`;
            const response = await fetch(url, { method: 'DELETE' });
            if (!response.ok) {
                let errorMsg = `Server error: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMsg = errorData.error || errorData.message || errorMsg;
                } catch(e) { /* response not JSON */ }
                throw new Error(errorMsg);
            }
            const result = await response.json();
            alert(result.message || result.mensaje || 'Piece deleted successfully.'); // 'mensaje' from existing HTML
            fetchPieces(); // Refresh gallery
        } catch (error) {
            console.error('Error deleting museum piece:', error);
            alert(`Error deleting piece: ${error.message}`);
        }
    }

    // --- Rendering Functions ---
    function renderGallery(piecesArray) {
        if (!museumGalleryGrid || !noPiecesMessage) {
            console.error("Gallery grid or noPiecesMessage element not found for rendering.");
            return;
        }
        museumGalleryGrid.innerHTML = '';
        if (!piecesArray || piecesArray.length === 0) {
            noPiecesMessage.style.display = 'block';
            noPiecesMessage.textContent = 'No pieces have been added to the museum yet. Be the first to contribute!';
            return;
        }
        noPiecesMessage.style.display = 'none';

        piecesArray.forEach(pieza => {
            const card = document.createElement('div');
            card.classList.add('card', 'museum-piece-card');

            const img = document.createElement('img');
            // The API now returns full imagenUrl, so no need to prepend API_BASE_URL here
            // If imagenUrl was relative from API, then:
            // let imageUrl = pieza.imagenUrl;
            // if (API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:')) {
            //     imageUrl = `${API_BASE_URL}${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}`;
            // } else if (!API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:') && !imageUrl.startsWith('/')) {
            //    imageUrl = `/${imageUrl}`; // Ensure leading slash if no base_url and relative
            // }
            img.src = pieza.imagenUrl;
            img.alt = pieza.altText || `Image of ${pieza.titulo}`;
            img.onerror = function() {
                this.onerror=null;
                this.src='https://placehold.co/300x200/D2B48C/2c1d12?text=Image+not+available';
                this.alt = `Error loading image for ${pieza.titulo}`;
            };
            img.addEventListener('click', () => openModal(img.src, `${pieza.titulo} - by ${pieza.autor || 'Unknown'}`));

            const cardContent = document.createElement('div');
            cardContent.classList.add('card-content');

            const titleH3 = document.createElement('h3');
            titleH3.innerHTML = `<i class="fas fa-monument"></i> ${pieza.titulo}`;

            const authorP = document.createElement('p');
            authorP.classList.add('piece-author');
            authorP.innerHTML = `<strong>Contributed by:</strong> ${pieza.autor || 'Anonymous'}`;

            const descriptionP = document.createElement('p');
            descriptionP.classList.add('piece-description');
            const descText = pieza.descripcion || "";
            descriptionP.textContent = descText.substring(0, 120) + (descText.length > 120 ? '...' : '');

            // Delete button (assuming admin role or for local management)
            // The visibility of this button might depend on user roles in a real app.
            // The new API requires admin auth for deletion.
            const deleteButton = document.createElement('button');
            deleteButton.classList.add('delete-button', 'btn-condado', 'btn-condado-peligro');
            deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
            deleteButton.setAttribute('data-id', pieza.id); // Ensure 'id' is part of the piece data from API
            deleteButton.addEventListener('click', handleDeletePiezaMuseo);

            cardContent.appendChild(titleH3);
            cardContent.appendChild(authorP);
            cardContent.appendChild(descriptionP);
            cardContent.appendChild(deleteButton); // Add delete button

            card.appendChild(img);
            card.appendChild(cardContent);
            museumGalleryGrid.appendChild(card);
        });
    }

    // --- Modal Logic ---
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

    // Close modal on window click outside content
    window.onclick = function(event) {
        if (event.target == modal) {
            if(modal) modal.style.display = "none";
        }
    }

    // --- Image Preview Logic ---
    if (piezaImagenInput) {
        piezaImagenInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    alert('Image is too large. Maximum size is 2MB.');
                    this.value = ""; // Reset file input
                    if(imagePreview) imagePreview.src = '#';
                    if(imagePreviewContainer) imagePreviewContainer.style.display = 'none';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    if(imagePreview) imagePreview.src = e.target.result;
                    if(imagePreviewContainer) imagePreviewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                if(imagePreview) imagePreview.src = '#';
                if(imagePreviewContainer) imagePreviewContainer.style.display = 'none';
            }
        });
    }

    // --- Sample Data (Fallback) ---
    function loadSamplePieces() {
        console.warn("Loading sample pieces as fallback.");
        return [
            { id: 'sample_m1', titulo: "Estela Funeraria (Ejemplo Museo)", descripcion: "Antigua estela de piedra con grabados, encontrada en las cercanías de un camino histórico.", autor: "Arqueología Local", imagenUrl: "/imagenes/museo_estela_ejemplo.jpg", altText: "Estela funeraria de piedra" },
            { id: 'sample_m2', titulo: "Capitel Románico (Ejemplo Museo)", descripcion: "Detalle de un capitel con figuras zoomorfas, perteneciente a una ermita desaparecida.", autor: "Patrimonio Olvidado", imagenUrl: "/imagenes/museo_capitel_ejemplo.jpg", altText: "Capitel románico con figuras" }
        ];
    }

    // --- Initializations & Event Listeners ---
    if (uploadForm) {
        uploadForm.addEventListener('submit', handleSubmitUploadForm);
    } else {
        console.warn("Upload form not found.");
    }

    fetchPieces(); // Initial fetch of pieces for the gallery
});
