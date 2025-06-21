// assets/js/main.js - simplified menu controller and theme toggle

document.addEventListener('DOMContentLoaded', () => {
    const paletteClasses = ['palette-dawn','palette-day','palette-dusk','palette-night'];

    const detectPalette = () => {
        const h = new Date().getHours();
        if (h >= 5 && h < 10) return 'dawn';
        if (h >= 10 && h < 17) return 'day';
        if (h >= 17 && h < 21) return 'dusk';
        return 'night';
    };

    const applyPalette = (p) => {
        document.body.classList.remove(...paletteClasses);
        document.body.classList.add(`palette-${p}`);
    };

    const storedPalette = localStorage.getItem('palette');
    if (storedPalette && storedPalette !== 'auto') {
        applyPalette(storedPalette);
    } else {
        applyPalette(detectPalette());
    }

    const sidebarMenuId = 'sidebar'; // Assuming 'sidebar' is the ID of your sidebar

    const updateAria = (btn, menu, open) => {
        if (btn) btn.setAttribute('aria-expanded', String(open));
        if (menu) menu.setAttribute('aria-hidden', String(!open));
    };

    const closeMobileSidebar = (menu, btn) => {
        menu.classList.remove('sidebar-visible');
        document.body.classList.remove('sidebar-active');
        updateAria(btn, menu, false);
        if (btn) btn.focus();
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
    };

    const toggleMobileSidebar = (btn) => {
        const menu = document.getElementById(sidebarMenuId);
        if (!menu) return;

        // Close other panel menus
        document.querySelectorAll('.menu-panel.active').forEach(m => closeMenu(m));

        const open = !menu.classList.contains('sidebar-visible');
        menu.classList.toggle('sidebar-visible', open);
        document.body.classList.toggle('sidebar-active', open);
        updateAria(btn, menu, open);

        if (open) {
            const first = menu.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            (first || menu).focus();
        }
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
    };

    const closeMenu = (menu, triggerButton = null) => { // Added triggerButton for focus
        menu.classList.remove('active');
        const btn = triggerButton || document.querySelector(`[data-menu-target="${menu.id}"]`);
        updateAria(btn, menu, false);
        if (btn && triggerButton) btn.focus(); // Only focus if we passed the button explicitly
        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        if (side) document.body.classList.remove(`menu-open-${side}`);
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
    };

    const toggleMenu = (btn) => {
        const targetId = btn.getAttribute('data-menu-target');
        if (!targetId) return;
        const menu = document.getElementById(targetId);
        if (!menu) return;

        // Close sidebar if open
        const sidebar = document.getElementById(sidebarMenuId);
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
            const sidebarBtn = document.getElementById('consolidated-menu-button'); // Assuming this is the sidebar toggle
            closeMobileSidebar(sidebar, sidebarBtn);
        }

        // Close any other active panel menus to avoid overlap
        document.querySelectorAll('.menu-panel.active').forEach(m => {
            if (m !== menu) closeMenu(m, document.querySelector(`[data-menu-target="${m.id}"]`));
        });

        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        const open = !menu.classList.contains('active');
        menu.classList.toggle('active', open);
        updateAria(btn, menu, open);
        if (side) document.body.classList.toggle(`menu-open-${side}`, open);

        if (open && menu.id === 'language-panel' && typeof primeTranslateLoad === 'function') {
            primeTranslateLoad();
        }
        if (open) {
            const first = menu.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            (first || menu).focus();
        }

        updateGlobalMenuState();

        if (open && menu.id === 'ai-chat-panel') {
            const chatArea = document.getElementById('gemini-chat-area');
            if (chatArea) {
                chatArea.focus();
            }
        }
    };

    const updateGlobalMenuState = () => {
        const anyPanelOpen = document.querySelectorAll('.menu-panel.active').length > 0;
        const sidebarOpen = document.getElementById(sidebarMenuId)?.classList.contains('sidebar-visible');
        const anyOpen = anyPanelOpen || sidebarOpen;

        document.body.classList.toggle('menu-compressed', anyOpen);

        if (window.audioController && typeof window.audioController.handleMenuToggle === 'function') {
            window.audioController.handleMenuToggle(anyOpen);
        }
        document.dispatchEvent(new CustomEvent('menu-toggled', { detail: { open: anyOpen } }));
    };

    // Use event delegation so dynamically injected buttons still work
    const handleToggleEvent = (e) => {
        const btn = e.target.closest('[data-menu-target]');
        if (btn) {
            e.preventDefault();
            if (btn.id === 'consolidated-menu-button' && window.innerWidth <= 768) {
                toggleMobileSidebar(btn);
            } else {
                // If #consolidated-menu-items is the target on desktop, ensure sidebar is closed
                if (btn.id === 'consolidated-menu-button' && btn.getAttribute('data-menu-target') === 'consolidated-menu-items') {
                    const sidebar = document.getElementById(sidebarMenuId);
                    if (sidebar && sidebar.classList.contains('sidebar-visible')) {
                        closeMobileSidebar(sidebar, btn);
                    }
                }
                toggleMenu(btn);
            }
        }
    };

    let touchHandled = false;
    document.addEventListener('touchstart', (e) => {
        touchHandled = true;
        handleToggleEvent(e);
    });
    document.addEventListener('click', (e) => {
        if (touchHandled) {
            touchHandled = false;
            return;
        }
        handleToggleEvent(e);
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('.menu-panel a[href]');
        if (link) {
            const menu = link.closest('.menu-panel');
            if (menu) {
                if (menu.id === sidebarMenuId) {
                    const sidebarBtn = document.getElementById('consolidated-menu-button');
                    closeMobileSidebar(menu, sidebarBtn);
                } else {
                    const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
                    closeMenu(menu, btn);
                }
            }
        }

        // Close panel menus if click is outside
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
            // If the click is outside the menu and not on its toggle button
            if (!menu.contains(e.target) && !(btn && btn.contains(e.target))) {
                closeMenu(menu, btn); // Pass btn for focus management
            }
        });

        // Close sidebar if click is outside
        const sidebar = document.getElementById(sidebarMenuId);
        const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
            // If the click is outside the sidebar and not on the consolidated menu button
            if (!sidebar.contains(e.target) && !(consolidatedMenuButton && consolidatedMenuButton.contains(e.target))) {
                closeMobileSidebar(sidebar, consolidatedMenuButton);
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.menu-panel.active').forEach(menu => {
                closeMenu(menu, document.querySelector(`[data-menu-target="${menu.id}"]`));
            });
            const sidebar = document.getElementById(sidebarMenuId);
            if (sidebar && sidebar.classList.contains('sidebar-visible')) {
                const sidebarBtn = document.getElementById('consolidated-menu-button');
                closeMobileSidebar(sidebar, sidebarBtn);
            }
            // updateGlobalMenuState will be called by closeMenu/closeMobileSidebar
        }
    });

    const handleResize = () => {
        const sidebar = document.getElementById(sidebarMenuId);
        const consolidatedMenuButton = document.getElementById('consolidated-menu-button');

        // Close all panel menus
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            closeMenu(menu, document.querySelector(`[data-menu-target="${menu.id}"]`));
        });

        // Close sidebar
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
            closeMobileSidebar(sidebar, consolidatedMenuButton);
        }

        // Reset body classes that might persist if not handled by individual close functions
        document.body.classList.remove('menu-compressed', 'sidebar-active', 'menu-open-left', 'menu-open-right');
        updateGlobalMenuState(); // Final state update
    };

    window.addEventListener('resize', handleResize);

    const closeDrawer = document.getElementById('close-ai-drawer');
    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            const panel = document.getElementById('ai-chat-panel');
            if (panel && panel.classList.contains('active')) { // Ensure panel is active before closing
                const btn = document.querySelector(`[data-menu-target="ai-chat-panel"]`);
                closeMenu(panel, btn); // Pass button for focus, menu-compressed handled by updateGlobalMenuState
            }
        });
    }

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        const storedTheme = localStorage.getItem('theme');
        let activeTheme = storedTheme;
        if (!activeTheme) {
            activeTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        if (activeTheme === 'dark') {
            document.body.classList.add('dark-mode');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
        themeToggle.setAttribute('aria-pressed', activeTheme === 'dark' ? 'true' : 'false');
        if (!storedTheme) {
            localStorage.setItem('theme', activeTheme);
        }

        themeToggle.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark-mode');
            if (icon) {
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
            }
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeToggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });
    }

    const moonToggle = document.getElementById('moon-toggle');
    if (moonToggle) {
        const storedMoon = localStorage.getItem('moon');
        if (storedMoon === 'on') {
            document.body.classList.add('luna');
        }
        moonToggle.addEventListener('click', () => {
            const active = document.body.classList.toggle('luna');
            if (active) {
                localStorage.setItem('moon', 'on');
            } else {
                localStorage.removeItem('moon');
            }
        });
    }

    const paletteToggle = document.getElementById('palette-toggle');
    if (paletteToggle) {
        const order = ['auto','dawn','day','dusk','night'];
        let index = order.indexOf(storedPalette || 'auto');
        paletteToggle.addEventListener('click', () => {
            index = (index + 1) % order.length;
            const p = order[index];
            if (p === 'auto') {
                localStorage.removeItem('palette');
                applyPalette(detectPalette());
            } else {
                localStorage.setItem('palette', p);
                applyPalette(p);
            }
        });
    }

    // IA chat toggle and Homonexus functionality removed

    function setupMobileAIChatTrigger() {
        if (window.innerWidth <= 768) {
            const originalTrigger = document.getElementById('ai-chat-trigger');
            const placeholderMobile = document.getElementById('ai-chat-trigger-placeholder-mobile');

            if (originalTrigger && placeholderMobile && placeholderMobile.children.length === 0) {
                const clonedTrigger = originalTrigger.cloneNode(true);
                clonedTrigger.id = 'ai-chat-trigger-mobile';
                // Ensure the data-menu-target is still correct for the cloned button.
                // If event listeners were attached by ID, they might need re-attaching or using class based listeners.
                // However, the current main click listener uses e.target.closest('[data-menu-target]'), which will work.
                placeholderMobile.appendChild(clonedTrigger);
            }
        }
    }
    setupMobileAIChatTrigger(); // Call it on initial load

    // Also consider calling setupMobileAIChatTrigger in handleResize if the trigger should be added/removed dynamically
    // For now, it's only added if on mobile on load. If resizing from desktop to mobile, it won't be there.
    // And if resizing from mobile to desktop, it will remain in the sidebar unless explicitly removed.
    // The subtask asks for it to be cloned when mobile sidebar is initialized/opened or on DOMContentLoaded.
    // For simplicity, DOMContentLoaded is chosen here. A more robust solution might involve the resize handler.

    function populateSidebarContents() {
        const mainMenuPlaceholder = document.getElementById('main-menu-placeholder');
        const adminMenuPlaceholder = document.getElementById('admin-menu-placeholder');
        const socialMenuPlaceholder = document.getElementById('social-menu-placeholder');

        const mainMenuSource = document.getElementById('main-menu'); // This is <ul id="main-menu">
        const adminMenuSourceContent = document.getElementById('admin-menu-source-content');
        const socialMenuSourceContent = document.getElementById('social-menu-source-content');

        if (mainMenuPlaceholder && mainMenuSource && mainMenuPlaceholder.childElementCount === 0) {
            // Clone the UL and its children to avoid issues if original is modified or events are tied
            const clonedMainMenu = mainMenuSource.cloneNode(true);
            mainMenuPlaceholder.appendChild(clonedMainMenu);
        }

        if (adminMenuPlaceholder && adminMenuSourceContent && adminMenuPlaceholder.childElementCount === 0) {
            // adminMenuSourceContent is a div wrapper, we want its children (the actual ul)
            // Clone its children to avoid issues
            Array.from(adminMenuSourceContent.children).forEach(child => {
                adminMenuPlaceholder.appendChild(child.cloneNode(true));
            });
        }

        if (socialMenuPlaceholder && socialMenuSourceContent && socialMenuPlaceholder.childElementCount === 0) {
            // socialMenuSourceContent is a div wrapper, we want its children (the actual links/icons)
            // Clone its children
            Array.from(socialMenuSourceContent.children).forEach(child => {
                socialMenuPlaceholder.appendChild(child.cloneNode(true));
            });
        }
    }
    populateSidebarContents(); // Call it on initial load to populate the sidebar
});
