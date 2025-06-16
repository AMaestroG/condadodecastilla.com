// Consolidated menu controller from layout.js and header.js
// Toggles elements with class .slide-menu based on data attributes

(function(){
    function toggleMenu(button){
        const targetId = button.getAttribute('data-menu-target');
        if(!targetId) return;
        const menu = document.getElementById(targetId);
        if(!menu) return;
        const side = menu.classList.contains('left') ? 'left' : (menu.classList.contains('right') ? 'right' : '');
        menu.classList.toggle('open');
        if(side){
            const openClass = `menu-open-${side}`;
            document.body.classList.toggle(openClass, menu.classList.contains('open'));
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-menu-target]').forEach(btn => {
            btn.addEventListener('click', () => toggleMenu(btn));
        });
    });
})();
