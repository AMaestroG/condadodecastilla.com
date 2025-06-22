document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    if (!toggle || !menu) return;
    toggle.addEventListener('click', () => {
        const opened = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', opened);
        document.body.classList.toggle('menu-compressed', opened);
    });
});
