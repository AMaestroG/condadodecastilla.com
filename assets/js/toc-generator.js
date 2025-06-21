(function(){
  function slugify(text){
    return text.toString().toLowerCase()
      .trim()
      .replace(/\s+/g,'-')
      .replace(/[^\w\-]+/g,'')
      .replace(/\-\-+/g,'-');
  }

  window.generateTOC = function(options={}){
    const content = document.querySelector(options.contentSelector || 'main');
    if(!content) return;
    const headings = content.querySelectorAll(options.headingSelector || 'h2, h3, h4');
    if(!headings.length) return;

    const toc = document.createElement('nav');
    toc.className = options.containerClass || 'bg-white/80 backdrop-blur-md p-4 rounded shadow-md text-sm';
    const list = document.createElement('ul');
    list.className = 'space-y-2';

    headings.forEach(h => {
      if(!h.id) h.id = slugify(h.textContent);
      const depth = parseInt(h.tagName.substring(1)) - 2;
      const item = document.createElement('li');
      item.className = depth>0 ? `ml-${depth*2}` : '';
      const link = document.createElement('a');
      link.href = '#'+h.id;
      link.textContent = h.textContent;
      link.className = 'text-purple hover:text-old-gold';
      item.appendChild(link);
      list.appendChild(item);
    });

    toc.appendChild(list);
    const target = document.querySelector(options.targetSelector || '#toc');
    if(target){
      target.appendChild(toc);
    } else {
      content.prepend(toc);
    }
  };
})();
