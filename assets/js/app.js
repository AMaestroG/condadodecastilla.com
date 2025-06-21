import { initMenu } from './menu.js';
import { initTheme } from './theme.js';
import { initSidebar } from './sidebar.js';

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initMenu();
    initSidebar();
});
