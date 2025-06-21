(function(){
    function loadScript(src, callback){
        var s = document.createElement('script');
        s.src = src;
        s.onload = callback || function(){};
        document.head.appendChild(s);
    }
    if(!window.Promise){
        loadScript('https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js');
    }
    if(!window.fetch){
        loadScript('https://cdn.jsdelivr.net/npm/whatwg-fetch@3/dist/fetch.umd.js');
    }
    if(!('IntersectionObserver' in window)){
        loadScript('https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver');
    }
    if(typeof HTMLDialogElement === 'undefined'){
        loadScript('https://cdn.jsdelivr.net/npm/dialog-polyfill@0.5/dist/dialog-polyfill.min.js', function(){
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/dialog-polyfill@0.5/dist/dialog-polyfill.min.css';
            document.head.appendChild(link);
        });
    }
})();
