export function toggleMenu(button, panel) {
    if (!button || !panel) return;
    const isOpen = panel.classList.toggle('open');
    button.setAttribute('aria-expanded', isOpen);
    panel.setAttribute('aria-hidden', !isOpen);
}
