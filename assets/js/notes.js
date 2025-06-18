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

    const closePanel = () => {
        if (panel) panel.classList.remove('active');
        document.body.classList.remove('menu-open-right');
        document.body.classList.remove('menu-compressed');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    };

    if (closeBtn) closeBtn.addEventListener('click', closePanel);
});
