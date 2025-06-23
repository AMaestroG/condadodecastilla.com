// assets/js/main.js - Theme and other UI controls

document.addEventListener('DOMContentLoaded', () => {
    // --- THEME (DARK/LIGHT) LOGIC ---
    const themeToggle = document.getElementById('sidebar-theme-toggle');
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
