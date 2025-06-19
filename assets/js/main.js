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
        if (open && menu.id === 'language-panel' && typeof primeTranslateLoad === 'function') {
            primeTranslateLoad();
        }

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
    const moonToggle = document.getElementById('moon-toggle');

    const icon = themeToggle ? themeToggle.querySelector('i') : null;
    const applyIcon = (isDark) => {
        if (!icon) return;
        icon.classList.toggle('fa-moon', !isDark);
        icon.classList.toggle('fa-sun', isDark);
    };

    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'moon') {
        document.body.classList.add('luna');
        applyIcon(false);
    } else {
        const isDark = storedTheme === 'dark' || !storedTheme;
        document.body.classList.toggle('dark-mode', isDark);
        applyIcon(isDark);
        if (!storedTheme) {
            localStorage.setItem('theme', 'dark');
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            if (localStorage.getItem('theme') === 'moon') {
                document.body.classList.remove('luna');
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
                applyIcon(true);
                return;
            }
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            applyIcon(isDark);
        });
    }

    if (moonToggle) {
        moonToggle.addEventListener('click', () => {
            const isMoon = document.body.classList.toggle('luna');
            if (isMoon) {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'moon');
                applyIcon(false);
            } else {
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
                applyIcon(true);
            }
        });
    }

    // IA chat toggle and Homonexus functionality removed
});
