// assets/js/notes.js

document.addEventListener('DOMContentLoaded', () => {
    const panel = document.getElementById('notes-panel');
    const trigger = document.getElementById('notes-trigger');
    const closeBtn = document.getElementById('close-notes-panel');
    const textarea = document.getElementById('user-notes');

    if (textarea) {
        textarea.value = localStorage.getItem('user-notes') || '';
        textarea.addEventListener('input', () => {
            localStorage.setItem('user-notes', textarea.value);
        });
    }

    const openPanel = () => {
        if (panel) panel.classList.add('active');
        document.body.classList.add('menu-open-right', 'menu-compressed');
        if (trigger) trigger.setAttribute('aria-expanded', 'true');
    };

    const closePanel = () => {
        if (panel) panel.classList.remove('active');
        document.body.classList.remove('menu-open-right', 'menu-compressed');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    };

    if (trigger) trigger.addEventListener('click', (e) => { e.stopPropagation(); openPanel(); });
    if (closeBtn) closeBtn.addEventListener('click', closePanel);

    document.addEventListener('click', (e) => {
        if (panel && panel.classList.contains('active')) {
            if (!panel.contains(e.target) && !trigger.contains(e.target)) {
                closePanel();
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel && panel.classList.contains('active')) {
            closePanel();
        }
    });
});
