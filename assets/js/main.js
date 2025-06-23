// assets/js/main.js - Theme, Palette, and other UI controls

document.addEventListener('DOMContentLoaded', () => {
    // --- PALETTE LOGIC ---
    const paletteClasses = ['palette-dawn', 'palette-day', 'palette-dusk', 'palette-night'];
    const paletteToggle = document.getElementById('sidebar-palette-toggle'); // New ID

    const detectPalette = () => {
        const h = new Date().getHours();
        if (h >= 5 && h < 10) return 'dawn';
        if (h >= 10 && h < 17) return 'day';
        if (h >= 17 && h < 21) return 'dusk';
        return 'night';
    };

    const applyPalette = (palette) => {
        document.body.classList.remove(...paletteClasses);
        if (palette && palette !== 'auto') {
            document.body.classList.add(`palette-${palette}`);
        }
        // Update button text/icon if necessary
        if (paletteToggle) {
            const paletteText = paletteToggle.querySelector('span');
            if (paletteText) paletteText.textContent = `Paleta (${palette || 'auto'})`;
        }
    };

    let currentPalette = localStorage.getItem('palette') || 'auto';
    if (currentPalette === 'auto') {
        applyPalette(detectPalette()); // Apply detected on load if auto
    } else {
        applyPalette(currentPalette); // Apply stored palette
    }
    // Update button text on initial load
     if (paletteToggle) {
        const paletteText = paletteToggle.querySelector('span');
        if (paletteText) paletteText.textContent = `Paleta (${currentPalette})`;
    }


    if (paletteToggle) {
        const order = ['auto', 'dawn', 'day', 'dusk', 'night'];
        let currentIndex = order.indexOf(currentPalette);

        paletteToggle.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % order.length;
            const newPalette = order[currentIndex];
            localStorage.setItem('palette', newPalette);
            currentPalette = newPalette; // update currentPalette

            if (newPalette === 'auto') {
                // localStorage.removeItem('palette'); // No, keep 'auto' stored
                applyPalette(detectPalette());
            } else {
                applyPalette(newPalette);
            }
        });
    }

    // --- THEME (DARK/LIGHT) LOGIC ---
    const themeToggle = document.getElementById('sidebar-theme-toggle'); // New ID
    if (themeToggle) {
        const themeIcon = themeToggle.querySelector('i'); // Assuming <i> for icon
        const themeText = themeToggle.querySelector('span');

        const applyTheme = (theme) => {
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark'); // Apply to HTML for CSS vars
                document.body.classList.add('dark-mode'); // For other selectors if needed
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

    // --- MOON MODE LOGIC ---
    // Assuming a button with id="sidebar-moon-toggle" will be added to fragments/header.php sidebar
    const moonToggle = document.getElementById('sidebar-moon-toggle'); // New ID, if created
    if (moonToggle) {
        const moonIcon = moonToggle.querySelector('i'); // Assuming <i> for icon
        const moonText = moonToggle.querySelector('span');

        const applyMoonMode = (isMoonMode) => {
            if (isMoonMode) {
                document.body.classList.add('luna'); // Class for moon mode styles
                if (moonText) moonText.textContent = 'Modo Tierra'; // Or similar
                // Update icon if necessary
            } else {
                document.body.classList.remove('luna');
                if (moonText) moonText.textContent = 'Modo Luna';
                // Update icon if necessary
            }
            localStorage.setItem('moon', isMoonMode ? 'on' : 'off');
            moonToggle.setAttribute('aria-pressed', isMoonMode ? 'true' : 'false');
        };

        let currentMoonMode = localStorage.getItem('moon') === 'on';
        applyMoonMode(currentMoonMode);

        moonToggle.addEventListener('click', () => {
            currentMoonMode = !currentMoonMode; // Toggle state
            applyMoonMode(currentMoonMode);
        });
    } else {
        // console.log("Moon toggle button (#sidebar-moon-toggle) not found. Moon mode inactive.");
        // Clean up localStorage if button is definitely removed
        // localStorage.removeItem('moon');
        // document.body.classList.remove('luna');
    }

    // --- MUTE TOGGLE LOGIC (Placeholder - actual logic in audio-controller.js) ---
    // This just connects the new button to where the old logic might have been.
    // audio-controller.js needs to be updated to find #sidebar-mute-toggle
    const muteToggleSidebar = document.getElementById('sidebar-mute-toggle');
    if (muteToggleSidebar && typeof window.toggleMute === 'function') { // Check if global toggleMute exists
        muteToggleSidebar.addEventListener('click', () => {
            window.toggleMute(); // Or however audio-controller.js exposes its function
            // Update icon/text on muteToggleSidebar based on mute state from audio-controller
            const isMuted = window.isMuted(); // Assuming audio-controller exposes this
            const muteIcon = muteToggleSidebar.querySelector('i');
            const muteText = muteToggleSidebar.querySelector('span');
            if (isMuted) {
                if (muteIcon) { muteIcon.classList.remove('fa-volume-up'); muteIcon.classList.add('fa-volume-mute');}
                if (muteText) muteText.textContent = 'Sonido (Activar)';
            } else {
                if (muteIcon) { muteIcon.classList.remove('fa-volume-mute'); muteIcon.classList.add('fa-volume-up');}
                if (muteText) muteText.textContent = 'Sonido (Silenciar)';
            }
        });
        // Initial state update
        // setTimeout(() => { // Delay slightly for audio-controller to init
        //     const isMuted = window.isMuted ? window.isMuted() : false;
        //     const muteIcon = muteToggleSidebar.querySelector('i');
        //     const muteText = muteToggleSidebar.querySelector('span');
        //     if (isMuted) {
        //         if (muteIcon) { muteIcon.classList.remove('fa-volume-up'); muteIcon.classList.add('fa-volume-mute');}
        //         if (muteText) muteText.textContent = 'Sonido (Activar)';
        //     } else {
        //         if (muteIcon) { muteIcon.classList.remove('fa-volume-mute'); muteIcon.classList.add('fa-volume-up');}
        //         if (muteText) muteText.textContent = 'Sonido (Silenciar)';
        //     }
        // }, 100);
    }


    // --- HOMONEXUS TOGGLE LOGIC (Placeholder - actual logic in homonexus-toggle.js) ---
    // homonexus-toggle.js needs to be updated to find #sidebar-homonexus-toggle
    const homonexusToggleSidebar = document.getElementById('sidebar-homonexus-toggle');
    if (homonexusToggleSidebar && typeof window.toggleHomonexus === 'function') { // Check if global toggleHomonexus exists
        homonexusToggleSidebar.addEventListener('click', () => {
            window.toggleHomonexus();
            // Update icon/text on homonexusToggleSidebar based on state from homonexus-toggle.js
        });
    }

});
