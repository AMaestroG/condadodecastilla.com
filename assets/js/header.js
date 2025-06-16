document.addEventListener('DOMContentLoaded', () => {
    const leftMenu = document.getElementById('slide-menu-left');
    const rightMenu = document.getElementById('slide-menu-right');
    const menuBtn = document.getElementById('menu-button');
    const toolsBtn = document.getElementById('tools-button');

    if (menuBtn && leftMenu) {
        menuBtn.addEventListener('click', () => {
            leftMenu.classList.toggle('open');
            document.body.classList.toggle('menu-open-left');
        });
    }
    if (toolsBtn && rightMenu) {
        toolsBtn.addEventListener('click', () => {
            rightMenu.classList.toggle('open');
            document.body.classList.toggle('menu-open-right');
        });
    }
});
