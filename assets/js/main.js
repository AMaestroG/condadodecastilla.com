// assets/js/main.js - simplified menu controller and theme toggle

document.addEventListener('DOMContentLoaded', () => {
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
    };

    document.querySelectorAll('[data-menu-target]').forEach(btn => {
        btn.addEventListener('click', () => toggleMenu(btn));
    });

    document.addEventListener('click', (e) => {
        document.querySelectorAll('.menu-panel.active').forEach(menu => {
            const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
            if (!menu.contains(e.target) && !(btn && btn.contains(e.target))) {
                menu.classList.remove('active');
                if (btn) btn.setAttribute('aria-expanded', 'false');
                const side = menu.classList.contains('left-panel') ? 'left'
                            : (menu.classList.contains('right-panel') ? 'right' : '');
                if (side) document.body.classList.remove(`menu-open-${side}`);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.menu-panel.active').forEach(menu => {
                menu.classList.remove('active');
                const btn = document.querySelector(`[data-menu-target="${menu.id}"]`);
                if (btn) btn.setAttribute('aria-expanded', 'false');
                const side = menu.classList.contains('left-panel') ? 'left'
                            : (menu.classList.contains('right-panel') ? 'right' : '');
                if (side) document.body.classList.remove(`menu-open-${side}`);
            });
        }
    });

    const closeDrawer = document.getElementById('close-ai-drawer');
    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            const panel = document.getElementById('ai-chat-panel');
            if (panel) {
                panel.classList.remove('active');
                document.body.classList.remove('menu-open-left');
            }
            const btn = document.querySelector('[data-menu-target="ai-chat-panel"]');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'dark') {
            document.body.classList.add('dark-mode');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
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
