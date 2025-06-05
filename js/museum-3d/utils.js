// js/museum-3d/utils.js
if (typeof MUSEUM_3D === 'undefined') {
    globalThis.MUSEUM_3D = {}; // Use globalThis for broader compatibility
}

MUSEUM_3D.Utils = (function() {
    'use strict';

    const THEME_COLORS = {
        purpleEmperor: new THREE.Color("#4A0D67"),
        goldMain: new THREE.Color("#FFD700"),
        goldSecondary: new THREE.Color("#B8860B"),
        alabasterBg: new THREE.Color("#EAEBF0"),
        alabasterMedium: new THREE.Color("#D8D9E0"),
        textColor: new THREE.Color("#2c1d12"),
        darkFloor: new THREE.Color("#3a2d23"),
        darkBackground: new THREE.Color("#1c1824"),
        glass: new THREE.Color(0xd0e0ff), // Example: 0xd0e0ff for a light blue tint
        hoverEmissive: new THREE.Color(0x505020), // A subtle yellow/greenish glow
        doorFrame: new THREE.Color("#5c4d3f")
    };

    const API_BASE_URL = ""; // Assuming API is at root, e.g., /api/museo/piezas
    const PIEZAS_API_URL = `${API_BASE_URL}/api/museo/piezas`;

    const DEBUG_MODE = false; // Set to true for debugging features like Box3Helpers

    // Constants for player and interaction
    const PLAYER_HEIGHT = 1.7;
    const INTERACTION_DISTANCE = 3.5; // Max distance to interact with an exhibit

    // Room and layout constants (can be expanded)
    const WALL_THICKNESS = 0.2;

    // Basic texture loading wrapper
    const textureLoader = new THREE.TextureLoader();
    function loadTexture(url, onSuccess, onError) {
        return textureLoader.load(url, onSuccess, undefined, onError);
    }

    // Exported values
    return {
        THEME_COLORS,
        API_BASE_URL,
        PIEZAS_API_URL,
        DEBUG_MODE,
        PLAYER_HEIGHT,
        INTERACTION_DISTANCE,
        WALL_THICKNESS,
        loadTexture
    };
})();
