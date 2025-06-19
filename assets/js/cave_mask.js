document.addEventListener('DOMContentLoaded',()=>{
    const mask=document.getElementById('cave-mask');
    if(!mask) return;
    const shrink=()=>mask.style.setProperty('--mask-radius','20vw');
    document.addEventListener('mousemove',e=>{
        mask.style.setProperty('--mask-x',`${e.clientX}px`);
        mask.style.setProperty('--mask-y',`${e.clientY}px`);
        mask.style.setProperty('--mask-radius','40vw');
        mask.style.opacity='1';
        clearTimeout(mask._t); mask._t=setTimeout(()=>{mask.style.opacity='0.3';shrink();},200);
    });
});
