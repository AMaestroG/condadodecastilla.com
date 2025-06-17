// Foro page interactions
document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('agents-menu');
    const btn = document.getElementById('menu-btn');
    if (menu && btn) {
        btn.addEventListener('click', () => {
            menu.classList.toggle('open');
        });
    }
});
