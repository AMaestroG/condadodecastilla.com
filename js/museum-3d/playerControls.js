// js/museum-3d/playerControls.js
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {};
}

MUSEUM_3D.PlayerControls = (function() {
    'use strict';

    let camera;
    let domElement;
    let controlsFPS;

    let moveForward = false;
    let moveBackward = false;
    let moveLeft = false;
    let moveRight = false;

    const playerVelocity = new THREE.Vector3();
    const playerDirection = new THREE.Vector3(); // Temp vector for calculations
    const playerSpeed = 4.0; // meters per second

    let crosshairElement;
    let instructionsElement;

    let isInitializedFlag = false;

    // Collision detection related
    let wallBoundingBoxes = []; // From Layout module
    let exhibitorBoundingBoxes = []; // From ExhibitManager module

    function init(cameraInstance, rendererDomElement) {
        if (typeof THREE.PointerLockControls === 'undefined') {
            console.error('THREE.PointerLockControls not found. Make sure it is loaded.');
            const instructions = document.getElementById('pointer-lock-instructions');
            if (instructions) {
                instructions.innerHTML = 'Error: Player controls (PointerLockControls) could not be loaded. Please check the console.';
            }
            return;
        }

        camera = cameraInstance;
        domElement = rendererDomElement;

        crosshairElement = document.getElementById('crosshair');
        instructionsElement = document.getElementById('pointer-lock-instructions');

        if (!instructionsElement) {
            console.error('Pointer lock instructions element (#pointer-lock-instructions) not found.');
            return;
        }
        if (!crosshairElement) {
            console.warn('Crosshair element (#crosshair) not found.');
        }

        controlsFPS = new THREE.PointerLockControls(camera, domElement);
        // Add the camera (which is the PointerLockControls object's "head") to the scene
        // This is usually done by SceneManager or the main script after initializing SceneManager
        // MUSEUM_3D.SceneManager.getScene().add(controlsFPS.getObject());
        // ^ This line should be called from museo-3d-main.js after scene is available.

        instructionsElement.addEventListener('click', function () {
            controlsFPS.lock();
        });

        // Manual ESC handler in case the browser doesn't automatically unlock
        document.addEventListener('keydown', function (event) {
            if (event.code === 'Escape' && controlsFPS.isLocked) {
                controlsFPS.unlock();
            }
        });

        controlsFPS.addEventListener('lock', function () {
            instructionsElement.style.display = 'none';
            if (crosshairElement) crosshairElement.style.display = 'block';
            document.addEventListener('keydown', onKeyDown);
            document.addEventListener('keyup', onKeyUp);
            // Reset movement state on lock
            moveForward = false; moveBackward = false; moveLeft = false; moveRight = false;
        });

        controlsFPS.addEventListener('unlock', function () {
            instructionsElement.style.display = 'flex';
            if (crosshairElement) crosshairElement.style.display = 'none';
            document.removeEventListener('keydown', onKeyDown);
            document.removeEventListener('keyup', onKeyUp);
            // Clear any interaction highlights when controls are unlocked
            if (MUSEUM_3D.ExhibitManager) {
                MUSEUM_3D.ExhibitManager.clearFocus();
            }
        });

        // Set initial player position (e.g., center of main room, looking down Z axis)
        // This might need adjustment based on final room layout from MUSEUM_3D.Layout
        const mainRoomDims = MUSEUM_3D.Layout.getMainRoomDimensions ? MUSEUM_3D.Layout.getMainRoomDimensions() : {x:0, z:0, depth: 20};
        controlsFPS.getObject().position.set(mainRoomDims.x, MUSEUM_3D.Utils.PLAYER_HEIGHT, mainRoomDims.z + mainRoomDims.depth / 2 - 5);

        isInitializedFlag = true;
    }

    function onKeyDown(event) {
        switch (event.code) {
            case 'KeyW': case 'ArrowUp': moveForward = true; break;
            case 'KeyA': case 'ArrowLeft': moveLeft = true; break;
            case 'KeyS': case 'ArrowDown': moveBackward = true; break;
            case 'KeyD': case 'ArrowRight': moveRight = true; break;
            case 'KeyE': // Interaction key
                if (MUSEUM_3D.ExhibitManager && controlsFPS.isLocked) {
                    MUSEUM_3D.ExhibitManager.interactWithFocusedExhibit();
                }
                break;
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

    function update(delta) {
        if (!controlsFPS.isLocked) {
            playerVelocity.set(0,0,0); // No movement if not locked
            return;
        }

        // Friction / damping
        playerVelocity.x -= playerVelocity.x * 10.0 * delta;
        playerVelocity.z -= playerVelocity.z * 10.0 * delta;
        // No vertical velocity changes from movement controls (no jumping/flying)
        // playerVelocity.y -= 9.8 * 100.0 * delta; // gravity (if needed)

        playerDirection.z = Number(moveForward) - Number(moveBackward);
        playerDirection.x = Number(moveLeft) - Number(moveRight);
        playerDirection.normalize(); // this ensures consistent movements in all directions

        if (moveForward || moveBackward) playerVelocity.z -= playerDirection.z * playerSpeed * 10.0 * delta;
        if (moveLeft || moveRight) playerVelocity.x -= playerDirection.x * playerSpeed * 10.0 * delta;

        // Store current position before movement
        const oldPosition = controlsFPS.getObject().position.clone();

        // Apply movement
        controlsFPS.moveRight(-playerVelocity.x * delta);
        controlsFPS.moveForward(-playerVelocity.z * delta);

        // Collision Detection
        const newPlayerBox = new THREE.Box3().setFromCenterAndSize(
            controlsFPS.getObject().position,
            new THREE.Vector3(0.5, MUSEUM_3D.Utils.PLAYER_HEIGHT, 0.5) // Player's bounding box
        );

        let collision = false;
        const allBoundingBoxes = [...wallBoundingBoxes, ...exhibitorBoundingBoxes];

        for (const itemBox of allBoundingBoxes) {
            if (newPlayerBox.intersectsBox(itemBox.box)) {
                collision = true;
                break;
            }
        }

        if (collision) {
            controlsFPS.getObject().position.copy(oldPosition); // Revert to old position
            playerVelocity.set(0,0,0); // Stop movement on collision
        }

        // Ensure player stays at playerHeight (no flying or sinking)
        // controlsFPS.getObject().position.y = MUSEUM_3D.Utils.PLAYER_HEIGHT;
        // ^ This might be too restrictive if there are stairs/ramps later.
        // For now, simple flat museum, so this is fine.
    }

    function setCollisionBoundingBoxes(walls, exhibits) {
        wallBoundingBoxes = walls || [];
        exhibitorBoundingBoxes = exhibits || [];
    }


    return {
        init,
        update,
        getControls: () => controlsFPS,
        isInitialized: () => isInitializedFlag,
        setCollisionBoundingBoxes
        // No direct exposure of onKeyDown/onKeyUp, handled internally by lock/unlock
    };
})();
