(function(){
  document.addEventListener('DOMContentLoaded',function(){
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) return;
    const pointerSrc=document.currentScript?.dataset.pointerSrc||document.body.dataset.pointerSrc;
    document.body.classList.add('cursor-none');
    const wrapper=document.createElement('div');
    wrapper.id='custom-pointer';
    const outer=document.createElement('div');
    outer.className='outer';
    const inner=document.createElement('div');
    inner.className='inner';
    wrapper.appendChild(outer);
    wrapper.appendChild(inner);
    let img;
    if(pointerSrc){
      img=new Image();
      img.className='pointer-img';
      img.src=pointerSrc;
      img.style.position='absolute';
      img.style.top='50%';
      img.style.left='50%';
      img.style.width='2.5rem';
      img.style.height='2.5rem';
      img.style.transform='translate(-50%,-50%)';
      img.style.pointerEvents='none';
      wrapper.appendChild(img);
    }
    document.body.appendChild(wrapper);
    let x=window.innerWidth/2, y=window.innerHeight/2;
    let tx=x, ty=y;
    const lerp=(a,b,n)=> a+(b-a)*n;
    const moveHandler=function(e){
      tx=e.clientX;
      ty=e.clientY;
    };
    document.addEventListener('mousemove',moveHandler);
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

    function cleanup(){
      document.removeEventListener('mousemove',moveHandler);
      wrapper.remove();
    }

    window.addEventListener('pagehide',cleanup);
    window.addEventListener('beforeunload',cleanup);
    window.disableCustomPointer=cleanup;
  });
})();
