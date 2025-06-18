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

function applyLanguageBarOffset() {
    const el = document.getElementById('google_translate_element');
    if (!el) return;

    const isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
    const offset = isHidden ? 0 : (el.offsetHeight || 40);
    document.documentElement.style.setProperty('--language-bar-offset', offset + 'px');
}

function toggleLanguageBar() {
    const el = document.getElementById('google_translate_element');
    if (!el) return;

    if (!window.googleTranslateLoaded) {
        loadGoogleTranslate();
        window.googleTranslateLoaded = true;
    }

    const isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
    if (isHidden) {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
    applyLanguageBarOffset();
}

function initLangBarToggle() {
    const btn = document.getElementById('lang-bar-toggle');
    if (btn) {
        btn.addEventListener('click', toggleLanguageBar);
    }
    const el = document.getElementById('google_translate_element');
    if (el) {
        el.style.display = 'none';
    }
    applyLanguageBarOffset();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLangBarToggle);
} else {
    initLangBarToggle();
}
