document.addEventListener('DOMContentLoaded', () => {
    const unifiedPanel = document.getElementById('unified-panel');
    const openUnifiedPanelButton = document.getElementById('open-unified-panel-button');
    const closeUnifiedPanelButton = document.getElementById('close-unified-panel-button');

    const siteOverlay = document.getElementById('site-overlay');

    function openDrawer(drawer) {
        if (drawer) {
            drawer.classList.remove('translate-x-full'); // Panel is always on the right
            drawer.classList.add('translate-x-0');
            drawer.setAttribute('aria-hidden', 'false');

            siteOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }
    }

    function closeDrawer(drawer) {
        if (drawer) {
            drawer.classList.add('translate-x-full'); // Panel is always on the right
            drawer.classList.remove('translate-x-0');
            drawer.setAttribute('aria-hidden', 'true');

            // Overlay is always hidden when the single panel is closed
            siteOverlay.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scroll
        }
    }

    if (openUnifiedPanelButton) {
        openUnifiedPanelButton.addEventListener('click', () => openDrawer(unifiedPanel));
    }
    if (closeUnifiedPanelButton) {
        closeUnifiedPanelButton.addEventListener('click', () => closeDrawer(unifiedPanel));
    }

    if (siteOverlay) {
        siteOverlay.addEventListener('click', () => {
            if (unifiedPanel && unifiedPanel.getAttribute('aria-hidden') === 'false') {
                closeDrawer(unifiedPanel);
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (unifiedPanel && unifiedPanel.getAttribute('aria-hidden') === 'false') {
                closeDrawer(unifiedPanel);
            }
        }
    });

    // The following placeholder event listeners were for buttons inside the sidebar,
    // which are handled by assets/js/main.js or their respective modules.
    // They are removed from here as they don't belong to ui-drawers.js.
    /*
    document.getElementById('sidebar-theme-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Theme Toggle Clicked');
        document.getElementById('theme-toggle')?.click();
    });
    document.getElementById('sidebar-palette-toggle')?.addEventListener('click', () => { // This ID is already removed from HTML
        console.log('Sidebar Palette Toggle Clicked');
        // document.getElementById('palette-toggle')?.click();
    });
    document.getElementById('sidebar-mute-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Mute Toggle Clicked');
        document.getElementById('mute-toggle')?.click();
    });
     document.getElementById('sidebar-homonexus-toggle')?.addEventListener('click', () => {
        console.log('Sidebar Homonexus Toggle Clicked');
        document.getElementById('homonexus-toggle')?.click();
    });
    */
});
