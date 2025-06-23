document.addEventListener('DOMContentLoaded', () => {
    const mainSidebar = document.getElementById('main-sidebar');
    const openMainSidebarButton = document.getElementById('open-main-sidebar');
    const closeMainSidebarButton = document.getElementById('close-main-sidebar');

    const aiChatDrawer = document.getElementById('ai-chat-drawer');
    const openAiChatButton = document.getElementById('open-ai-chat');
    const closeAiChatButton = document.getElementById('close-ai-chat');

    const siteOverlay = document.getElementById('site-overlay');

    function openDrawer(drawer) {
        if (drawer) {
            // Ensure other drawers are closed before opening a new one, if that's the desired behavior
            // For now, they can be open simultaneously if triggered.

            drawer.classList.remove('translate-x-full', '-translate-x-full'); // Handles both left and right drawers
            drawer.classList.add('translate-x-0');
            drawer.setAttribute('aria-hidden', 'false');

            siteOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }
    }

    function closeDrawer(drawer) {
        if (drawer) {
            if(drawer.id === 'main-sidebar') {
                drawer.classList.add('-translate-x-full');
            } else { // Handles ai-chat-drawer and any other right-side drawers
                drawer.classList.add('translate-x-full');
            }
            drawer.classList.remove('translate-x-0');
            drawer.setAttribute('aria-hidden', 'true');

            // Hide overlay only if no other drawer is intended to be open.
            // This check ensures if one drawer is closed, but another was already open (or opened programmatically),
            // the overlay stays if needed by another drawer.
            // For simplicity, we'll assume closing one drawer means we check if ANY other drawer is still open.
            const anyDrawerOpen = Array.from(document.querySelectorAll('#main-sidebar, #ai-chat-drawer')) // Add other drawer IDs if any
                                      .some(d => d.getAttribute('aria-hidden') === 'false');

            if (!anyDrawerOpen) {
                siteOverlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scroll
            }
        }
    }

    if (openMainSidebarButton) {
        openMainSidebarButton.addEventListener('click', () => openDrawer(mainSidebar));
    }
    if (closeMainSidebarButton) {
        closeMainSidebarButton.addEventListener('click', () => closeDrawer(mainSidebar));
    }

    if (openAiChatButton) {
        openAiChatButton.addEventListener('click', () => openDrawer(aiChatDrawer));
    }
    if (closeAiChatButton) {
        closeAiChatButton.addEventListener('click', () => closeDrawer(aiChatDrawer));
    }

    if (siteOverlay) {
        siteOverlay.addEventListener('click', () => {
            // Close all drawers when overlay is clicked
            if (mainSidebar && mainSidebar.getAttribute('aria-hidden') === 'false') {
                closeDrawer(mainSidebar);
            }
            if (aiChatDrawer && aiChatDrawer.getAttribute('aria-hidden') === 'false') {
                closeDrawer(aiChatDrawer);
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (mainSidebar && mainSidebar.getAttribute('aria-hidden') === 'false') {
                closeDrawer(mainSidebar);
            }
            if (aiChatDrawer && aiChatDrawer.getAttribute('aria-hidden') === 'false') {
                closeDrawer(aiChatDrawer);
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
