// Ensure Three.js is loaded
if (typeof THREE === 'undefined') {
    console.error('Three.js not loaded. Make sure the CDN link is correct in museo.html.');
} else {
    console.log('Three.js loaded successfully. Version: ' + THREE.REVISION);
    window.DEBUG_MODE = false; // Disable Box3Helpers for final output

    let scene, camera, renderer;
    let composer, bloomPass;
    const textureLoader = new THREE.TextureLoader();
    const raycaster = new THREE.Raycaster();
    let clickableMeshes = [];

    let controlsFPS;
    let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false;
    const playerVelocity = new THREE.Vector3();
    const playerDirection = new THREE.Vector3();
    const playerSpeed = 4.0;
    let prevTime = performance.now();
    const playerHeight = 1.7;
    const wallBoundingBoxes = [];
    const exhibitorBoundingBoxes = [];

    let focusedExhibitor = null;
    const originalExhibitorEmissives = new Map();
    const INTERACTION_DISTANCE = 3.5;

    const API_BASE_URL = "";
    const PIEZAS_API_URL = `${API_BASE_URL}/api/museo/piezas`;

    const THEME_COLORS = {
        purpleEmperor: new THREE.Color("#4A0D67"), goldMain: new THREE.Color("#FFD700"), goldSecondary: new THREE.Color("#B8860B"),
        alabasterBg: new THREE.Color("#EAEBF0"), alabasterMedium: new THREE.Color("#D8D9E0"), textColor: new THREE.Color("#2c1d12"),
        darkFloor: new THREE.Color("#3a2d23"), darkBackground: new THREE.Color("#1c1824"), glass: new THREE.Color(0xd0e0ff),
        hoverEmissive: new THREE.Color(0x505020),
        doorFrame: new THREE.Color("#5c4d3f")
    };

    const mainRoom = { name: 'MainRoom', x:0, y:0, z:0, width: 20, height: 5, depth: 25 };
    const corridorA = { name: 'CorridorA', width: 4, height: 3.5, depth: 10 };
    const room2 = { name: 'Room2', width: 15, height: 4.5, depth: 15 };
    const wallThickness = 0.2;
    let currentRoomSizeInfo = mainRoom;

    let overlayContainerElement, overlayTituloElement, overlayAutorElement, overlayDescripcionElement, overlayCerrarElement;
    let crosshairElement;

    function getOverlayElements() { /* Unchanged */  /* ... */ }
    function showPiezaInfoOverlay(pieza) { /* Unchanged */ /* ... */ }
    function hidePiezaInfoOverlay() { /* Unchanged */ /* ... */ }
    function createVitrinaPlaceholder() { /* Unchanged */ /* ... */ }
    function createPedestalPlaceholder() { /* Unchanged */ /* ... */ }
    function initPostprocessing() { /* Unchanged */ /* ... */ }
    function onKeyDown(event) { /* Unchanged */ /* ... */ }
    function onKeyUp(event) { /* Unchanged */ /* ... */ }
    function initPointerLockControls() { /* Unchanged */ /* ... */ }


    function createWall(width, height, depth, material, position, rotationY = 0, name = 'wall') {
        const wallGeometry = new THREE.BoxGeometry(width, height, depth);
        const wallMesh = new THREE.Mesh(wallGeometry, material);
        wallMesh.position.copy(position);
        if (rotationY !== 0) wallMesh.rotation.y = rotationY;
        wallMesh.castShadow = true;
        wallMesh.receiveShadow = true;
        scene.add(wallMesh);
        wallMesh.updateMatrixWorld(true);
        const wallBox = new THREE.Box3().setFromObject(wallMesh);
        wallBoundingBoxes.push({box: wallBox, name: `${name}_${wallBoundingBoxes.length}`});

        if (window.DEBUG_MODE) { // This will now be false
            const helper = new THREE.Box3Helper(wallBox, 0x00ffff);
            scene.add(helper);
        }
        return wallMesh;
    }

    function createDoorFrame(openingWidth, openingHeight, frameDepth, frameThickness, material, position, rotationY = 0) {
        const group = new THREE.Group();
        group.position.copy(position);
        if (rotationY) group.rotation.y = rotationY;

        const lintelGeo = new THREE.BoxGeometry(openingWidth + 2 * frameThickness, frameThickness, frameDepth);
        const lintel = new THREE.Mesh(lintelGeo, material);
        lintel.position.y = openingHeight / 2 + frameThickness / 2; // Assumes openingHeight is full height, lintel sits on top
        group.add(lintel);

        const jambGeo = new THREE.BoxGeometry(frameThickness, openingHeight + frameThickness, frameDepth);
        const jambLeft = new THREE.Mesh(jambGeo, material);
        jambLeft.position.set(-openingWidth / 2 - frameThickness / 2, 0, 0); // Centered at opening's Y mid-point
        group.add(jambLeft);

        const jambRight = new THREE.Mesh(jambGeo, material);
        jambRight.position.set(openingWidth / 2 + frameThickness / 2, 0, 0);
        group.add(jambRight);

        scene.add(group);
        return group;
    }

    function init() { /* Unchanged */ /* ... */ }
    function createMuseumLayout() { /* Unchanged */ /* ... */ }
    function animate(time) { /* Unchanged */ /* ... */ }
    function onWindowResize() { /* Unchanged */ /* ... */ }
    async function fetchAndDisplayPieces() { /* Unchanged */ /* ... */ }
    function create3DRepresentations(pieces) { /* Unchanged */ /* ... */ }
    function onMouseClickFPS(event) { /* Unchanged */ /* ... */ }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}

