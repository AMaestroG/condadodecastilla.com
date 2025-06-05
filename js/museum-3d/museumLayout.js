// js/museum-3d/museumLayout.js
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {};
}

MUSEUM_3D.Layout = (function() {
    'use strict';

    let scene; // Will be set by SceneManager
    const wallBoundingBoxes = [];

    // Room definitions - these could be loaded from a config file in a more complex setup
    // For now, keep them here. Positions are relative to (0,0,0) or each other.
    const mainRoom = { name: 'MainRoom', x:0, y:0, z:0, width: 20, height: 5, depth: 25, doorWidth: 2, doorHeight: 2.5 };
    const corridorA = { name: 'CorridorA', width: 4, height: 3.5, depth: 10, doorWidth: 2, doorHeight: 2.2 }; // Connects MainRoom to Room2
    const room2 = { name: 'Room2', width: 15, height: 4.5, depth: 15, doorWidth: 2, doorHeight: 2.5 };


    function init(threeScene) {
        scene = threeScene;
    }

    function create() {
        if (!scene) {
            console.error("Scene not initialized in Layout module. Call init(scene) first.");
            return;
        }
        createMuseumFloor();
        createMainRoomLayout();
        createCorridorALayout();
        createRoom2Layout();
    }

    function createMuseumFloor() {
        // A large plane for the floor, extending beyond the rooms slightly
        const totalWidth = mainRoom.width + corridorA.depth + room2.width + 10; // Approximate total extent
        const totalDepth = Math.max(mainRoom.depth, room2.depth) + 10;
        const floorGeometry = new THREE.PlaneGeometry(totalWidth, totalDepth);
        const floorMaterial = new THREE.MeshStandardMaterial({
            color: MUSEUM_3D.Utils.THEME_COLORS.darkFloor,
            roughness: 0.8,
            metalness: 0.1,
            side: THREE.DoubleSide // In case camera dips below
        });
        const floorMesh = new THREE.Mesh(floorGeometry, floorMaterial);
        floorMesh.rotation.x = -Math.PI / 2; // Rotate to be horizontal
        floorMesh.position.y = 0; // At the base of the walls
        // Center the floor roughly under the main room for now
        floorMesh.position.x = mainRoom.x + (corridorA.depth + room2.width) / 2;
        floorMesh.position.z = mainRoom.z;
        floorMesh.receiveShadow = true;
        scene.add(floorMesh);
    }

    function createWall(width, height, depth, material, position, rotationY = 0, name = 'wall') {
        const wallGeometry = new THREE.BoxGeometry(width, height, depth);
        const wallMesh = new THREE.Mesh(wallGeometry, material);
        wallMesh.position.copy(position);
        if (rotationY !== 0) wallMesh.rotation.y = rotationY;
        wallMesh.castShadow = true;
        wallMesh.receiveShadow = true;
        scene.add(wallMesh);

        wallMesh.updateMatrixWorld(true); // Ensure matrix is updated
        const wallBox = new THREE.Box3().setFromObject(wallMesh);
        wallBoundingBoxes.push({box: wallBox, name: `${name}_${wallBoundingBoxes.length}`});

        if (MUSEUM_3D.Utils.DEBUG_MODE) {
            const helper = new THREE.Box3Helper(wallBox, 0x00ffff); // Cyan color for wall helpers
            scene.add(helper);
        }
        return wallMesh;
    }

    function createDoorFrame(openingWidth, openingHeight, frameDepth, frameThickness, material, position, rotationY = 0) {
        const group = new THREE.Group();
        group.position.copy(position);
        if (rotationY) group.rotation.y = rotationY;

        // Lintel (top piece)
        const lintelGeo = new THREE.BoxGeometry(openingWidth + 2 * frameThickness, frameThickness, frameDepth);
        const lintel = new THREE.Mesh(lintelGeo, material);
        lintel.position.y = openingHeight / 2 + frameThickness / 2;
        group.add(lintel);

        // Jambs (side pieces)
        // Adjusting jamb height: it should be openingHeight, not openingHeight + frameThickness if lintel sits on top
        const jambGeo = new THREE.BoxGeometry(frameThickness, openingHeight, frameDepth);
        const jambLeft = new THREE.Mesh(jambGeo, material);
        jambLeft.position.set(-(openingWidth / 2 + frameThickness / 2), 0, 0);
        group.add(jambLeft);

        const jambRight = new THREE.Mesh(jambGeo, material);
        jambRight.position.set(openingWidth / 2 + frameThickness / 2, 0, 0);
        group.add(jambRight);

        scene.add(group);
        // Add bounding boxes for door frame parts if needed for collision, or treat as part of wall opening.
        // For simplicity, not adding detailed frame bounding boxes for collision here.
        return group;
    }


    function createMainRoomLayout() {
        const mr = mainRoom; // alias
        const wt = MUSEUM_3D.Utils.WALL_THICKNESS;
        const wallMaterial = new THREE.MeshStandardMaterial({ color: MUSEUM_3D.Utils.THEME_COLORS.alabasterBg, roughness: 0.9 });
        const doorFrameMaterial = new THREE.MeshStandardMaterial({color: MUSEUM_3D.Utils.THEME_COLORS.doorFrame});

        // Back wall (positive Z)
        createWall(mr.width + 2*wt, mr.height, wt, wallMaterial, new THREE.Vector3(mr.x, mr.y + mr.height / 2, mr.z - mr.depth / 2), 0, 'MainRoom_BackWall');
        // Front wall (negative Z) - with entrance (conceptual, assuming player starts inside or entrance is open)
        createWall(mr.width + 2*wt, mr.height, wt, wallMaterial, new THREE.Vector3(mr.x, mr.y + mr.height / 2, mr.z + mr.depth / 2), 0, 'MainRoom_FrontWall');
        // Left wall (negative X)
        createWall(mr.depth, mr.height, wt, wallMaterial, new THREE.Vector3(mr.x - mr.width / 2, mr.y + mr.height / 2, mr.z), Math.PI / 2, 'MainRoom_LeftWall');

        // Right wall (positive X) - with doorway to CorridorA
        const rightWallDoorwayPos = mr.x + mr.width / 2;
        const doorOpeningWidth = corridorA.width; // Corridor width acts as door opening
        const doorOpeningHeight = corridorA.height; // Corridor height

        // Segment 1 (before door)
        const segment1Width = (mr.depth / 2) - (doorOpeningWidth / 2) - wt; // Simplified, assuming door is centered on Z axis of this wall
        if (segment1Width > 0) {
            createWall(segment1Width, mr.height, wt, wallMaterial, new THREE.Vector3(rightWallDoorwayPos, mr.y + mr.height / 2, mr.z - (doorOpeningWidth/2) - (segment1Width/2) ), Math.PI / 2, 'MainRoom_RightWall_Seg1');
        }
        // Segment 2 (after door)
        const segment2Width = (mr.depth / 2) - (doorOpeningWidth / 2) - wt;
         if (segment2Width > 0) {
            createWall(segment2Width, mr.height, wt, wallMaterial, new THREE.Vector3(rightWallDoorwayPos, mr.y + mr.height / 2, mr.z + (doorOpeningWidth/2) + (segment2Width/2) ), Math.PI / 2, 'MainRoom_RightWall_Seg2');
        }
        // Door Frame for CorridorA entrance
        createDoorFrame(doorOpeningWidth, doorOpeningHeight, wt + 0.1, wt*1.5, doorFrameMaterial,
            new THREE.Vector3(rightWallDoorwayPos, mr.y + doorOpeningHeight/2, mr.z), Math.PI / 2);

        // Ceiling for Main Room
        const ceilingGeo = new THREE.PlaneGeometry(mr.width, mr.depth);
        const ceilingMat = new THREE.MeshStandardMaterial({color: MUSEUM_3D.Utils.THEME_COLORS.alabasterMedium, side: THREE.DoubleSide});
        const ceiling = new THREE.Mesh(ceilingGeo, ceilingMat);
        ceiling.rotation.x = Math.PI / 2;
        ceiling.position.set(mr.x, mr.y + mr.height, mr.z);
        scene.add(ceiling);
    }

    function createCorridorALayout() {
        const ca = corridorA; // alias
        const mr = mainRoom; // For positioning relative to main room
        const wt = MUSEUM_3D.Utils.WALL_THICKNESS;
        const wallMaterial = new THREE.MeshStandardMaterial({ color: MUSEUM_3D.Utils.THEME_COLORS.alabasterMedium, roughness: 0.9 });

        const startX = mr.x + mr.width / 2 + wt/2; // Corridor starts where main room's right wall ends

        // Corridor walls (parallel to Z axis, extending in positive X)
        // Top wall (positive Z side of corridor)
        createWall(ca.depth, ca.height, wt, wallMaterial, new THREE.Vector3(startX + ca.depth/2, mr.y + ca.height / 2, mr.z - ca.width / 2 - wt/2), 0, 'CorridorA_TopWall');
        // Bottom wall (negative Z side of corridor)
        createWall(ca.depth, ca.height, wt, wallMaterial, new THREE.Vector3(startX + ca.depth/2, mr.y + ca.height / 2, mr.z + ca.width / 2 + wt/2), 0, 'CorridorA_BottomWall');

        // Ceiling for Corridor A
        const ceilingGeo = new THREE.PlaneGeometry(ca.depth, ca.width);
        const ceilingMat = new THREE.MeshStandardMaterial({color: MUSEUM_3D.Utils.THEME_COLORS.alabasterMedium, side: THREE.DoubleSide});
        const ceiling = new THREE.Mesh(ceilingGeo, ceilingMat);
        ceiling.rotation.x = Math.PI / 2;
        ceiling.position.set(startX + ca.depth/2, mr.y + ca.height, mr.z);
        scene.add(ceiling);
    }

    function createRoom2Layout() {
        const r2 = room2; // alias
        const ca = corridorA;
        const mr = mainRoom;
        const wt = MUSEUM_3D.Utils.WALL_THICKNESS;
        const wallMaterial = new THREE.MeshStandardMaterial({ color: MUSEUM_3D.Utils.THEME_COLORS.alabasterBg, roughness: 0.9 });
        const doorFrameMaterial = new THREE.MeshStandardMaterial({color: MUSEUM_3D.Utils.THEME_COLORS.doorFrame});

        const startX = mr.x + mr.width / 2 + ca.depth + wt; // Room2 starts after CorridorA ends

        // Back wall (positive Z of Room2, aligned with Corridor's Z axis)
        createWall(r2.width + 2*wt, r2.height, wt, wallMaterial, new THREE.Vector3(startX + r2.width/2, mr.y + r2.height / 2, mr.z - r2.depth / 2),0, 'Room2_BackWall');
        // Front wall (negative Z of Room2)
        createWall(r2.width + 2*wt, r2.height, wt, wallMaterial, new THREE.Vector3(startX + r2.width/2, mr.y + r2.height / 2, mr.z + r2.depth / 2),0, 'Room2_FrontWall');
        // Right wall (positive X end of Room2)
        createWall(r2.depth, r2.height, wt, wallMaterial, new THREE.Vector3(startX + r2.width + wt/2, mr.y + r2.height / 2, mr.z), Math.PI / 2, 'Room2_RightWall');

        // Left wall (negative X side of Room2 - this is the wall connecting to CorridorA)
        // This wall has the doorway from CorridorA.
        const doorOpeningWidth = ca.width;
        const doorOpeningHeight = ca.height;

        // Create door frame for Room2 entrance from CorridorA
        createDoorFrame(doorOpeningWidth, doorOpeningHeight, wt + 0.1, wt*1.5, doorFrameMaterial,
            new THREE.Vector3(startX - wt/2, mr.y + doorOpeningHeight/2, mr.z), Math.PI/2); // Rotated to face corridor

        // Ceiling for Room2
        const ceilingGeo = new THREE.PlaneGeometry(r2.width, r2.depth);
        const ceilingMat = new THREE.MeshStandardMaterial({color: MUSEUM_3D.Utils.THEME_COLORS.alabasterMedium, side: THREE.DoubleSide});
        const ceiling = new THREE.Mesh(ceilingGeo, ceilingMat);
        ceiling.rotation.x = Math.PI / 2;
        ceiling.position.set(startX + r2.width/2, mr.y + r2.height, mr.z);
        scene.add(ceiling);
    }


    return {
        init,
        create,
        getWallBoundingBoxes: () => wallBoundingBoxes,
        // Expose room dimensions if other modules need them for placement logic
        getMainRoomDimensions: () => mainRoom,
        getCorridorADimensions: () => corridorA,
        getRoom2Dimensions: () => room2
    };
})();
