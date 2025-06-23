// assets/js/main.js - Theme and other UI controls

document.addEventListener('DOMContentLoaded', () => {
    // --- THEME (DARK/LIGHT) LOGIC ---
    const themeToggle = document.getElementById('unified-theme-toggle'); // UPDATED ID
    if (themeToggle) {
        const themeIcon = themeToggle.querySelector('i');
        const themeText = themeToggle.querySelector('span');

        const applyTheme = (theme) => {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.classList.add('dark-mode');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
                if (themeText) themeText.textContent = 'Tema (Sol)';
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                document.body.classList.remove('dark-mode');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
                if (themeText) themeText.textContent = 'Tema (Luna)';
            }
            localStorage.setItem('theme', theme);
            themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        };

        let activeTheme = localStorage.getItem('theme');
        if (!activeTheme) {
            activeTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        applyTheme(activeTheme);

        themeToggle.addEventListener('click', () => {
            let newTheme = localStorage.getItem('theme') === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }

    // --- MUTE TOGGLE LOGIC (Placeholder - actual logic in audio-controller.js) ---
    // audio-controller.js needs to be updated to find #unified-mute-toggle
    const muteToggleUnified = document.getElementById('unified-mute-toggle'); // UPDATED ID
    if (muteToggleUnified && typeof window.toggleMute === 'function') {
        muteToggleUnified.addEventListener('click', () => {
            window.toggleMute();
            const isMuted = window.isMuted ? window.isMuted() : false; // Assuming audio-controller exposes this
            const muteIcon = muteToggleUnified.querySelector('i');
            const muteText = muteToggleUnified.querySelector('span');
            if (isMuted) {
                if (muteIcon) { muteIcon.classList.remove('fa-volume-up'); muteIcon.classList.add('fa-volume-mute');}
                if (muteText) muteText.textContent = 'Sonido (Activar)';
            } else {
                if (muteIcon) { muteIcon.classList.remove('fa-volume-mute'); muteIcon.classList.add('fa-volume-up');}
                if (muteText) muteText.textContent = 'Sonido (Silenciar)';
            }
        });
        // Consider an initial state update if audio-controller.js loads and sets state before this runs
        // For example, dispatch a custom event from audio-controller on init or state change.
    }


    // --- HOMONEXUS TOGGLE LOGIC (Placeholder - actual logic in homonexus-toggle.js) ---
    // homonexus-toggle.js needs to be updated to find #unified-homonexus-toggle
    const homonexusToggleUnified = document.getElementById('unified-homonexus-toggle'); // UPDATED ID
    if (homonexusToggleUnified && typeof window.toggleHomonexus === 'function') {
        homonexusToggleUnified.addEventListener('click', () => {
            window.toggleHomonexus();
            // Update icon/text on homonexusToggleUnified based on state from homonexus-toggle.js
            // e.g. const isActive = window.isHomonexusActive ? window.isHomonexusActive() : false;
            // ... update button text/icon ...
        });
    }

});
