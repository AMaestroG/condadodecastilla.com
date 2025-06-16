// Foro page interactions
document.addEventListener('DOMContentLoaded', () => {
    const menu = document.getElementById('slide-menu');
    document.getElementById('menu-btn').addEventListener('click', () => {
        menu.classList.toggle('open');
    });
});
