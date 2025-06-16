(function(){
    function toggleMenu(button){
        const targetId = button.getAttribute('data-menu-target');
        if(!targetId) return;
        const menu = document.getElementById(targetId);
        if(!menu) return;
        const side = menu.classList.contains('left') ? 'left' : (menu.classList.contains('right') ? 'right' : '');
        const isOpen = menu.classList.toggle('open');
        if(side){
            document.body.classList.toggle(`menu-open-${side}`, isOpen);
        }
        button.setAttribute('aria-expanded', isOpen);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-menu-target]').forEach(btn => {
            const targetId = btn.getAttribute('data-menu-target');
            if(!targetId) return;
            btn.setAttribute('aria-controls', targetId);
            btn.setAttribute('aria-expanded', 'false');
            btn.addEventListener('click', () => toggleMenu(btn));
            btn.addEventListener('keydown', e => {
                if(e.key === 'Enter' || e.key === ' '){
                    e.preventDefault();
                    toggleMenu(btn);
                }
            });
        });
    });
})();
