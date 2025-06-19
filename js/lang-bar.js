function loadGoogleTranslate(callback) {
    if (window.googleTranslateElementInit) {
        window.googleTranslateElementInit();
        if (typeof callback === 'function') callback();
        return;
    }
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement({
            pageLanguage: 'es',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
        if (typeof callback === 'function') callback();
    };
    var script = document.createElement('script');
    script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    script.async = true;
    script.onload = function() { window.googleTranslateLoaded = true; };
    script.onerror = function() { console.error('Failed to load Google Translate script'); };
    document.head.appendChild(script);
}

function toggleLanguageBar() {
    const el = document.getElementById('google_translate_element');
    if (!el) return;
    const body = document.body;

    const isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';


    if (isHidden) {
        const showBar = () => {
            el.style.display = 'block';
            body.classList.add('lang-bar-visible');
        };

        if (!window.googleTranslateLoaded) {
            loadGoogleTranslate(showBar);
        } else {
            showBar();
        }
    } else {
        el.style.display = 'none';
        body.classList.remove('lang-bar-visible');
    }
}

function initLangBarToggle() {
    const btn = document.getElementById('lang-bar-toggle');
    if (btn) {
        btn.addEventListener('click', toggleLanguageBar);
    }
    const el = document.getElementById('google_translate_element');
    if (el) {
        el.addEventListener('click', toggleLanguageBar);
        el.style.display = 'none';
    }

    const params = new URLSearchParams(window.location.search);
    const lang = params.get('lang');
    if (lang) {
        const startTranslation = () => {
            document.cookie = 'googtrans=/es/' + lang + ';path=/';
            toggleLanguageBar();
        };
        if (!window.googleTranslateLoaded) {
            loadGoogleTranslate(startTranslation);
            window.googleTranslateLoaded = true;
        } else {
            startTranslation();
        }
    }
}

function setupLanguageBar() {
    initLangBarToggle();
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupLanguageBar);
} else {
    setupLanguageBar();
}
