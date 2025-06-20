// js/museo-3d-main.js
document.addEventListener('DOMContentLoaded', () => {
    // Check if Three.js is loaded
    if (typeof THREE === 'undefined') {
        console.error('Three.js not loaded. Museo 3D cannot start.');
        const museumContainer = document.getElementById('museo-3d-container');
        if (museumContainer) {
            museumContainer.innerHTML = '<p class="text-center notice-error mt-20">Error: Three.js library not loaded. The 3D museum cannot be displayed.</p>';
        }
        // Hide pointer lock instructions if Three.js is missing
        const instructions = document.getElementById('pointer-lock-instructions');
        if (instructions) instructions.style.display = 'none';
        return;
    }

    // Initialize all modules in the correct order
    // MUSEUM_3D.Utils is an IIFE, so it's already initialized and its properties are available.
    // No explicit init needed unless it had one.

    // 1. SceneManager: Sets up scene, camera, renderer, lights, post-processing
    MUSEUM_3D.SceneManager.init();
    const scene = MUSEUM_3D.SceneManager.getScene();
    const camera = MUSEUM_3D.SceneManager.getCamera();
    const rendererDomElement = MUSEUM_3D.SceneManager.getDomElement();

    if (!scene || !camera || !rendererDomElement) {
        console.error("Scene, Camera, or Renderer DOM element not available from SceneManager. Museo 3D cannot continue.");
        return;
    }

    // 2. MuseumLayout: Creates static parts of the museum (walls, floor)
    MUSEUM_3D.Layout.init(scene); // Pass the scene to the Layout module
    MUSEUM_3D.Layout.create();
    if (MUSEUM_3D.SceneManager.updateLayoutDependentLights) {
        MUSEUM_3D.SceneManager.updateLayoutDependentLights();
    }
    const wallBoundingBoxes = MUSEUM_3D.Layout.getWallBoundingBoxes();

    // 3. PlayerControls: Sets up PointerLockControls, movement, collision
    MUSEUM_3D.PlayerControls.init(camera, rendererDomElement);
    // Add player's "head" (controlsFPS.getObject()) to the scene
    if (MUSEUM_3D.PlayerControls.getControls()) {
        scene.add(MUSEUM_3D.PlayerControls.getControls().getObject());
    } else {
        console.error("PlayerControls not properly initialized. Player object not added to scene.");
    }


    // 4. ExhibitManager: Fetches pieces, creates 3D representations, handles interactions
    MUSEUM_3D.ExhibitManager.init(scene, camera); // Pass scene and camera
    MUSEUM_3D.ExhibitManager.loadExhibits().then(() => {
        // After exhibits are loaded (or attempted to load), set all bounding boxes for collision
        const exhibitorBoundingBoxes = MUSEUM_3D.ExhibitManager.getExhibitorBoundingBoxes();
        MUSEUM_3D.PlayerControls.setCollisionBoundingBoxes(wallBoundingBoxes, exhibitorBoundingBoxes);
        // console.log("Exhibits loaded and collision boxes updated.");
    }).catch(error => {
        console.error("Error during exhibit loading process:", error);
        // Still set wall bounding boxes even if exhibits fail
        MUSEUM_3D.PlayerControls.setCollisionBoundingBoxes(wallBoundingBoxes, []);
    });


    // 5. Start the animation loop (which is inside SceneManager)
    MUSEUM_3D.SceneManager.startAnimationLoop();

    // console.log("Museo 3D initialized.");

    // Initial check for view state (show/hide 3D section based on 2D/3D toggle)
    // This assumes the 2D gallery script (museo-2d-gallery.js) might set the initial display style.
    // We need to ensure that if the 3D view is supposed to be active, it is visible.
    const museum3DSection = document.getElementById('museo-3d-section');
    const show3DMuseumBtn = document.getElementById('show-3d-museum-btn');

    if (museum3DSection) {
        // If the 3D button is disabled, it implies 3D view should be active.
        // Or, if no buttons exist, we might default to showing it (depends on overall page logic).
        if (show3DMuseumBtn && show3DMuseumBtn.disabled) {
            museum3DSection.style.display = 'block';
            // Trigger a resize in case the canvas was initialized while hidden
            window.dispatchEvent(new Event('resize'));
        } else if (!show3DMuseumBtn) { // If no toggle buttons, default to showing 3D view if it exists.
            // This case might not be desired if 2D is the primary default.
            // For now, let's assume the 2D script handles initial visibility.
            // If museum3DSection.style.display is 'none' due to 2D script, this won't override it.
        }
    }
});
