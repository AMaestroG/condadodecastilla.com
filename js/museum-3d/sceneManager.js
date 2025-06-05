// js/museum-3d/sceneManager.js
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {};
}

MUSEUM_3D.SceneManager = (function() {
    'use strict';

    let scene, camera, renderer;
    let composer, bloomPass; // For post-processing
    let ambientLight, hemisphereLight, mainRoomLight, corridorLight, room2Light; // Lights

    const mainRoom = { name: 'MainRoom', x:0, y:0, z:0, width: 20, height: 5, depth: 25 }; // TODO: Move to Layout or Config
    const room2 = { name: 'Room2', width: 15, height: 4.5, depth: 15 }; // TODO: Move to Layout or Config
    const corridorA = { name: 'CorridorA', width: 4, height: 3.5, depth: 10 };// TODO: Move to Layout or Config


    function init() {
        scene = new THREE.Scene();
        scene.background = MUSEUM_3D.Utils.THEME_COLORS.darkBackground;
        scene.fog = new THREE.Fog(MUSEUM_3D.Utils.THEME_COLORS.darkBackground, Math.max(mainRoom.depth, room2.depth) * 0.8, Math.max(mainRoom.depth, room2.depth) * 2.5);

        const container = document.getElementById('museo-3d-container');
        const aspect = container ? container.clientWidth / container.clientHeight : window.innerWidth / window.innerHeight;
        camera = new THREE.PerspectiveCamera(75, aspect, 0.1, 1000);
        // Initial camera position will be set by PlayerControls, but set a default fallback
        camera.position.set(mainRoom.x, MUSEUM_3D.Utils.PLAYER_HEIGHT, mainRoom.z + mainRoom.depth / 2 - 3);


        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 0.9;

        if (container) {
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);
        } else {
            console.warn('Museo 3D container not found, appending renderer to body.');
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);
        }

        setupLights();
        initPostprocessing();

        window.addEventListener('resize', onWindowResize, false);
        onWindowResize(); // Call once to set initial size
    }

    function setupLights() {
        ambientLight = new THREE.AmbientLight(0xffefd5, 0.12);
        scene.add(ambientLight);

        hemisphereLight = new THREE.HemisphereLight(0xB1E1FF, MUSEUM_3D.Utils.THEME_COLORS.darkFloor, 0.25);
        hemisphereLight.position.set(0, Math.max(mainRoom.height, room2.height, corridorA.height), 0);
        scene.add(hemisphereLight);

        mainRoomLight = new THREE.DirectionalLight(0xffefd5, 0.6);
        mainRoomLight.position.set(mainRoom.x + mainRoom.width * 0.3, mainRoom.y + mainRoom.height * 1.5, mainRoom.z + mainRoom.depth * 0.1);
        mainRoomLight.target.position.set(mainRoom.x, mainRoom.y + mainRoom.height / 3, mainRoom.z);
        mainRoomLight.castShadow = true;
        mainRoomLight.shadow.mapSize.width = 2048;
        mainRoomLight.shadow.mapSize.height = 2048;
        mainRoomLight.shadow.camera.near = 1;
        mainRoomLight.shadow.camera.far = Math.max(mainRoom.width, mainRoom.depth) * 2;
        mainRoomLight.shadow.camera.left = -mainRoom.width * 0.75;
        mainRoomLight.shadow.camera.right = mainRoom.width * 0.75;
        mainRoomLight.shadow.camera.top = mainRoom.depth * 0.75;
        mainRoomLight.shadow.camera.bottom = -mainRoom.depth * 0.75;
        mainRoomLight.shadow.bias = -0.001;
        scene.add(mainRoomLight);
        scene.add(mainRoomLight.target);

        // TODO: These light positions should be updated if room positions change via Layout module
        const corridorAPosXCenter = mainRoom.x + mainRoom.width / 2 + corridorA.depth / 2;
        corridorLight = new THREE.PointLight(MUSEUM_3D.Utils.THEME_COLORS.goldMain, 0.6, corridorA.depth * 1.1, 1.5);
        corridorLight.position.set(corridorAPosXCenter, mainRoom.y + corridorA.height * 0.8, mainRoom.z);
        corridorLight.castShadow = false; // Point lights can be expensive with shadows
        scene.add(corridorLight);

        const room2PosXCenter = mainRoom.x + mainRoom.width / 2 + corridorA.depth + MUSEUM_3D.Utils.WALL_THICKNESS + room2.width / 2;
        room2Light = new THREE.SpotLight(0xfff0e0, 0.5, room2.width * 1.8, Math.PI / 3.8, 0.2, 1.0);
        room2Light.position.set(room2PosXCenter, mainRoom.y + room2.height * 0.95, mainRoom.z);
        room2Light.target.position.set(room2PosXCenter, mainRoom.y, mainRoom.z);
        room2Light.castShadow = false; // Spotlights can also be expensive
        scene.add(room2Light);
        scene.add(room2Light.target);
    }

    function initPostprocessing() {
        if (typeof THREE.EffectComposer === 'undefined' || typeof THREE.RenderPass === 'undefined' || typeof THREE.UnrealBloomPass === 'undefined') {
            console.error('Postprocessing classes not found (EffectComposer, RenderPass, or UnrealBloomPass). Skipping postprocessing.');
            composer = null; // Ensure composer is null if classes are missing
            return;
        }
        const container = document.getElementById('museo-3d-container');
        let currentWidth = container ? container.clientWidth : window.innerWidth;
        let currentHeight = container ? container.clientHeight : window.innerHeight;

        composer = new THREE.EffectComposer(renderer);
        const renderPass = new THREE.RenderPass(scene, camera);
        composer.addPass(renderPass);

        // Adjust bloom parameters: strength, radius, threshold
        bloomPass = new THREE.UnrealBloomPass(new THREE.Vector2(currentWidth, currentHeight), 0.6, 0.5, 0.2);
        composer.addPass(bloomPass);
    }

    function onWindowResize() {
        const container = document.getElementById('museo-3d-container');
        let currentWidth = window.innerWidth;
        let currentHeight = window.innerHeight;

        if (container) { // Prefer container dimensions if it exists
            currentWidth = container.clientWidth;
            currentHeight = container.clientHeight;
        }

        if (camera) {
            camera.aspect = currentWidth / currentHeight;
            camera.updateProjectionMatrix();
        }
        if (renderer) {
            renderer.setSize(currentWidth, currentHeight);
        }
        if (composer) {
            composer.setSize(currentWidth, currentHeight);
            if (bloomPass) { // Update bloomPass resolution if it exists
                 bloomPass.resolution.set(currentWidth, currentHeight);
            }
        }
    }

    let prevTime = performance.now();

    function animate() {
        requestAnimationFrame(animate);

        const time = performance.now();
        const delta = (time - prevTime) / 1000;

        // Update controls if they exist and have an update method
        if (MUSEUM_3D.PlayerControls && MUSEUM_3D.PlayerControls.isInitialized() && MUSEUM_3D.PlayerControls.getControls().isLocked) {
            MUSEUM_3D.PlayerControls.update(delta);
        }

        // Update exhibit interactions (e.g., highlighting)
        if (MUSEUM_3D.ExhibitManager && MUSEUM_3D.PlayerControls.isInitialized() && MUSEUM_3D.PlayerControls.getControls().isLocked) {
            MUSEUM_3D.ExhibitManager.updateInteractionRaycaster(camera);
        }

        if (composer) {
            composer.render(delta);
        } else if (renderer && scene && camera) { // Fallback if no composer
            renderer.render(scene, camera);
        }

        prevTime = time;
    }

    function startAnimationLoop(){
        // Reset prevTime before starting the loop to avoid large delta on first frame
        prevTime = performance.now();
        animate();
    }

    // Exposed public methods and properties
    return {
        init,
        startAnimationLoop,
        getScene: () => scene,
        getCamera: () => camera,
        getRenderer: () => renderer,
        getDomElement: () => renderer ? renderer.domElement : null
        // No need to expose animate directly if startAnimationLoop is used
    };
})();
