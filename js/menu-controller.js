// Consolidated menu controller from layout.js and header.js
// Toggles elements with class .slide-menu based on data attributes


(function(){
    function getSide(menu){
        if(menu.classList.contains('left')) return 'left';
        if(menu.classList.contains('right')) return 'right';
        return '';
    }

    function openMenu(menu){
        menu.classList.add('open');
        const side = getSide(menu);
        if(side){
            document.body.classList.add(`menu-open-${side}`);
        }
    }

    function closeMenu(menu){
        menu.classList.remove('open');
        const side = getSide(menu);
        if(side){
            document.body.classList.remove(`menu-open-${side}`);
        }
    }

    function toggleMenu(button){
        const targetId = button.getAttribute('data-menu-target');
        if(!targetId) return;
        const menu = document.getElementById(targetId);
        if(!menu) return;
        if(menu.classList.contains('open')){
            closeMenu(menu);
        }else{
            openMenu(menu);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('[data-menu-target]');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => toggleMenu(btn));
        });

        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            document.querySelectorAll('.slide-menu.open').forEach(menu => {
                const button = document.querySelector(`[data-menu-target="${menu.id}"]`);
                if(!menu.contains(e.target) && !(button && button.contains(e.target))){
                    closeMenu(menu);
                }
            });
        });

        // Close menus with Escape key
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape'){
                document.querySelectorAll('.slide-menu.open').forEach(menu => closeMenu(menu));
            }
        });
    });
})();
