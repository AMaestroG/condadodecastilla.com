(function(){
    function loadGoogleTranslate(){
        if (typeof window.googleTranslateElementInit !== 'function') {
            window.googleTranslateElementInit = function() {
                new google.translate.TranslateElement({pageLanguage:'es'}, 'google_translate_element');
            };
        }
        if (!document.querySelector('script[src*="translate.google.com"]')) {
            var s = document.createElement('script');
            s.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            document.head.appendChild(s);
        } else if (window.google && google.translate && google.translate.TranslateElement) {
            window.googleTranslateElementInit();
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadGoogleTranslate);
    } else {
        loadGoogleTranslate();
    }
})();
