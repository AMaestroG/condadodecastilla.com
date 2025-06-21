export function initMenu() {
    const sidebarMenuId = 'sidebar';

    const updateAria = (btn, menu, open) => {
        if (btn) btn.setAttribute('aria-expanded', String(open));
        if (menu) menu.setAttribute('aria-hidden', String(!open));
    };

    const closeMobileSidebar = (menu, btn) => {
        menu.classList.remove('sidebar-visible');
        document.body.classList.remove('sidebar-active');
        updateAria(btn, menu, false);
        if (btn) btn.focus();
        updateGlobalMenuState();
    };

    const toggleMobileSidebar = (btn) => {
        const menu = document.getElementById(sidebarMenuId);
        if (!menu) return;
        document.querySelectorAll('.menu-panel.active').forEach(m => closeMenu(m));
        const open = !menu.classList.contains('sidebar-visible');
        menu.classList.toggle('sidebar-visible', open);
        document.body.classList.toggle('sidebar-active', open);
        document.body.classList.toggle('menu-compressed', open);
        updateAria(btn, menu, open);
        if (open) {
            const first = menu.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            (first || menu).focus();
        }
        updateGlobalMenuState();
    };

    const closeMenu = (menu, triggerButton = null) => {
        menu.classList.remove('active');
        const btn = triggerButton || document.querySelector(`[data-menu-target="${menu.id}"]`);
        updateAria(btn, menu, false);
        if (btn && triggerButton) btn.focus();
        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        if (side) document.body.classList.remove(`menu-open-${side}`);
        updateGlobalMenuState();
    };

    const toggleMenu = (btn) => {
        const targetId = btn.getAttribute('data-menu-target');
        if (!targetId) return;
        const menu = document.getElementById(targetId);
        if (!menu) return;
        const sidebar = document.getElementById(sidebarMenuId);
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
            const sidebarBtn = document.getElementById('consolidated-menu-button');
            closeMobileSidebar(sidebar, sidebarBtn);
        }
        document.querySelectorAll('.menu-panel.active').forEach(m => {
            if (m !== menu) closeMenu(m, document.querySelector(`[data-menu-target="${m.id}"]`));
        });
        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        const open = !menu.classList.contains('active');
        menu.classList.toggle('active', open);
        document.body.classList.toggle('menu-compressed', open);
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
            if (chatArea) chatArea.focus();
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

    const handleToggleEvent = (e) => {
        const btn = e.target.closest('[data-menu-target]');
        if (btn) {
            e.preventDefault();
            if (btn.id === 'consolidated-menu-button' && window.innerWidth <= 768) {
                toggleMobileSidebar(btn);
            } else {
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
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
            if (!menu.contains(e.target) && !(btn && btn.contains(e.target))) {
                closeMenu(menu, btn);
            }
        });
        const sidebar = document.getElementById(sidebarMenuId);
        const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
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
        }
    });

    const handleResize = () => {
        const sidebar = document.getElementById(sidebarMenuId);
        const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            closeMenu(menu, document.querySelector(`[data-menu-target="${menu.id}"]`));
        });
        if (sidebar && sidebar.classList.contains('sidebar-visible')) {
            closeMobileSidebar(sidebar, consolidatedMenuButton);
        }
        document.body.classList.remove('menu-compressed', 'sidebar-active', 'menu-open-left', 'menu-open-right');
        updateGlobalMenuState();
    };

    window.addEventListener('resize', handleResize);

    const closeDrawer = document.getElementById('close-ai-drawer');
    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            const panel = document.getElementById('ai-chat-panel');
            if (panel && panel.classList.contains('active')) {
                const btn = document.querySelector(`[data-menu-target="ai-chat-panel"]`);
                closeMenu(panel, btn);
            }
        });
    }
}
