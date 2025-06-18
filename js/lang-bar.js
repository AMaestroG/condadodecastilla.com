function loadGoogleTranslate() {
    if (window.googleTranslateElementInit) {
        window.googleTranslateElementInit();
        return;
    }
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement({
            pageLanguage: 'es',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
    };
    var script = document.createElement('script');
    script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(script);
}

function toggleLanguageBar() {
    const el = document.getElementById('google_translate_element');
    if (!el) return;

    if (!window.googleTranslateLoaded) {
        loadGoogleTranslate();
        window.googleTranslateLoaded = true;
    }

    const isVisible = el.classList.contains('visible');
    if (!isVisible) {
        el.classList.add('visible');
        const offset = el.offsetHeight || 40;
        document.documentElement.style.setProperty('--language-bar-offset', offset + 'px');
    } else {
        el.classList.remove('visible');
        document.documentElement.style.setProperty('--language-bar-offset', '0px');
    }
}

function initLangBarToggle() {
    const btn = document.getElementById('lang-bar-toggle');
    if (btn) {
        btn.addEventListener('click', toggleLanguageBar);
    }
    const el = document.getElementById('google_translate_element');
    if (el) {
        el.classList.remove('visible');
        document.documentElement.style.setProperty('--language-bar-offset', '0px');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLangBarToggle);
} else {
    initLangBarToggle();
}
