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

    const isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';

    const updateOffset = () => {
        const waitForHeight = () => {
            const h = el.offsetHeight;
            if (h === 0) {
                requestAnimationFrame(waitForHeight);
            } else {
                document.documentElement.style.setProperty('--language-bar-offset', h + 'px');
                document.body.style.setProperty('--language-bar-offset', h + 'px');
            }
        };
        waitForHeight();
    };

    if (isHidden) {
        const showBar = () => {
            el.style.display = 'block';
            updateOffset();
        };

        if (!window.googleTranslateLoaded) {
            loadGoogleTranslate(showBar);
        } else {
            showBar();
        }
    } else {
        el.style.display = 'none';
        document.documentElement.style.setProperty('--language-bar-offset', '0px');
        document.body.style.setProperty('--language-bar-offset', '0px');
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
        document.documentElement.style.setProperty('--language-bar-offset', '0px');
        document.body.style.setProperty('--language-bar-offset', '0px');
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

function applyLanguageBarOffset() {
    const el = document.getElementById('google_translate_element');
    const isHidden = !el || el.style.display === 'none' || getComputedStyle(el).display === 'none';
    const offset = (!isHidden && el.offsetHeight) ? el.offsetHeight : 0;
    document.documentElement.style.setProperty('--language-bar-offset', offset + 'px');
    document.body.style.setProperty('--language-bar-offset', offset + 'px');
    const extra = getComputedStyle(document.documentElement).getPropertyValue('--menu-extra-offset') || '60px';
    document.documentElement.style.setProperty('--menu-extra-offset', extra.trim());
    document.body.style.setProperty('--menu-extra-offset', extra.trim());
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupLanguageBar);
} else {
    setupLanguageBar();
}
