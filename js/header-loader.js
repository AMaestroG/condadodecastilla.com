(function(){
    document.addEventListener('DOMContentLoaded', function(){
        const placeholder = document.getElementById('header-placeholder');
        if(!placeholder) return;
        fetch('/_header.php')
            .then(resp => resp.text())
            .then(html => {
                placeholder.innerHTML = html;
                if (typeof setupLanguageBar === 'function') {
                    setupLanguageBar();
                }
                if (typeof applyLanguageBarOffset === 'function') {
                    applyLanguageBarOffset();
                }
            })
            .catch(err => console.error('Error loading header:', err));
    });
})();
