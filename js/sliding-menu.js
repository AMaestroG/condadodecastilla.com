(function(){
    function toggleMenu(button){
        var targetId = button.getAttribute('data-menu-target');
        if(!targetId) return;
        var menu = document.getElementById(targetId);
        if(!menu) return;
        var body = document.body;
        var side = menu.classList.contains('left') ? 'left' : 'right';
        var width = menu.offsetWidth || 260;
        var openClass = 'menu-open-' + side;
        var isOpen = menu.classList.contains('open');

        if(!window.gsap){
            menu.classList.toggle('open');
            body.classList.toggle(openClass, !isOpen);
            return;
        }

        if(isOpen){
            gsap.to(menu, { x: side==='left' ? '-100%' : '100%', duration:0.35, ease:'power2.in', onComplete:function(){
                menu.classList.remove('open');
                menu.style.transform='';
            }});
            gsap.to(body, { x:0, duration:0.35, ease:'power2.in', onComplete:function(){
                body.classList.remove(openClass);
                body.style.transform='';
            }});
        } else {
            menu.classList.add('open');
            body.classList.add(openClass);
            gsap.fromTo(menu, { x: side==='left' ? '-100%' : '100%' }, { x:'0%', duration:0.35, ease:'power2.out', clearProps:'transform' });
            gsap.to(body, { x: side==='left' ? width : -width, duration:0.35, ease:'power2.out', clearProps:'transform' });
        }
    }

    function init(){
        document.querySelectorAll('[data-menu-target]').forEach(function(btn){
            btn.addEventListener('click', function(){ toggleMenu(btn); });
        });
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