// Stubs for functions that are mostly unchanged or only have minor internal tweaks from previous step
function getOverlayElements() {
    overlayContainerElement = document.getElementById('pieza-info-overlay');
    overlayTituloElement = document.getElementById('overlay-titulo');
    overlayAutorElement = document.getElementById('overlay-autor');
    overlayDescripcionElement = document.getElementById('overlay-descripcion');
    overlayCerrarElement = document.getElementById('overlay-cerrar');
    crosshairElement = document.getElementById('crosshair');
    if (overlayCerrarElement) overlayCerrarElement.addEventListener('click', hidePiezaInfoOverlay);
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (overlayContainerElement && overlayContainerElement.classList.contains('visible')) hidePiezaInfoOverlay();
        }
    });
}
function showPiezaInfoOverlay(pieza) {
    if (overlayContainerElement && overlayTituloElement && overlayAutorElement && overlayDescripcionElement) {
        overlayTituloElement.textContent = pieza.titulo || "Título no disponible";
        overlayAutorElement.textContent = 'Aportado por: ' + (pieza.autor || 'Desconocido');
        overlayDescripcionElement.textContent = pieza.descripcion || "Descripción no disponible.";
        overlayContainerElement.classList.add('visible');
    } else { console.error("Overlay elements not found."); alert(`Pieza: ${pieza.titulo}\nAutor: ${pieza.autor || 'Desconocido'}\nDescripción: ${pieza.descripcion || 'No disponible'}`);}
}
function hidePiezaInfoOverlay() {
    if (overlayContainerElement) overlayContainerElement.classList.remove('visible');
}
function createVitrinaPlaceholder() {
    const group = new THREE.Group(); const vitrinaWidth = 1.2, vitrinaHeight = 1.8, vitrinaDepth = 0.6;
    const baseHeight = 0.7; const glassThickness = 0.02;
    const baseMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.textColor, roughness: 0.7, metalness: 0.2, emissive: 0x000000 });
    const baseGeometry = new THREE.BoxGeometry(vitrinaWidth, baseHeight, vitrinaDepth);
    const baseMesh = new THREE.Mesh(baseGeometry, baseMaterial); baseMesh.position.y = baseHeight / 2; baseMesh.castShadow = true; baseMesh.receiveShadow = true; baseMesh.name = 'baseMesh'; group.add(baseMesh);
    const glassMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.glass, transparent: true, opacity: 0.15, roughness: 0.1, metalness: 0.2, side: THREE.DoubleSide });
    const glassPanelHeight = vitrinaHeight - baseHeight;
    const fbGlassGeom = new THREE.PlaneGeometry(vitrinaWidth - glassThickness * 2, glassPanelHeight);
    const frontGlass = new THREE.Mesh(fbGlassGeom, glassMaterial); frontGlass.position.set(0, baseHeight + glassPanelHeight / 2, vitrinaDepth / 2 - glassThickness / 2); group.add(frontGlass);
    const sideGlassGeom = new THREE.PlaneGeometry(vitrinaDepth - glassThickness*2, glassPanelHeight);
    const leftGlass = new THREE.Mesh(sideGlassGeom, glassMaterial); leftGlass.position.set(-vitrinaWidth / 2 + glassThickness/2, baseHeight + glassPanelHeight / 2, 0); leftGlass.rotation.y = Math.PI / 2; group.add(leftGlass);
    const rightGlass = new THREE.Mesh(sideGlassGeom, glassMaterial); rightGlass.position.set(vitrinaWidth / 2 - glassThickness/2, baseHeight + glassPanelHeight / 2, 0); rightGlass.rotation.y = -Math.PI / 2; group.add(rightGlass);
    const topGlassGeom = new THREE.PlaneGeometry(vitrinaWidth - glassThickness*2, vitrinaDepth - glassThickness*2);
    const topGlass = new THREE.Mesh(topGlassGeom, glassMaterial); topGlass.position.y = vitrinaHeight - glassThickness/2; topGlass.rotation.x = Math.PI/2; group.add(topGlass);
    return group;
}
function createPedestalPlaceholder() {
    const pedestalRadius = 0.3; const pedestalHeight = 1.0;
    const pedestalMaterial = new THREE.MeshStandardMaterial({ color: THEME_COLORS.alabasterMedium, roughness: 0.6, metalness: 0.1, emissive: 0x000000 });
    const pedestalGeometry = new THREE.CylinderGeometry(pedestalRadius, pedestalRadius * 0.9, pedestalHeight, 32);
    const pedestalMesh = new THREE.Mesh(pedestalGeometry, pedestalMaterial); pedestalMesh.position.y = pedestalHeight / 2; pedestalMesh.castShadow = true; pedestalMesh.receiveShadow = true; pedestalMesh.name = 'baseMesh';
    return pedestalMesh;
}
function initPointerLockControls() {
    const instructions = document.getElementById('pointer-lock-instructions');
    if (!instructions) { console.error('Pointer lock instructions element not found.'); return; }
    if (typeof THREE.PointerLockControls === 'undefined') { console.error('THREE.PointerLockControls not found.'); instructions.innerHTML = 'Error: Controles no cargados.'; return; }
    controlsFPS = new THREE.PointerLockControls(camera, renderer.domElement); scene.add(controlsFPS.getObject());
    instructions.addEventListener('click', function () { controlsFPS.lock(); });
    controlsFPS.addEventListener('lock', function () {
        instructions.style.display = 'none'; if(crosshairElement) crosshairElement.style.display = 'block';
        document.addEventListener('keydown', onKeyDown); document.addEventListener('keyup', onKeyUp);
        moveForward = false; moveBackward = false; moveLeft = false; moveRight = false;
    });
    controlsFPS.addEventListener('unlock', function () {
        instructions.style.display = 'flex'; if(crosshairElement) crosshairElement.style.display = 'none';
        document.removeEventListener('keydown', onKeyDown); document.removeEventListener('keyup', onKeyUp);
        if (focusedExhibitor) {
            const baseMesh = focusedExhibitor.getObjectByName('baseMesh');
            if (baseMesh && baseMesh.material && originalExhibitorEmissives.has(focusedExhibitor.uuid)) { baseMesh.material.emissive.setHex(originalExhibitorEmissives.get(focusedExhibitor.uuid)); }
            originalExhibitorEmissives.delete(focusedExhibitor.uuid); focusedExhibitor = null;
        }
    });
    controlsFPS.getObject().position.set(mainRoom.x, playerHeight, mainRoom.z + mainRoom.depth / 2 - 3);
}
function onKeyDown(event) {
    switch (event.code) {
        case 'KeyW': case 'ArrowUp': moveForward = true; break;
        case 'KeyA': case 'ArrowLeft': moveLeft = true; break;
        case 'KeyS': case 'ArrowDown': moveBackward = true; break;
        case 'KeyD': case 'ArrowRight': moveRight = true; break;
        case 'KeyE': if (focusedExhibitor && controlsFPS.isLocked) showPiezaInfoOverlay(focusedExhibitor.userData); break;
    }
}
function onKeyUp(event) {
    switch (event.code) {
        case 'KeyW': case 'ArrowUp': moveForward = false; break;
        case 'KeyA': case 'ArrowLeft': moveLeft = false; break;
        case 'KeyS': case 'ArrowDown': moveBackward = false; break;
        case 'KeyD': case 'ArrowRight': moveRight = false; break;
    }
}
function initPostprocessing() {
    if (typeof THREE.EffectComposer === 'undefined' || typeof THREE.RenderPass === 'undefined' || typeof THREE.UnrealBloomPass === 'undefined') { console.error('Postprocessing classes not found.'); return; }
    const container = document.getElementById('museo-3d-container');
    let currentWidth = container ? container.clientWidth : window.innerWidth; let currentHeight = container ? container.clientHeight : window.innerHeight;
    composer = new THREE.EffectComposer(renderer);
    const renderPass = new THREE.RenderPass(scene, camera); composer.addPass(renderPass);
    bloomPass = new THREE.UnrealBloomPass(new THREE.Vector2(currentWidth, currentHeight), 0.6, 0.5, 0.2); composer.addPass(bloomPass);
}
function init() {
    getOverlayElements();
    scene = new THREE.Scene();
    scene.background = THEME_COLORS.darkBackground;
    scene.fog = new THREE.Fog(THEME_COLORS.darkBackground, Math.max(mainRoom.depth, room2.depth) * 0.8, Math.max(mainRoom.depth, room2.depth) * 2.5);
    const container = document.getElementById('museo-3d-container');
    const aspect = container ? container.clientWidth / container.clientHeight : window.innerWidth / window.innerHeight;
    camera = new THREE.PerspectiveCamera(75, aspect, 0.1, 1000);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.shadowMap.enabled = true; renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping; renderer.toneMappingExposure = 0.9;
    if (container) { renderer.setSize(container.clientWidth, container.clientHeight); container.appendChild(renderer.domElement);
    } else { renderer.setSize(window.innerWidth, window.innerHeight); document.body.appendChild(renderer.domElement); }
    scene.add(new THREE.AmbientLight(0xffefd5, 0.12));
    const hemisphereLight = new THREE.HemisphereLight(0xB1E1FF, THEME_COLORS.darkFloor, 0.25);
    hemisphereLight.position.set(0, Math.max(mainRoom.height, room2.height, corridorA.height), 0); scene.add(hemisphereLight);
    const mainRoomLight = new THREE.DirectionalLight(0xffefd5, 0.6);
    mainRoomLight.position.set(mainRoom.x + mainRoom.width*0.3, mainRoom.y + mainRoom.height * 1.5, mainRoom.z + mainRoom.depth*0.1);
    mainRoomLight.target.position.set(mainRoom.x, mainRoom.y + mainRoom.height/3, mainRoom.z);
    mainRoomLight.castShadow = true; mainRoomLight.shadow.mapSize.width = 2048; mainRoomLight.shadow.mapSize.height = 2048;
    mainRoomLight.shadow.camera.near = 1; mainRoomLight.shadow.camera.far = Math.max(mainRoom.width, mainRoom.depth) * 2;
    mainRoomLight.shadow.camera.left = -mainRoom.width * 0.75; mainRoomLight.shadow.camera.right = mainRoom.width * 0.75;
    mainRoomLight.shadow.camera.top = mainRoom.depth * 0.75; mainRoomLight.shadow.camera.bottom = -mainRoom.depth * 0.75;
    mainRoomLight.shadow.bias = -0.001; scene.add(mainRoomLight); scene.add(mainRoomLight.target);
    const corridorAPosXCenter = mainRoom.x + mainRoom.width / 2 + corridorA.depth / 2;
    const corridorLight = new THREE.PointLight(THEME_COLORS.goldMain, 0.6, corridorA.depth * 1.1, 1.5);
    corridorLight.position.set(corridorAPosXCenter, mainRoom.y + corridorA.height * 0.8, mainRoom.z);
    corridorLight.castShadow = false; scene.add(corridorLight);
    const room2PosXCenter = mainRoom.x + mainRoom.width / 2 + corridorA.depth + wallThickness + room2.width / 2;
    const room2Light = new THREE.SpotLight(0xfff0e0, 0.5, room2.width * 1.8, Math.PI / 3.8, 0.2, 1.0);
    room2Light.position.set(room2PosXCenter, mainRoom.y + room2.height * 0.95, mainRoom.z);
    room2Light.target.position.set(room2PosXCenter, mainRoom.y, mainRoom.z);
    room2Light.castShadow = false; scene.add(room2Light); scene.add(room2Light.target);
    createMuseumLayout(); initPointerLockControls(); initPostprocessing();
    fetchAndDisplayPieces();
    renderer.domElement.addEventListener('click', onMouseClickFPS, false);
    window.addEventListener('resize', onWindowResize, false);
    onWindowResize(); animate();
}
function createMuseumLayout() { /* ... */ }
function animate(time) { /* ... */ }
function onWindowResize() { /* ... */ }
async function fetchAndDisplayPieces() { /* ... */ }
function create3DRepresentations(pieces) { /* ... */ }
function onMouseClickFPS(event) { /* ... */ }
