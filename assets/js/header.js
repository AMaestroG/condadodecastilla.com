document.addEventListener('DOMContentLoaded', () => {
    const leftMenu = document.getElementById('slide-menu-left');
    const rightMenu = document.getElementById('slide-menu-right');
    const menuBtn = document.getElementById('menu-button');
    const toolsBtn = document.getElementById('tools-button');
    const overlay = document.getElementById('menu-overlay');

    function closeMenus() {
        [leftMenu, rightMenu].forEach(menu => {
            if (menu) {
                menu.classList.remove('open');
                menu.setAttribute('aria-hidden', 'true');
            }
        });
        document.body.classList.remove('menu-open-left', 'menu-open-right');
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
        if (toolsBtn) toolsBtn.setAttribute('aria-expanded', 'false');
        if (overlay) overlay.classList.remove('visible');
    }

    if (menuBtn && leftMenu) {
        menuBtn.addEventListener('click', () => {
            const isOpen = leftMenu.classList.toggle('open');
            rightMenu && rightMenu.classList.remove('open');
            document.body.classList.toggle('menu-open-left', isOpen);
            document.body.classList.remove('menu-open-right');
            menuBtn.setAttribute('aria-expanded', isOpen);
            toolsBtn && toolsBtn.setAttribute('aria-expanded', 'false');
            leftMenu.setAttribute('aria-hidden', !isOpen);
            rightMenu && rightMenu.setAttribute('aria-hidden', 'true');
            if (overlay) overlay.classList.toggle('visible', isOpen);
        });
    }

    if (toolsBtn && rightMenu) {
        toolsBtn.addEventListener('click', () => {
            const isOpen = rightMenu.classList.toggle('open');
            leftMenu && leftMenu.classList.remove('open');
            document.body.classList.toggle('menu-open-right', isOpen);
            document.body.classList.remove('menu-open-left');
            toolsBtn.setAttribute('aria-expanded', isOpen);
            menuBtn && menuBtn.setAttribute('aria-expanded', 'false');
            rightMenu.setAttribute('aria-hidden', !isOpen);
            leftMenu && leftMenu.setAttribute('aria-hidden', 'true');
            if (overlay) overlay.classList.toggle('visible', isOpen);
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMenus);
    }

    document.addEventListener('keyup', (e) => {
        if (e.key === 'Escape') {
            closeMenus();
        }
    });
});
