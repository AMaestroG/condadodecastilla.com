// Consolidated menu controller from layout.js and header.js
// Works with panels using .left-panel and .right-panel classes


(function(){
    function getSide(menu){
        if(menu.classList.contains('left-panel')) return 'left';
        if(menu.classList.contains('right-panel')) return 'right';
        return '';
    }

    function openMenu(menu){
        menu.classList.add('active');
        const side = getSide(menu);
        if(side){
            document.body.classList.add(`menu-open-${side}`);
            document.body.classList.add('menu-compressed');
        }
        const button = document.querySelector(`[data-menu-target="${menu.id}"]`);
        if(button){
            button.setAttribute('aria-expanded', 'true');
        }
    }

    function closeMenu(menu){
        menu.classList.remove('active');
        const side = getSide(menu);
        if(side){
            document.body.classList.remove(`menu-open-${side}`);
        }
        document.body.classList.remove('menu-compressed');
        const button = document.querySelector(`[data-menu-target="${menu.id}"]`);
        if(button){
            button.setAttribute('aria-expanded', 'false');
        }
    }

    function toggleMenu(button){
        const targetId = button.getAttribute('data-menu-target');
        if(!targetId) return;
        const menu = document.getElementById(targetId);
        if(!menu) return;
        if(menu.classList.contains('active')){
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
            document.querySelectorAll('.menu-panel.active').forEach(menu => {
                const button = document.querySelector(`[data-menu-target="${menu.id}"]`);
                if(!menu.contains(e.target) && !(button && button.contains(e.target))){
                    closeMenu(menu);
                }
            });
        });

        // Close menus with Escape key
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape'){
                document.querySelectorAll('.menu-panel.active').forEach(menu => closeMenu(menu));
            }
        });
    });
})();
