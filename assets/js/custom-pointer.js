(function(){
  document.addEventListener('DOMContentLoaded',function(){
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) return;
    document.body.classList.add('cursor-none');
    const wrapper=document.createElement('div');
    wrapper.id='custom-pointer';
    const outer=document.createElement('div');
    outer.className='outer';
    const inner=document.createElement('div');
    inner.className='inner';
    wrapper.appendChild(outer);
    wrapper.appendChild(inner);
    document.body.appendChild(wrapper);
    let x=window.innerWidth/2, y=window.innerHeight/2;
    let tx=x, ty=y;
    const lerp=(a,b,n)=> a+(b-a)*n;
    document.addEventListener('mousemove',function(e){
      tx=e.clientX;
      ty=e.clientY;
    });
    function render(){
      x=lerp(x,tx,0.2);
      y=lerp(y,ty,0.2);
      wrapper.style.left=x+'px';
      wrapper.style.top=y+'px';
      requestAnimationFrame(render);
    }
    render();
    document.querySelectorAll('.interactivo').forEach(function(el){
      el.addEventListener('mouseenter',function(){wrapper.classList.add('active');});
      el.addEventListener('mouseleave',function(){wrapper.classList.remove('active');});
    });
  });
})();
