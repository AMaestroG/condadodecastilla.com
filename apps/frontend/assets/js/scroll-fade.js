document.addEventListener('DOMContentLoaded',()=>{
  const items=document.querySelectorAll('.scroll-fade');
  if(items.length){
    const opts={threshold:0.1};
    const observer=new IntersectionObserver((entries,obs)=>{
      entries.forEach(entry=>{
        if(entry.isIntersecting){
          entry.target.classList.add('opacity-100');
          obs.unobserve(entry.target);
        }
      });
    },opts);
    items.forEach(el=>observer.observe(el));
  }
});
