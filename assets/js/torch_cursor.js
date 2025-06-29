(function(){
  document.addEventListener('DOMContentLoaded', function(){
    if ('ontouchstart' in window) return;
    const cursor = document.createElement('div');
    cursor.id = 'torch-cursor';
    document.body.appendChild(cursor);
    let x = window.innerWidth / 2, y = window.innerHeight / 2;
    let targetX = x, targetY = y;
    const lerp = function(a,b,n){ return a + (b - a) * n; };
    document.addEventListener('mousemove', function(e){
      targetX = e.clientX;
      targetY = e.clientY;
    });
    function update(){
      x = lerp(x, targetX, 0.1);
      y = lerp(y, targetY, 0.1);
      cursor.style.left = x + 'px';
      cursor.style.top = y + 'px';
      requestAnimationFrame(update);
    }
    update();
    const focusEls = document.querySelectorAll('a, .clickable');
    focusEls.forEach(function(el){
      el.addEventListener('mouseenter', function(){ cursor.classList.add('focus'); });
      el.addEventListener('mouseleave', function(){ cursor.classList.remove('focus'); });
    });
    window.addEventListener('touchstart', function(){ cursor.style.display = 'none'; }, { once: true });
  });
})();
