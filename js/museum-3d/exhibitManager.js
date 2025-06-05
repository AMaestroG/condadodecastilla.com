// js/museum-3d/exhibitManager.js
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {};
}

MUSEUM_3D.ExhibitManager = (function() {
    'use strict';

    let scene;
    let camera; // For raycasting
    const clickableMeshes = []; // Meshes that can be interacted with (exhibits)
    const exhibitorBoundingBoxes = []; // For collision

    let focusedExhibitor = null;
    const originalExhibitorEmissives = new Map(); // To store original emissive color for hover effect
    const raycaster = new THREE.Raycaster();

    // Overlay DOM elements
    let overlayContainerElement, overlayTituloElement, overlayAutorElement, overlayDescripcionElement, overlayCerrarElement;

    function init(threeScene, threeCamera) {
        scene = threeScene;
        camera = threeCamera;
        getOverlayElementsDOM();
    }

    function getOverlayElementsDOM() {
        overlayContainerElement = document.getElementById('pieza-info-overlay');
        overlayTituloElement = document.getElementById('overlay-titulo');
        overlayAutorElement = document.getElementById('overlay-autor');
        overlayDescripcionElement = document.getElementById('overlay-descripcion');
        overlayCerrarElement = document.getElementById('overlay-cerrar');

        if (overlayCerrarElement) {
            overlayCerrarElement.addEventListener('click', hidePiezaInfoOverlay);
        }
        // Optional: Close overlay with Escape key, even if pointer is not locked
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (overlayContainerElement && overlayContainerElement.classList.contains('visible')) {
                    hidePiezaInfoOverlay();
                }
            }
        });
    }

    async function loadExhibits() {
        try {
            const response = await fetch(MUSEUM_3D.Utils.PIEZAS_API_URL);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
            }
            const pieces = await response.json();
            if (pieces && pieces.length > 0) {
                create3DRepresentations(pieces);
            } else {
                console.warn("No museum pieces fetched from API or API returned empty array.");
            }
        } catch (error) {
            console.error('Error fetching museum pieces:', error);
            // Optionally, load sample pieces or display an error in the 3D scene
        }
    }

    function create3DRepresentations(pieces) {
        pieces.forEach((pieza, index) => {
            // Determine exhibit type (e.g., vitrina for small objects, pedestal for statues/images on wall)
            // For now, let's use pedestals for all image-based exhibits.
            const exhibitType = pieza.escala || (pieza.pos_x || pieza.pos_y || pieza.pos_z) ? 'custom' : 'pedestal'; // crude type determination

            let exhibitMesh;
            if (exhibitType === 'vitrina') { // Vitrine logic can be more complex
                exhibitMesh = createVitrinaWithImage(pieza);
            } else { // Pedestal or custom placement
                exhibitMesh = createPedestalWithImage(pieza);
            }

            // Default placement strategy if no coordinates provided
            // This is a very basic placeholder logic for placement.
            // A more sophisticated system would use room dimensions, designated spots, etc.
            if (isNaN(parseFloat(pieza.pos_x)) || isNaN(parseFloat(pieza.pos_y)) || isNaN(parseFloat(pieza.pos_z))) {
                const mainRoomDims = MUSEUM_3D.Layout.getMainRoomDimensions();
                exhibitMesh.position.set(
                    mainRoomDims.x - mainRoomDims.width / 2 + 2 + (index % 5) * 3, // Arrange along one wall
                    (exhibitMesh.name === 'pedestal' ? 0 : 0.75), // Pedestals sit on floor, vitrine items might be higher in base
                    mainRoomDims.z - mainRoomDims.depth / 2 + 3
                );
            } else {
                exhibitMesh.position.set(
                    parseFloat(pieza.pos_x),
                    parseFloat(pieza.pos_y) + (exhibitMesh.name === 'pedestal' ? 0 : 0.75), // Adjust Y based on type if pedestal base is at y=0
                    parseFloat(pieza.pos_z)
                );
            }

            if (!isNaN(parseFloat(pieza.escala))) {
                exhibitMesh.scale.set(parseFloat(pieza.escala), parseFloat(pieza.escala), parseFloat(pieza.escala));
            }
             if (!isNaN(parseFloat(pieza.rotacion_y))) {
                exhibitMesh.rotation.y = parseFloat(pieza.rotacion_y) * (Math.PI / 180); // Assuming degrees
            }


            exhibitMesh.userData = pieza; // Store all piece data for interaction
            scene.add(exhibitMesh);
            clickableMeshes.push(exhibitMesh);

            // Add bounding box for collision
            exhibitMesh.updateMatrixWorld(true);
            const exhibitorBox = new THREE.Box3().setFromObject(exhibitMesh);
            exhibitorBoundingBoxes.push({box: exhibitorBox, name: `exhibit_${pieza.id || index}`});

            if (MUSEUM_3D.Utils.DEBUG_MODE) {
                const helper = new THREE.Box3Helper(exhibitorBox, 0xff00ff); // Magenta for exhibits
                scene.add(helper);
            }
        });
        // Update collision boxes in PlayerControls
        if (MUSEUM_3D.PlayerControls && MUSEUM_3D.PlayerControls.setCollisionBoundingBoxes) {
             MUSEUM_3D.PlayerControls.setCollisionBoundingBoxes(
                MUSEUM_3D.Layout.getWallBoundingBoxes ? MUSEUM_3D.Layout.getWallBoundingBoxes() : [],
                exhibitorBoundingBoxes
            );
        }
    }

    function createPedestalWithImage(pieza) {
        const group = new THREE.Group();
        group.name = 'pedestal_group';

        const pedestalHeight = 1.0;
        const pedestalRadius = 0.3;
        const pedestalMaterial = new THREE.MeshStandardMaterial({
            color: MUSEUM_3D.Utils.THEME_COLORS.alabasterMedium,
            roughness: 0.6, metalness: 0.1
        });
        const pedestalGeometry = new THREE.CylinderGeometry(pedestalRadius, pedestalRadius * 0.9, pedestalHeight, 32);
        const pedestalMesh = new THREE.Mesh(pedestalGeometry, pedestalMaterial);
        pedestalMesh.position.y = pedestalHeight / 2; // Sits on the floor
        pedestalMesh.castShadow = true;
        pedestalMesh.receiveShadow = true;
        pedestalMesh.name = 'baseMesh'; // For hover effect
        group.add(pedestalMesh);

        const imagePlaneWidth = 1.0; const imagePlaneHeight = 0.75;
        const imageMaterial = new THREE.MeshStandardMaterial({
            color: 0xffffff,
            map: MUSEUM_3D.Utils.loadTexture(pieza.imagenUrl,
                (texture) => { imageMaterial.needsUpdate = true; },
                () => { console.error(`Failed to load texture: ${pieza.imagenUrl}`); imageMaterial.map = createFallbackTexture(); }
            ),
            side: THREE.DoubleSide,
            emissive: 0x111111, // Slight self-illumination if needed
            roughness: 0.8,
            metalness: 0.1
        });
        const imageGeometry = new THREE.PlaneGeometry(imagePlaneWidth, imagePlaneHeight);
        const imageMesh = new THREE.Mesh(imageGeometry, imageMaterial);
        imageMesh.position.y = pedestalHeight + imagePlaneHeight / 2 + 0.1; // Above pedestal
        imageMesh.castShadow = true;
        imageMesh.name = 'imageMesh';
        group.add(imageMesh);

        group.userData = pieza; // Attach piece data to the group
        return group;
    }

    function createVitrinaWithImage(pieza) {
        // This is a simplified version. A real vitrina would have glass, frame, etc.
        const group = new THREE.Group();
        group.name = 'vitrina_group';

        const baseWidth = 1.2, baseHeight = 0.7, baseDepth = 0.6;
        const baseMaterial = new THREE.MeshStandardMaterial({
            color: MUSEUM_3D.Utils.THEME_COLORS.textColor,
            roughness: 0.7, metalness: 0.2
        });
        const baseGeometry = new THREE.BoxGeometry(baseWidth, baseHeight, baseDepth);
        const baseMesh = new THREE.Mesh(baseGeometry, baseMaterial);
        baseMesh.position.y = baseHeight / 2; // Sits on the floor
        baseMesh.castShadow = true;
        baseMesh.receiveShadow = true;
        baseMesh.name = 'baseMesh'; // For hover effect
        group.add(baseMesh);

        // Display image inside/on top of vitrine base
        const imagePlaneWidth = baseWidth * 0.8; const imagePlaneHeight = baseDepth * 0.8; // Smaller image
        const imageMaterial = new THREE.MeshStandardMaterial({
            color: 0xffffff,
            map: MUSEUM_3D.Utils.loadTexture(pieza.imagenUrl, null, () => { imageMaterial.map = createFallbackTexture(); }),
            side: THREE.DoubleSide,
            emissive: 0x222222,
            roughness: 0.7
        });
        const imageGeometry = new THREE.PlaneGeometry(imagePlaneWidth, imagePlaneHeight);
        const imageMesh = new THREE.Mesh(imageGeometry, imageMaterial);
        // Position image on top of the base, slightly tilted
        imageMesh.position.y = baseHeight + 0.01;
        imageMesh.rotation.x = -Math.PI / 2.2; // Tilt back slightly
        imageMesh.position.z = -baseDepth * 0.1; // Move slightly forward on the base
        imageMesh.castShadow = true;
        imageMesh.name = 'imageMesh';
        group.add(imageMesh);

        group.userData = pieza;
        return group;
    }

    function createFallbackTexture() {
        const canvas = document.createElement('canvas');
        canvas.width = 128; canvas.height = 128;
        const context = canvas.getContext('2d');
        context.fillStyle = '#AAAAAA'; context.fillRect(0, 0, 128, 128);
        context.fillStyle = '#FFFFFF'; context.textAlign = 'center'; context.font = '16px Arial';
        context.fillText('Error', 64, 70);
        return new THREE.CanvasTexture(canvas);
    }


    function updateInteractionRaycaster(currentCamera) {
        if (!currentCamera) return;
        camera = currentCamera; // Ensure camera is up-to-date

        raycaster.setFromCamera({ x: 0, y: 0 }, camera); // Ray from center of screen
        const intersects = raycaster.intersectObjects(clickableMeshes, true); // true for recursive

        let newFocusedExhibitor = null;
        if (intersects.length > 0) {
            let intersectedObject = intersects[0].object;
            // Traverse up to find the group that holds userData (the exhibit itself)
            while (intersectedObject && !intersectedObject.userData.id) {
                if (intersectedObject.parent) {
                    intersectedObject = intersectedObject.parent;
                } else { // Should not happen if structure is correct
                    intersectedObject = null;
                    break;
                }
            }

            if (intersectedObject && intersectedObject.userData.id && intersects[0].distance <= MUSEUM_3D.Utils.INTERACTION_DISTANCE) {
                newFocusedExhibitor = intersectedObject;
            }
        }

        if (focusedExhibitor && focusedExhibitor !== newFocusedExhibitor) {
            // Clear old focus
            const baseMesh = focusedExhibitor.getObjectByName('baseMesh') || focusedExhibitor.getObjectByName('imageMesh');
            if (baseMesh && baseMesh.material && originalExhibitorEmissives.has(focusedExhibitor.uuid)) {
                baseMesh.material.emissive.setHex(originalExhibitorEmissives.get(focusedExhibitor.uuid));
                originalExhibitorEmissives.delete(focusedExhibitor.uuid);
            }
        }

        if (newFocusedExhibitor && newFocusedExhibitor !== focusedExhibitor) {
            // Set new focus
            const baseMesh = newFocusedExhibitor.getObjectByName('baseMesh') || newFocusedExhibitor.getObjectByName('imageMesh');
            if (baseMesh && baseMesh.material) {
                if (!originalExhibitorEmissives.has(newFocusedExhibitor.uuid)) {
                     originalExhibitorEmissives.set(newFocusedExhibitor.uuid, baseMesh.material.emissive.getHex());
                }
                baseMesh.material.emissive.set(MUSEUM_3D.Utils.THEME_COLORS.hoverEmissive);
            }
        }
        focusedExhibitor = newFocusedExhibitor;
    }

    function interactWithFocusedExhibit() {
        if (focusedExhibitor) {
            showPiezaInfoOverlay(focusedExhibitor.userData);
        }
    }

    function showPiezaInfoOverlay(pieza) {
        if (overlayContainerElement && overlayTituloElement && overlayAutorElement && overlayDescripcionElement) {
            overlayTituloElement.textContent = pieza.titulo || "Título no disponible";
            overlayAutorElement.textContent = 'Aportado por: ' + (pieza.autor || 'Desconocido');
            overlayDescripcionElement.textContent = pieza.descripcion || "Descripción no disponible.";
            overlayContainerElement.classList.add('visible');
        } else {
            console.error("Overlay elements not found. Cannot display piece info.");
            // Fallback alert if UI elements are missing
            alert(`Pieza: ${pieza.titulo}\nAutor: ${pieza.autor || 'Desconocido'}\nDescripción: ${pieza.descripcion || 'No disponible'}`);
        }
    }

    function hidePiezaInfoOverlay() {
        if (overlayContainerElement) {
            overlayContainerElement.classList.remove('visible');
        }
    }

    function clearFocus() {
        if (focusedExhibitor) {
            const baseMesh = focusedExhibitor.getObjectByName('baseMesh') || focusedExhibitor.getObjectByName('imageMesh');
            if (baseMesh && baseMesh.material && originalExhibitorEmissives.has(focusedExhibitor.uuid)) {
                baseMesh.material.emissive.setHex(originalExhibitorEmissives.get(focusedExhibitor.uuid));
                originalExhibitorEmissives.delete(focusedExhibitor.uuid);
            }
            focusedExhibitor = null;
        }
    }

    return {
        init,
        loadExhibits,
        updateInteractionRaycaster,
        interactWithFocusedExhibit,
        clearFocus,
        getExhibitorBoundingBoxes: () => exhibitorBoundingBoxes,
        // No direct show/hide overlay, it's handled via interactWithFocusedExhibit
    };
})();
