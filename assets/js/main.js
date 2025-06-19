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
    const closeMenu = (menu) => {
        menu.classList.remove('active');
        const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
        if (btn) btn.setAttribute('aria-expanded', 'false');
        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        if (side) document.body.classList.remove(`menu-open-${side}`);
    };

    const toggleMenu = (btn) => {
        const targetId = btn.getAttribute('data-menu-target');
        if (!targetId) return;
        const menu = document.getElementById(targetId);
        if (!menu) return;

        // Close any other active menus to avoid overlap
        document.querySelectorAll('.menu-panel.active').forEach(m => {
            if (m !== menu) closeMenu(m);
        });

        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        const open = !menu.classList.contains('active');
        menu.classList.toggle('active', open);
        btn.setAttribute('aria-expanded', open);
        if (side) document.body.classList.toggle(`menu-open-${side}`, open);
        if (open && menu.id === 'language-panel' && typeof primeTranslateLoad === 'function') {
            primeTranslateLoad();
        }

        const anyOpen = document.querySelectorAll('.menu-panel.active').length > 0;
        document.body.classList.toggle('menu-compressed', anyOpen);
        if (window.audioController && typeof window.audioController.handleMenuToggle === 'function') {
            window.audioController.handleMenuToggle(anyOpen);
        }
        document.dispatchEvent(new CustomEvent('menu-toggled', { detail: { open: anyOpen } }));

        if (open && menu.id === 'ai-chat-panel') {
            const chatArea = document.getElementById('gemini-chat-area');
            if (chatArea) {
                chatArea.focus();
            }
        }
    };

    // Use event delegation so dynamically injected buttons still work
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-menu-target]');
        if (btn) {
            e.preventDefault();
            toggleMenu(btn);
        }
    });

    document.addEventListener('click', (e) => {
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
            if (!menu.contains(e.target) && !(btn && btn.contains(e.target))) {
                closeMenu(menu);
                document.body.classList.remove('menu-compressed');
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.menu-panel.active').forEach(menu => {
                closeMenu(menu);
            });
            document.body.classList.remove('menu-compressed');
        }
    });

    const closeDrawer = document.getElementById('close-ai-drawer');
    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            const panel = document.getElementById('ai-chat-panel');
            if (panel) {
                closeMenu(panel);
                document.body.classList.remove('menu-compressed');
            }
        });
    }

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'moon') {
            document.body.classList.add('luna');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        } else if (storedTheme === 'dark' || !storedTheme) {
            document.body.classList.add('dark-mode');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
            if (!storedTheme) {
                localStorage.setItem('theme', 'dark');
            }
        }
        themeToggle.addEventListener('click', () => {
            document.body.classList.remove('luna');
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            if (icon) {
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
            }
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }

    const moonToggle = document.getElementById('moon-toggle');
    if (moonToggle) {
        const moonIcon = moonToggle.querySelector('i');
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'moon' && moonIcon) {
            moonIcon.classList.remove('fa-moon');
            moonIcon.classList.add('fa-sun');
        }
        moonToggle.addEventListener('click', () => {
            const active = document.body.classList.contains('luna');
            if (active) {
                document.body.classList.remove('luna');
                if (moonIcon) {
                    moonIcon.classList.add('fa-moon');
                    moonIcon.classList.remove('fa-sun');
                }
                localStorage.setItem('theme', 'light');
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.add('luna');
                if (moonIcon) {
                    moonIcon.classList.remove('fa-moon');
                    moonIcon.classList.add('fa-sun');
                }
                localStorage.setItem('theme', 'moon');
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
});
