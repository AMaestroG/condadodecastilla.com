// js/museum-3d/config.js
// Definiciones de salas y pasillos del museo.
// Modificar estos valores permite ampliar fácilmente la disposición del museo.
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {};
}

MUSEUM_3D.Config = (function() {
    'use strict';

    const mainRoom = { name: 'MainRoom', x: 0, y: 0, z: 0, width: 20, height: 5, depth: 25 };
    const corridorA = { name: 'CorridorA', width: 4, height: 3.5, depth: 10 };
    const room2 = { name: 'Room2', width: 15, height: 4.5, depth: 15 };

    return {
        mainRoom,
        corridorA,
        room2
    };
})();
