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
    }

    function closeMenu(menu){
        menu.classList.remove('active');
        const side = getSide(menu);
        if(side){
            document.body.classList.remove(`menu-open-${side}`);
        }
        document.body.classList.remove('menu-compressed');
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

    function activateTab(tabHeader){
        const targetId = tabHeader.getAttribute('data-tab-target');
        if(!targetId) return;
        const panel = document.getElementById(targetId);
        if(!panel) return;
        const container = tabHeader.closest('.menu-panel.vertical-tabs');
        if(!container) return;
        container.querySelectorAll('.tab-header').forEach(h => {
            h.classList.remove('active');
            h.setAttribute('aria-selected', 'false');
        });
        container.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        tabHeader.classList.add('active');
        tabHeader.setAttribute('aria-selected', 'true');
        panel.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('[data-menu-target]');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => toggleMenu(btn));
        });

        // Tab header click handling
        document.querySelectorAll('.menu-panel.vertical-tabs .tab-header').forEach(header => {
            header.addEventListener('click', () => activateTab(header));
        });

        // Activate first tab by default
        document.querySelectorAll('.menu-panel.vertical-tabs').forEach(panel => {
            const first = panel.querySelector('.tab-header');
            if(first) activateTab(first);
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
