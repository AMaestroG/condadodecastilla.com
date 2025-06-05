// Ensure Three.js is loaded
if (typeof THREE === 'undefined') {
    console.error('Three.js not loaded. Make sure the CDN link is correct in museo.html.');
} else {
    console.log('Three.js loaded successfully. Version: ' + THREE.REVISION);

    let scene, camera, renderer, controls;
    const textureLoader = new THREE.TextureLoader();
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let clickableMeshes = [];

    const API_BASE_URL = "";
    const PIEZAS_API_URL = `${API_BASE_URL}/api/museo/piezas`;

    // Theme colors (hardcoded for simplicity, from epic_theme.css)
    const THEME_COLORS = {
        purpleEmperor: new THREE.Color("#4A0D67"),
        goldMain: new THREE.Color("#FFD700"),
        goldSecondary: new THREE.Color("#B8860B"),
        alabasterBg: new THREE.Color("#EAEBF0"),
        alabasterMedium: new THREE.Color("#D8D9E0"),
        textColor: new THREE.Color("#2c1d12"),
        darkFloor: new THREE.Color("#3a2d23"),
        darkBackground: new THREE.Color("#201523")
    };

    // Global variable to store room dimensions, accessible by setupOrbitControls
    // Note: It's generally better to pass such shared config or get it from a single source if it grows.
    let roomSize = { width: 14, height: 7, depth: 14 };


    function init() {
        // 1. Scene
        scene = new THREE.Scene();
        scene.background = THEME_COLORS.darkBackground;

        // 2. Renderer
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;

        const container = document.getElementById('museo-3d-container');
        if (container) {
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);
        } else {
            console.warn("#museo-3d-container not found. Appending to body as fallback.");
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);
        }

        // 3. Camera
        const aspect = container ? container.clientWidth / container.clientHeight : window.innerWidth / window.innerHeight;
        camera = new THREE.PerspectiveCamera(60, aspect, 0.1, 1000);
        camera.position.set(0, 2.5, 9);

        // 4. Lighting
        const ambientLight = new THREE.AmbientLight(0xffefd5, 0.4);
        scene.add(ambientLight);

        const hemisphereLight = new THREE.HemisphereLight(0xffefd5, THEME_COLORS.darkFloor, 0.6);
        hemisphereLight.position.set(0, 50, 0);
        scene.add(hemisphereLight);

        const directionalLight = new THREE.DirectionalLight(0xffe4b5, 0.8);
        directionalLight.position.set(8, 12, 10);
        directionalLight.castShadow = true;
        directionalLight.shadow.mapSize.width = 1024;
        directionalLight.shadow.mapSize.height = 1024;
        directionalLight.shadow.camera.near = 0.5;
        directionalLight.shadow.camera.far = 50;
        directionalLight.shadow.camera.left = -10;
        directionalLight.shadow.camera.right = 10;
        directionalLight.shadow.camera.top = 10;
        directionalLight.shadow.camera.bottom = -10;
        directionalLight.shadow.bias = -0.001;
        scene.add(directionalLight);

        // 5. Room
        // roomSize is already defined globally for this script
        const wallThickness = 0.2;
        const wallMaterial = new THREE.MeshStandardMaterial({
            color: THEME_COLORS.alabasterMedium,
            roughness: 0.8,
            metalness: 0.1
        });
        const floorMaterial = new THREE.MeshStandardMaterial({
            color: THEME_COLORS.darkFloor,
            roughness: 0.7,
            metalness: 0.2
        });

        const floorGeometry = new THREE.PlaneGeometry(roomSize.width, roomSize.depth);
        const floor = new THREE.Mesh(floorGeometry, floorMaterial);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = 0;
        floor.receiveShadow = true;
        scene.add(floor);

        const wallPositions = [
            { type: 'box', size: [roomSize.width, roomSize.height, wallThickness], pos: [0, roomSize.height / 2, -roomSize.depth / 2], rot: [0, 0, 0] }, // Back
            { type: 'box', size: [roomSize.depth, roomSize.height, wallThickness], pos: [-roomSize.width / 2, roomSize.height / 2, 0], rot: [0, Math.PI / 2, 0] }, // Left
            { type: 'box', size: [roomSize.depth, roomSize.height, wallThickness], pos: [roomSize.width / 2, roomSize.height / 2, 0], rot: [0, -Math.PI / 2, 0] }  // Right
        ];
        wallPositions.forEach(data => {
            const geom = new THREE.BoxGeometry(...data.size);
            const wall = new THREE.Mesh(geom, wallMaterial);
            wall.position.set(...data.pos);
            wall.rotation.set(...data.rot);
            wall.castShadow = true;
            wall.receiveShadow = true;
            scene.add(wall);
        });

        // 6. Setup OrbitControls (now called directly)
        setupOrbitControls();

        // 7. Fetch and display museum pieces
        fetchAndDisplayPieces();

        // 8. Event Listeners
        renderer.domElement.addEventListener('click', onMouseClick, false);
        window.addEventListener('resize', onWindowResize, false);

        // Initial resize call
        onWindowResize();

        // Start animation loop
        animate();
    }

    function setupOrbitControls() {
        if (!THREE.OrbitControls) {
            console.error("THREE.OrbitControls is not available. Ensure OrbitControls.js is included in museo.html.");
            // No alert here, as per instruction. Console error is sufficient.
            return;
        }
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.screenSpacePanning = true;
        controls.minDistance = 1.5;
        controls.maxDistance = roomSize.width * 1.2;
        controls.target.set(0, roomSize.height / 3, 0);
        controls.maxPolarAngle = Math.PI / 1.9;
        controls.minPolarAngle = Math.PI / 4;
        controls.update();
        console.log("OrbitControls initialized.");
    }

    function animate() {
        requestAnimationFrame(animate);
        if (controls && controls.update) { // Check if controls and its update method exist
            controls.update();
        }
        renderer.render(scene, camera);
    }

    function onWindowResize() {
        const container = document.getElementById('museo-3d-container');
        let currentWidth, currentHeight;

        if (container) {
            currentWidth = container.clientWidth;
            currentHeight = container.clientHeight;
        } else {
            currentWidth = window.innerWidth;
            currentHeight = window.innerHeight;
        }

        if (currentHeight === 0) {
            camera.aspect = currentWidth / 600; // Fallback aspect
        } else {
            camera.aspect = currentWidth / currentHeight;
        }
        camera.updateProjectionMatrix();
        renderer.setSize(currentWidth, currentHeight);
    }

    async function fetchAndDisplayPieces() {
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

    function create3DRepresentations(pieces) {
        const pieceBaseSize = 1.8;
        const pieceSpacing = pieceBaseSize * 1.8;
        const wallOffset = 0.15;
        const frameThickness = 0.08;
        const frameColor = THEME_COLORS.textColor;
        const roomCenterY = roomSize.height / 2.5;

        const pieceGroupPositions = [
            { wall: 'back', normal: new THREE.Vector3(0,0,1), offset: -roomSize.depth / 2 + wallOffset, rotationY: 0 },
            { wall: 'left', normal: new THREE.Vector3(1,0,0), offset: -roomSize.width / 2 + wallOffset, rotationY: Math.PI / 2 },
            { wall: 'right', normal: new THREE.Vector3(-1,0,0), offset: roomSize.width / 2 - wallOffset, rotationY: -Math.PI / 2 }
        ];

        let currentWallIndex = 0;
        let piecesOnCurrentWall = 0;
        // Calculate maxPiecesPerWall carefully, avoid division by zero if pieceGroupPositions is empty
        const numDisplayWalls = pieceGroupPositions.length > 0 ? pieceGroupPositions.length : 1;
        const maxPiecesPerWall = Math.ceil(pieces.length / numDisplayWalls);


        pieces.forEach((pieza) => {
            if (!pieza.imagenUrl) return;

            let imageUrl = pieza.imagenUrl;
            if (API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:')) {
                imageUrl = `${API_BASE_URL}${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}`;
            } else if (!API_BASE_URL && !imageUrl.startsWith('http') && !imageUrl.startsWith('data:') && !imageUrl.startsWith('/')) {
                imageUrl = `/${imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl}`;
            }

            textureLoader.load(imageUrl, (texture) => {
                const aspectRatio = texture.image ? texture.image.width / texture.image.height : 1;
                const pieceWidth = pieceBaseSize * aspectRatio;
                const pieceHeight = pieceBaseSize;

                const group = new THREE.Group();
                const frameMaterial = new THREE.MeshStandardMaterial({ color: frameColor, roughness: 0.6, metalness: 0.3 });
                const frameWidth = pieceWidth + 2 * frameThickness;
                const frameHeight = pieceHeight + 2 * frameThickness;
                const frameGeometry = new THREE.PlaneGeometry(frameWidth, frameHeight);
                const frameMesh = new THREE.Mesh(frameGeometry, frameMaterial);
                frameMesh.position.z = -0.01;
                group.add(frameMesh);

                const imageGeometry = new THREE.PlaneGeometry(pieceWidth, pieceHeight);
                const imageMaterial = new THREE.MeshStandardMaterial({ map: texture, side: THREE.DoubleSide, roughness: 0.6, metalness: 0.1 });
                const imageMesh = new THREE.Mesh(imageGeometry, imageMaterial);
                imageMesh.userData = pieza;
                imageMesh.castShadow = true;
                group.add(imageMesh);

                const wallInfo = pieceGroupPositions[currentWallIndex];
                // Calculate total pieces for this wall, considering remaining pieces
                const remainingPieces = pieces.length - (currentWallIndex * maxPiecesPerWall);
                const totalOnThisWall = Math.min(maxPiecesPerWall, remainingPieces);
                const startOffset = - (totalOnThisWall - 1) * pieceSpacing / 2;

                group.rotation.y = wallInfo.rotationY;
                group.position.y = roomCenterY;

                if (wallInfo.wall === 'back') {
                    group.position.x = startOffset + (piecesOnCurrentWall * pieceSpacing);
                    group.position.z = wallInfo.offset;
                } else if (wallInfo.wall === 'left') {
                    group.position.z = startOffset + (piecesOnCurrentWall * pieceSpacing);
                    group.position.x = wallInfo.offset;
                } else if (wallInfo.wall === 'right') {
                    group.position.z = -(startOffset + (piecesOnCurrentWall * pieceSpacing));
                    group.position.x = wallInfo.offset;
                }

                scene.add(group);
                clickableMeshes.push(imageMesh);

                piecesOnCurrentWall++;
                if (piecesOnCurrentWall >= maxPiecesPerWall && currentWallIndex < pieceGroupPositions.length -1) {
                    currentWallIndex++;
                    piecesOnCurrentWall = 0;
                }

            }, undefined, (error) => {
                console.error(`Error loading texture for ${pieza.titulo}:`, error);
            });
        });
    }

    function onMouseClick(event) {
        const container = renderer.domElement.parentElement;
        if (!container) return;
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(clickableMeshes);
        if (intersects.length > 0) {
            const clickedObject = intersects[0].object;
            if (clickedObject.userData && clickedObject.userData.titulo) {
                const pieza = clickedObject.userData;
                alert(`Pieza: ${pieza.titulo}\nAutor: ${pieza.autor || 'Desconocido'}\nDescripción: ${pieza.descripcion || 'No disponible'}`);
            }
        }
    }

    // Initialize the 3D scene when the DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init(); // DOMContentLoaded has already fired
    }
}
