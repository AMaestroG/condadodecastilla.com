// assets/js/help.js - simple help panel toggle

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('help-toggle');
    const panel = document.getElementById('help-panel');

    if (!toggle || !panel) return;

    const applyState = () => {
        const closed = sessionStorage.getItem('helpClosed') === 'true';
        panel.classList.toggle('hidden', closed);
        toggle.setAttribute('aria-expanded', (!closed).toString());
        panel.setAttribute('aria-hidden', closed.toString());
    };

    applyState();

    const togglePanel = () => {
        const currentlyHidden = panel.classList.contains('hidden');
        panel.classList.toggle('hidden');
        const hidden = !currentlyHidden;
        sessionStorage.setItem('helpClosed', hidden.toString());
        toggle.setAttribute('aria-expanded', (!hidden).toString());
        panel.setAttribute('aria-hidden', hidden.toString());
    };

    toggle.addEventListener('click', togglePanel);
    const closeBtn = document.getElementById('help-close');
    if (closeBtn) closeBtn.addEventListener('click', togglePanel);
});
