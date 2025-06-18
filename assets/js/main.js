// assets/js/main.js - simplified menu controller and theme toggle

document.addEventListener('DOMContentLoaded', () => {
    if (typeof applyLanguageBarOffset === 'function') {
        applyLanguageBarOffset();
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
        const side = menu.classList.contains('left-panel') ? 'left'
                    : (menu.classList.contains('right-panel') ? 'right' : '');
        const open = menu.classList.toggle('active');
        btn.setAttribute('aria-expanded', open);
        if (side) document.body.classList.toggle(`menu-open-${side}`, open);
        document.body.classList.toggle('menu-compressed', open);
        if (open && menu.id === 'ai-chat-panel') {
            const chatArea = document.getElementById('gemini-chat-area');
            if (chatArea) {
                chatArea.focus();
            }
        }
    };

    document.querySelectorAll('[data-menu-target]').forEach(btn => {
        btn.addEventListener('click', () => toggleMenu(btn));
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
});
