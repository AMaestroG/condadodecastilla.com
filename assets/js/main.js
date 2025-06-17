// assets/js/main.js
// Central initialization hooks. This file delegates menu behavior to js/menu-controller.js
// and initializes optional helpers only when available.

document.addEventListener('DOMContentLoaded', () => {
    if (typeof initializeThemeToggle === 'function') {
        initializeThemeToggle();
    }
});
