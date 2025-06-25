/* assets/js/sliding-menu.js - handles sliding menu interactions */

document.addEventListener('DOMContentLoaded', () => {
    const vibrateFeedback = () => {
        if (navigator.vibrate) {
            navigator.vibrate([20,40,20]);
        }
    };

    const sidebarMenuId = 'sidebar'; // Assuming 'sidebar' is the ID of your sidebar

    const updateBodyForPanel = (menu, open) => {
        if (!menu) return;
        if (menu.classList.contains('left-panel')) {
            document.body.classList.toggle('menu-open-left', open);
        } else if (menu.classList.contains('right-panel')) {
            document.body.classList.toggle('menu-open-right', open);
        } else if (menu.classList.contains('top-panel')) {
            document.body.classList.toggle('menu-open-top', open);
        }
    };

    const updateAria = (btn, menu, open) => {
        if (btn) btn.setAttribute('aria-expanded', String(open));
        if (menu) menu.setAttribute('aria-hidden', String(!open));
    };

    const closeMobileSidebar = (menu, btn) => {
        menu.classList.remove('open');
        document.body.classList.remove('sidebar-active');
        updateAria(btn, menu, false);
        if (btn) btn.focus();
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
        vibrateFeedback();
    };

    const toggleMobileSidebar = (btn) => {
        const menu = document.getElementById(sidebarMenuId);
        if (!menu) return;

        // Close other panel menus
        document.querySelectorAll('.menu-panel.open').forEach(m => closeMenu(m));

        const open = !menu.classList.contains('open');
        menu.classList.toggle('open', open);
        document.body.classList.toggle('sidebar-active', open);
        updateAria(btn, menu, open);

        if (open) {
            const first = menu.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            (first || menu).focus();
        }
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
        vibrateFeedback();
    };

    const closeMenu = (menu, triggerButton = null) => { // Added triggerButton for focus
        menu.classList.remove('open');
        updateBodyForPanel(menu, false);
        const btn = triggerButton || document.querySelector(`[data-menu-target="${menu.id}"]`);
        updateAria(btn, menu, false);
        if (btn && triggerButton) btn.focus(); // Only focus if we passed the button explicitly
        // Recalculate anyOpen and update body classes
        updateGlobalMenuState();
        vibrateFeedback();
    };

    const toggleMenu = (btn) => {
        const targetId = btn.getAttribute('data-menu-target');
        if (!targetId) return;
        const menu = document.getElementById(targetId);
        if (!menu) return;

        // Close sidebar if open
        const sidebar = document.getElementById(sidebarMenuId);
        if (sidebar && sidebar.classList.contains('open')) {
            const sidebarBtn = document.getElementById('consolidated-menu-button'); // Assuming this is the sidebar toggle
            closeMobileSidebar(sidebar, sidebarBtn);
        }

        // Close any other active panel menus to avoid overlap
        document.querySelectorAll('.menu-panel.open').forEach(m => {
            if (m !== menu) closeMenu(m, document.querySelector(`[data-menu-target="${m.id}"]`));
        });

        const open = !menu.classList.contains('open');
        menu.classList.toggle('open', open);
        updateAria(btn, menu, open);
        updateBodyForPanel(menu, open);

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
        vibrateFeedback();
    };

    const updateGlobalMenuState = () => {
        const anyPanelOpen = document.querySelectorAll('.menu-panel.open').length > 0;
        const sidebarOpen = document.getElementById(sidebarMenuId)?.classList.contains('open');
        const anyOpen = anyPanelOpen || sidebarOpen;
        const leftPanelOpen = document.querySelector('.menu-panel.left-panel.open');
        const rightPanelOpen = document.querySelector('.menu-panel.right-panel.open');
        document.body.classList.toggle('menu-open-left', !!leftPanelOpen);
        document.body.classList.toggle('menu-open-right', !!rightPanelOpen);

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
            vibrateFeedback();
            if (btn.id === 'consolidated-menu-button' && window.innerWidth <= 768) {
                toggleMobileSidebar(btn);
            } else {
                // If #consolidated-menu-items is the target on desktop, ensure sidebar is closed
                if (btn.id === 'consolidated-menu-button' && btn.getAttribute('data-menu-target') === 'consolidated-menu-items') {
                    const sidebar = document.getElementById(sidebarMenuId);
                    if (sidebar && sidebar.classList.contains('open')) {
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
        if (e.target.closest('button')) {
            vibrateFeedback();
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
        document.querySelectorAll('.menu-panel.open').forEach(menu => {
            const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
            // If the click is outside the menu and not on its toggle button
            if (!menu.contains(e.target) && !(btn && btn.contains(e.target))) {
                closeMenu(menu, btn); // Pass btn for focus management
            }
        });

        // Close sidebar if click is outside
        const sidebar = document.getElementById(sidebarMenuId);
        const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
        if (sidebar && sidebar.classList.contains('open')) {
            // If the click is outside the sidebar and not on the consolidated menu button
            if (!sidebar.contains(e.target) && !(consolidatedMenuButton && consolidatedMenuButton.contains(e.target))) {
                closeMobileSidebar(sidebar, consolidatedMenuButton);
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.menu-panel.open').forEach(menu => {
                closeMenu(menu, document.querySelector(`[data-menu-target="${menu.id}"]`));
            });
            const sidebar = document.getElementById(sidebarMenuId);
            if (sidebar && sidebar.classList.contains('open')) {
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
        document.querySelectorAll('.menu-panel.open').forEach(menu => {
            closeMenu(menu, document.querySelector(`[data-menu-target="${menu.id}"]`));
        });

        // Close sidebar
        if (sidebar && sidebar.classList.contains('open')) {
            closeMobileSidebar(sidebar, consolidatedMenuButton);
        }

        // Reset body classes that might persist if not handled by individual close functions
        document.body.classList.remove('sidebar-active');
        updateGlobalMenuState(); // Final state update
    };

    window.addEventListener('resize', handleResize);

    const closeDrawer = document.getElementById('close-ai-drawer');
    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            const panel = document.getElementById('ai-chat-panel');
            if (panel && panel.classList.contains('open')) { // Ensure panel is open before closing
                const btn = document.querySelector(`[data-menu-target="ai-chat-panel"]`);
                closeMenu(panel, btn); // Pass button for focus
            }
        });
    }

    // IA chat toggle and Homonexus functionality removed

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

    const closeSidebarButton = document.getElementById('close-sidebar-button');
    if (closeSidebarButton) {
        closeSidebarButton.addEventListener('click', () => {
            const sidebar = document.getElementById(sidebarMenuId); // sidebarMenuId is 'sidebar'
            const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
            if (sidebar && sidebar.classList.contains('open')) {
                closeMobileSidebar(sidebar, consolidatedMenuButton);
            }
        });
    }

    const closeLanguagePanelButton = document.getElementById('close-language-panel');
    if (closeLanguagePanelButton) {
        closeLanguagePanelButton.addEventListener('click', () => {
            const panel = document.getElementById('language-panel');
            if (panel && panel.classList.contains('open')) {
                const btn = document.querySelector('[data-menu-target="language-panel"]');
                closeMenu(panel, btn);
            }
        });
    }
});
