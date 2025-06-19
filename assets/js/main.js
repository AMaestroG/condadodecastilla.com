// assets/js/main.js - simplified menu controller and theme toggle

document.addEventListener('DOMContentLoaded', () => {
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

        const anyOpen = document.querySelectorAll('.menu-panel.active').length > 0;
        document.body.classList.toggle('menu-compressed', anyOpen);

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
        if (storedTheme === 'dark' || !storedTheme) {
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
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            if (icon) {
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
            }
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }

    // IA chat toggle and Homonexus functionality removed
});
