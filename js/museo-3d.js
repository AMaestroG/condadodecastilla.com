// Ensure Three.js is loaded
if (typeof THREE === 'undefined') {
    console.error('Three.js not loaded. Make sure the CDN link is correct in museo.html.');
} else {
    console.log('Three.js loaded successfully. Version: ' + THREE.REVISION);

    let scene, camera, renderer, controls;
    let composer, bloomPass; // For post-processing
    const textureLoader = new THREE.TextureLoader();
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let clickableMeshes = [];
    let isCameraAnimating = false;

    let hoveredExhibitor = null;
    const originalEmissiveColors = new Map();

    const API_BASE_URL = "";
    const PIEZAS_API_URL = `${API_BASE_URL}/api/museo/piezas`;

    const THEME_COLORS = { // Unchanged
        purpleEmperor: new THREE.Color("#4A0D67"), goldMain: new THREE.Color("#FFD700"), goldSecondary: new THREE.Color("#B8860B"),
        alabasterBg: new THREE.Color("#EAEBF0"), alabasterMedium: new THREE.Color("#D8D9E0"), textColor: new THREE.Color("#2c1d12"),
        darkFloor: new THREE.Color("#3a2d23"), darkBackground: new THREE.Color("#1c1824"), glass: new THREE.Color(0xd0e0ff),
        hoverEmissive: new THREE.Color(0x444400)
    };

    const galleryWidth = 22, galleryHeight = 5, galleryDepth = 35, wallThickness = 0.5;
    let roomSize = { width: galleryWidth, height: galleryHeight, depth: galleryDepth };

    let overlayContainerElement, overlayTituloElement, overlayAutorElement, overlayDescripcionElement, overlayCerrarElement; // Unchanged

    function getOverlayElements() { /* Unchanged */
        overlayContainerElement = document.getElementById('pieza-info-overlay');
        overlayTituloElement = document.getElementById('overlay-titulo');
        overlayAutorElement = document.getElementById('overlay-autor');
        overlayDescripcionElement = document.getElementById('overlay-descripcion');
        overlayCerrarElement = document.getElementById('overlay-cerrar');
        if (overlayCerrarElement) overlayCerrarElement.addEventListener('click', hidePiezaInfoOverlay);
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && overlayContainerElement && overlayContainerElement.classList.contains('visible')) hidePiezaInfoOverlay();
        });
    }
    function showPiezaInfoOverlay(pieza) { /* Unchanged */
        if (overlayContainerElement && overlayTituloElement && overlayAutorElement && overlayDescripcionElement) {
            overlayTituloElement.textContent = pieza.titulo || "Título no disponible";
            overlayAutorElement.textContent = 'Aportado por: ' + (pieza.autor || 'Desconocido');
            overlayDescripcionElement.textContent = pieza.descripcion || "Descripción no disponible.";
            overlayContainerElement.classList.add('visible');
        } else {
            console.error("Overlay elements not found.");
            alert(`Pieza: ${pieza.titulo}\nAutor: ${pieza.autor || 'Desconocido'}\nDescripción: ${pieza.descripcion || 'No disponible'}`);
        }
    }
    function hidePiezaInfoOverlay() { /* Unchanged */
        if (overlayContainerElement) overlayContainerElement.classList.remove('visible');
    }
    function createVitrinaPlaceholder() { /* Unchanged */
        const group = new THREE.Group();
        const vitrinaWidth = 1.2, vitrinaHeight = 1.8, vitrinaDepth = 0.6;
        const baseHeight = 0.7; const glassThickness = 0.02;
        const baseMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.textColor, roughness: 0.7, metalness: 0.2, emissive: 0x000000 });
        const baseGeometry = new THREE.BoxGeometry(vitrinaWidth, baseHeight, vitrinaDepth);
        const baseMesh = new THREE.Mesh(baseGeometry, baseMaterial);
        baseMesh.position.y = baseHeight / 2; baseMesh.castShadow = true; baseMesh.receiveShadow = true; baseMesh.name = 'baseMesh';
        group.add(baseMesh);
        const glassMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.glass, transparent: true, opacity: 0.15, roughness: 0.1, metalness: 0.2, side: THREE.DoubleSide });
        const glassPanelHeight = vitrinaHeight - baseHeight;
        const fbGlassGeom = new THREE.PlaneGeometry(vitrinaWidth - glassThickness * 2, glassPanelHeight);
        const frontGlass = new THREE.Mesh(fbGlassGeom, glassMaterial);
        frontGlass.position.set(0, baseHeight + glassPanelHeight / 2, vitrinaDepth / 2 - glassThickness / 2); group.add(frontGlass);
        const sideGlassGeom = new THREE.PlaneGeometry(vitrinaDepth - glassThickness*2, glassPanelHeight);
        const leftGlass = new THREE.Mesh(sideGlassGeom, glassMaterial);
        leftGlass.position.set(-vitrinaWidth / 2 + glassThickness/2, baseHeight + glassPanelHeight / 2, 0); leftGlass.rotation.y = Math.PI / 2; group.add(leftGlass);
        const rightGlass = new THREE.Mesh(sideGlassGeom, glassMaterial);
        rightGlass.position.set(vitrinaWidth / 2 - glassThickness/2, baseHeight + glassPanelHeight / 2, 0); rightGlass.rotation.y = -Math.PI / 2; group.add(rightGlass);
        const topGlassGeom = new THREE.PlaneGeometry(vitrinaWidth - glassThickness*2, vitrinaDepth - glassThickness*2);
        const topGlass = new THREE.Mesh(topGlassGeom, glassMaterial);
        topGlass.position.y = vitrinaHeight - glassThickness/2; topGlass.rotation.x = Math.PI/2; group.add(topGlass);
        return group;
    }
    function createPedestalPlaceholder() { /* Unchanged */
        const pedestalRadius = 0.3; const pedestalHeight = 1.0;
        const pedestalMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.alabasterMedium, roughness: 0.6, metalness: 0.1, emissive: 0x000000 });
        const pedestalGeometry = new THREE.CylinderGeometry(pedestalRadius, pedestalRadius * 0.9, pedestalHeight, 32);
        const pedestalMesh = new THREE.Mesh(pedestalGeometry, pedestalMaterial);
        pedestalMesh.position.y = pedestalHeight / 2; pedestalMesh.castShadow = true; pedestalMesh.receiveShadow = true; pedestalMesh.name = 'baseMesh';
        return pedestalMesh;
    }

    function initPostprocessing() {
        if (typeof THREE.EffectComposer === 'undefined' ||
            typeof THREE.RenderPass === 'undefined' ||
            typeof THREE.UnrealBloomPass === 'undefined') {
            console.error('Postprocessing classes not found. Ensure EffectComposer, RenderPass, UnrealBloomPass and their dependencies (shaders) are loaded via script tags in HTML.');
            return;
        }

        const container = document.getElementById('museo-3d-container');
        let currentWidth = container ? container.clientWidth : window.innerWidth;
        let currentHeight = container ? container.clientHeight : window.innerHeight;

        composer = new THREE.EffectComposer(renderer);
        const renderPass = new THREE.RenderPass(scene, camera);
        composer.addPass(renderPass);

        bloomPass = new THREE.UnrealBloomPass(new THREE.Vector2(currentWidth, currentHeight), 0.6, 0.5, 0.2); // strength, radius, threshold
        // bloomPass.threshold = 0.20; // Lower to make more things bloom (default 0.85)
        // bloomPass.strength = 0.5;   // Intensity (default 1.5)
        // bloomPass.radius = 0.5;    // Bloom radius (default 0.4)
        composer.addPass(bloomPass);
        console.log("Postprocessing initialized with UnrealBloomPass.");
    }


    function init() {
        getOverlayElements();
        scene = new THREE.Scene();
        scene.background = THEME_COLORS.darkBackground;
        scene.fog = new THREE.Fog(THEME_COLORS.darkBackground, galleryDepth * 0.7, galleryDepth * 1.8);
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.0;
        const container = document.getElementById('museo-3d-container');
        if (container) {
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);
        } else {
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);
        }
        const aspect = container ? container.clientWidth / container.clientHeight : window.innerWidth / window.innerHeight;
        camera = new THREE.PerspectiveCamera(50, aspect, 0.1, 1000);
        camera.position.set(galleryWidth / 4, galleryHeight / 1.8, galleryDepth / 2 - 3);

        scene.add(new THREE.AmbientLight(0xffefd5, 0.15));
        const hemisphereLight = new THREE.HemisphereLight(0xB1E1FF, THEME_COLORS.darkFloor, 0.4);
        hemisphereLight.position.set(0, galleryHeight, 0);
        scene.add(hemisphereLight);
        const mainLight = new THREE.DirectionalLight(0xffefd5, 0.8);
        mainLight.position.set(galleryWidth*0.4, galleryHeight * 1.2, galleryDepth*0.3);
        mainLight.target.position.set(0, galleryHeight/3, 0);
        mainLight.castShadow = true; // Main light casts shadows
        mainLight.shadow.mapSize.width = 2048; mainLight.shadow.mapSize.height = 2048;
        mainLight.shadow.camera.near = 1; mainLight.shadow.camera.far = galleryWidth * 1.5;
        mainLight.shadow.camera.left = -galleryWidth / 1.5; mainLight.shadow.camera.right = galleryWidth / 1.5;
        mainLight.shadow.camera.top = galleryDepth / 1.5; mainLight.shadow.camera.bottom = -galleryDepth / 1.5;
        mainLight.shadow.bias = -0.001;
        scene.add(mainLight); scene.add(mainLight.target);
        const fillLight = new THREE.DirectionalLight(0xffefd5, 0.3);
        fillLight.position.set(-galleryWidth*0.4, galleryHeight * 0.8, -galleryDepth*0.3);
        fillLight.target.position.set(0, galleryHeight/3, 0);
        scene.add(fillLight); scene.add(fillLight.target);

        createGalleryRoom();
        setupOrbitControls();
        initPostprocessing(); // Initialize post-processing stack
        fetchAndDisplayPieces();
        renderer.domElement.addEventListener('click', onMouseClick, false);
        renderer.domElement.addEventListener('mousemove', onMouseMove, false);
        window.addEventListener('resize', onWindowResize, false);
        onWindowResize();
        animate();
    }

    function createGalleryRoom() { /* Unchanged */
        const floorMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.darkFloor, roughness: 0.8, metalness: 0.1 });
        const wallMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.alabasterMedium, roughness: 0.9, metalness: 0.05 });
        const ceilingMaterial = new THREE.MeshStandardMaterial({ color: 0xf0f0f0, roughness: 0.9, metalness: 0.1 });
        const floorGeometry = new THREE.PlaneGeometry(galleryWidth, galleryDepth);
        const floorMesh = new THREE.Mesh(floorGeometry, floorMaterial);
        floorMesh.rotation.x = -Math.PI / 2; floorMesh.receiveShadow = true; scene.add(floorMesh);
        const wallData = [
            { size: [galleryWidth, galleryHeight, wallThickness], position: [0, galleryHeight / 2, -galleryDepth / 2 + wallThickness/2] },
            { size: [galleryDepth, galleryHeight, wallThickness], position: [-galleryWidth / 2 + wallThickness/2, galleryHeight / 2, 0], rotationY: Math.PI / 2 },
            { size: [galleryDepth, galleryHeight, wallThickness], position: [galleryWidth / 2 - wallThickness/2, galleryHeight / 2, 0], rotationY: -Math.PI / 2 },
        ];
        wallData.forEach(data => {
            const wallGeom = new THREE.BoxGeometry(...data.size);
            const wallMesh = new THREE.Mesh(wallGeom, wallMaterial);
            wallMesh.position.set(data.position[0], data.position[1], data.position[2]);
            if (data.rotationY) wallMesh.rotation.y = data.rotationY;
            wallMesh.castShadow = true; wallMesh.receiveShadow = true; scene.add(wallMesh);
        });
        const ceilingGeometry = new THREE.PlaneGeometry(galleryWidth, galleryDepth);
        const ceilingMesh = new THREE.Mesh(ceilingGeometry, ceilingMaterial);
        ceilingMesh.rotation.x = Math.PI / 2; ceilingMesh.position.y = galleryHeight;
        ceilingMesh.receiveShadow = true; scene.add(ceilingMesh);
    }

    function setupOrbitControls() { /* Unchanged */
        if (!THREE.OrbitControls) { console.error("THREE.OrbitControls is not available."); return; }
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true; controls.dampingFactor = 0.05; controls.screenSpacePanning = true;
        controls.minDistance = 1; controls.maxDistance = galleryDepth * 0.7;
        controls.target.set(0, galleryHeight / 2.8, 0);
        controls.maxPolarAngle = Math.PI * 0.8; controls.minPolarAngle = Math.PI * 0.2;
        controls.update();
    }

    function animate() {
        requestAnimationFrame(animate);
        if (controls && controls.update) {
            controls.update();
        }
        // Use composer if available, otherwise fallback to renderer
        if (composer) {
            composer.render();
        } else {
            renderer.render(scene, camera);
        }
    }

    function onWindowResize() {
        const container = document.getElementById('museo-3d-container');
        let currentWidth = container ? container.clientWidth : window.innerWidth;
        let currentHeight = container ? container.clientHeight : window.innerHeight;
        camera.aspect = (currentHeight === 0) ? currentWidth / 600 : currentWidth / currentHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(currentWidth, currentHeight);
        if (composer) { // Resize composer and bloomPass
            composer.setSize(currentWidth, currentHeight);
        }
        if (bloomPass) {
             // For UnrealBloomPass, resolution is a Vector2.
             // The pass itself might handle this internally when composer is resized,
             // but explicitly setting can be safer if issues arise.
             // bloomPass.resolution.set(currentWidth, currentHeight); // Usually not needed if composer.setSize is called
        }
    }

    async function fetchAndDisplayPieces() { /* Unchanged */
        try {
            const response = await fetch(PIEZAS_API_URL);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const pieces = await response.json();
            if (pieces && pieces.length > 0) create3DRepresentations(pieces);
            else console.log("No museum pieces fetched.");
        } catch (error) {
            console.error('Error fetching museum pieces for 3D scene:', error);
        }
    }

    function create3DRepresentations(pieces) { /* Unchanged */
        const pieceImageBaseHeight = 1.0; const frameThickness = 0.05; const frameColor = THEME_COLORS.textColor;
        const pedestalHeight = 1.0; const pedestalRadius = 0.3;
        const vitrinaBaseHeight = 0.7; const vitrinaGlassHeight = 1.1; const vitrinaWidth = 1.0; const vitrinaDepth = 0.5;
        const exhibitorSpacing = 2.8; const wallMargin = 1.5;
        const exhibitorWallOffset = Math.max(vitrinaDepth, pedestalRadius * 2) / 2 + 0.1;
        const wallsForPieces = [
            { name: 'back', axis: 'x', positionZ: -galleryDepth / 2 + exhibitorWallOffset, rotationY: 0, availableLength: galleryWidth - wallMargin * 2 },
            { name: 'left', axis: 'z', positionX: -galleryWidth / 2 + exhibitorWallOffset, rotationY: Math.PI / 2, availableLength: galleryDepth - wallMargin * 2 },
            { name: 'right', axis: 'z', positionX: galleryWidth / 2 - exhibitorWallOffset, rotationY: -Math.PI / 2, availableLength: galleryDepth - wallMargin * 2, reverseOrder: true }
        ];
        let pieceIdxGlobal = 0;
        for (const wall of wallsForPieces) {
            if (pieceIdxGlobal >= pieces.length) break;
            let currentWallPos = -wall.availableLength / 2; let itemsOnThisWall = 0;
            for (let i = pieceIdxGlobal; i < pieces.length; i++) {
                const pieza = pieces[i]; if (!pieza.imagenUrl) continue;
                const useVitrina = i % 2 === 0;
                const currentExhibitorActualWidth = useVitrina ? vitrinaWidth : pedestalRadius * 2;
                let spaceRequiredForThisExhibitor = currentExhibitorActualWidth; let nextPotentialPosition = currentWallPos;
                if (itemsOnThisWall > 0) nextPotentialPosition += exhibitorSpacing;
                nextPotentialPosition += currentExhibitorActualWidth / 2;
                if (nextPotentialPosition > wall.availableLength / 2) break;
                if (itemsOnThisWall > 0) currentWallPos += exhibitorSpacing;
                currentWallPos += currentExhibitorActualWidth / 2;
                let exhibitorPlaceholder; let pieceYOffset;
                if (useVitrina) { exhibitorPlaceholder = createVitrinaPlaceholder(); pieceYOffset = vitrinaBaseHeight + vitrinaGlassHeight / 2;
                } else { exhibitorPlaceholder = createPedestalPlaceholder(); pieceYOffset = pedestalHeight + frameThickness; }
                const exhibitorClone = exhibitorPlaceholder.clone();
                let imageUrl = pieza.imagenUrl;
                if (API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:')) imageUrl = `${API_BASE_URL}${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}`;
                else if (!API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:') && !imageUrl.startsWith('/')) imageUrl = `/${imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl}`;
                textureLoader.load(imageUrl, (texture) => {
                    const aspectRatio = texture.image ? texture.image.width / texture.image.height : 1;
                    let pieceHeight = pieceImageBaseHeight; let pieceWidth = pieceHeight * aspectRatio;
                    const maxWidth = useVitrina ? vitrinaWidth - 0.2 : pedestalRadius * 2 - 0.1;
                    if (pieceWidth > maxWidth) { pieceWidth = maxWidth; pieceHeight = pieceWidth / aspectRatio; }
                    if (useVitrina && pieceHeight > vitrinaGlassHeight - 0.15) { pieceHeight = vitrinaGlassHeight - 0.15; pieceWidth = pieceHeight * aspectRatio; }
                    const pieceGroup = new THREE.Group(); pieceGroup.name = 'pieceImageGroup';
                    const frameMaterial = new THREE.MeshStandardMaterial({ color: frameColor, roughness: 0.5, metalness: 0.4 });
                    const imageFrameWidth = pieceWidth + 2 * frameThickness; const imageFrameHeight = pieceHeight + 2 * frameThickness;
                    const frameGeometry = new THREE.PlaneGeometry(imageFrameWidth, imageFrameHeight);
                    const frameMesh = new THREE.Mesh(frameGeometry, frameMaterial); frameMesh.position.z = -0.005; pieceGroup.add(frameMesh);
                    const imageGeometry = new THREE.PlaneGeometry(pieceWidth, pieceHeight);
                    const imageMaterial = new THREE.MeshStandardMaterial({ map: texture, side: THREE.DoubleSide, roughness: 0.7, metalness: 0.1 });
                    const imageMesh = new THREE.Mesh(imageGeometry, imageMaterial); imageMesh.castShadow = true; pieceGroup.add(imageMesh);
                    pieceGroup.position.y = pieceYOffset;
                    if(useVitrina) pieceGroup.position.z = 0;
                    else { pieceGroup.position.y += pieceHeight/2; pieceGroup.rotation.x = -Math.PI / 18; }
                    exhibitorClone.add(pieceGroup); exhibitorClone.userData = pieza;
                    exhibitorClone.rotation.y = wall.rotationY;
                    let finalPosOnAxis = wall.reverseOrder ? -currentWallPos : currentWallPos;
                    if (wall.axis === 'x') { exhibitorClone.position.x = finalPosOnAxis; exhibitorClone.position.z = wall.positionZ;
                    } else { exhibitorClone.position.z = finalPosOnAxis; exhibitorClone.position.x = wall.positionX; }
                    scene.add(exhibitorClone); clickableMeshes.push(exhibitorClone);
                }, undefined, (error) => console.error(`Error loading texture for ${pieza.titulo}:`, error));
                currentWallPos += currentExhibitorActualWidth / 2; itemsOnThisWall++; pieceIdxGlobal++;
                if (pieceIdxGlobal >= pieces.length) break;
            }
        }
    }

    function onMouseMove(event) { /* Unchanged */
        if (isCameraAnimating) {
            if (hoveredExhibitor) {
                const baseToRestore = hoveredExhibitor.getObjectByName('baseMesh');
                if (baseToRestore && baseToRestore.material && originalEmissiveColors.has(hoveredExhibitor.uuid)) {
                    baseToRestore.material.emissive.setHex(originalEmissiveColors.get(hoveredExhibitor.uuid));
                }
                originalEmissiveColors.delete(hoveredExhibitor.uuid);
                hoveredExhibitor = null;
                document.body.style.cursor = 'default';
            }
            return;
        }
        const container = renderer.domElement.parentElement; if (!container) return;
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(clickableMeshes, true);
        let intersectedParentGroup = null;
        if (intersects.length > 0) {
            let obj = intersects[0].object;
            while (obj && (!obj.userData || !obj.userData.titulo)) {
                if (!obj.parent || obj.parent === scene) { obj = null; break; }
                obj = obj.parent;
            }
            intersectedParentGroup = obj;
        }
        if (intersectedParentGroup) {
            if (hoveredExhibitor !== intersectedParentGroup) {
                if (hoveredExhibitor) {
                    const baseToRestore = hoveredExhibitor.getObjectByName('baseMesh');
                    if (baseToRestore && baseToRestore.material && originalEmissiveColors.has(hoveredExhibitor.uuid)) {
                        baseToRestore.material.emissive.setHex(originalEmissiveColors.get(hoveredExhibitor.uuid));
                        originalEmissiveColors.delete(hoveredExhibitor.uuid);
                    }
                }
                hoveredExhibitor = intersectedParentGroup;
                const baseMesh = hoveredExhibitor.getObjectByName('baseMesh');
                if (baseMesh && baseMesh.material) {
                    if (!originalEmissiveColors.has(hoveredExhibitor.uuid)) {
                         originalEmissiveColors.set(hoveredExhibitor.uuid, baseMesh.material.emissive.getHex());
                    }
                    baseMesh.material.emissive.setHex(THEME_COLORS.hoverEmissive.getHex());
                }
                document.body.style.cursor = 'pointer';
            }
        } else {
            if (hoveredExhibitor) {
                const baseToRestore = hoveredExhibitor.getObjectByName('baseMesh');
                if (baseToRestore && baseToRestore.material && originalEmissiveColors.has(hoveredExhibitor.uuid)) {
                    baseToRestore.material.emissive.setHex(originalEmissiveColors.get(hoveredExhibitor.uuid));
                    originalEmissiveColors.delete(hoveredExhibitor.uuid);
                }
                hoveredExhibitor = null;
            }
            document.body.style.cursor = 'default';
        }
    }

    function onMouseClick(event) { /* Unchanged (GSAP animation logic) */
        if (isCameraAnimating) return;
        const container = renderer.domElement.parentElement; if (!container) return;
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(clickableMeshes, true);
        if (intersects.length > 0) {
            let clickedParentGroup = intersects[0].object;
            while (clickedParentGroup && (!clickedParentGroup.userData || !clickedParentGroup.userData.titulo)) {
                if (!clickedParentGroup.parent || clickedParentGroup.parent === scene) { clickedParentGroup = null; break; }
                clickedParentGroup = clickedParentGroup.parent;
            }
            if (clickedParentGroup && clickedParentGroup.userData && clickedParentGroup.userData.titulo) {
                const piezaData = clickedParentGroup.userData;
                if (overlayContainerElement && overlayContainerElement.classList.contains('visible')) hidePiezaInfoOverlay();
                if (typeof gsap !== 'undefined') {
                    isCameraAnimating = true; controls.enabled = false;
                    const targetPosition = new THREE.Vector3(); clickedParentGroup.getWorldPosition(targetPosition);
                    let offsetDistance = 2.5; const cameraEndPosition = new THREE.Vector3();
                    const exhibitorForward = new THREE.Vector3(0, 0, 1);
                    exhibitorForward.applyQuaternion(clickedParentGroup.quaternion);
                    cameraEndPosition.copy(targetPosition).sub(exhibitorForward.multiplyScalar(offsetDistance));
                    cameraEndPosition.y = targetPosition.y + 0.8;
                    let targetFocusY = targetPosition.y;
                    const pieceImageGroup = clickedParentGroup.getObjectByName('pieceImageGroup');
                    if(pieceImageGroup){ const pieceWorldPos = new THREE.Vector3(); pieceImageGroup.getWorldPosition(pieceWorldPos); targetFocusY = pieceWorldPos.y; }
                    gsap.to(camera.position, {
                        x: cameraEndPosition.x, y: cameraEndPosition.y, z: cameraEndPosition.z, duration: 1.2, ease: "power2.inOut",
                        onUpdate: function() { if (controls) controls.update(); },
                        onComplete: function() {
                            isCameraAnimating = false; if (controls) controls.enabled = true;
                            const pieceToAnimate = clickedParentGroup.getObjectByName('pieceImageGroup');
                            if (pieceToAnimate && typeof gsap !== 'undefined') {
                                const originalScale = { x: 1, y: 1, z: 1 };
                                pieceToAnimate.scale.set(originalScale.x, originalScale.y, originalScale.z);
                                gsap.to(pieceToAnimate.scale, { x: originalScale.x * 1.08, y: originalScale.y * 1.08, duration: 0.3, yoyo: true, repeat: 1, ease: "power1.inOut" });
                            }
                            showPiezaInfoOverlay(piezaData);
                        }
                    });
                    gsap.to(controls.target, {
                        x: targetPosition.x, y: targetFocusY, z: targetPosition.z, duration: 1.2, ease: "power2.inOut",
                        onUpdate: function() { if (controls) controls.update(); }
                    });
                } else { showPiezaInfoOverlay(piezaData); }
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}
