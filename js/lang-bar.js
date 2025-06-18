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

    const isHidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
    if (isHidden) {
        el.style.display = 'block';
        const applyOffset = () => {
            const offset = el.offsetHeight || 40;
            document.documentElement.style.setProperty('--language-bar-offset', offset + 'px');
            document.body.style.setProperty('--language-bar-offset', offset + 'px');
        };

        if (el.childElementCount === 0) {
            const observer = new MutationObserver((mutations, obs) => {
                if (el.childElementCount > 0) {
                    applyOffset();
                    obs.disconnect();
                }
            });
            observer.observe(el, { childList: true, subtree: true });
        } else {
            applyOffset();
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
        el.style.display = 'none';
        document.documentElement.style.setProperty('--language-bar-offset', '0px');
        document.body.style.setProperty('--language-bar-offset', '0px');
    }

    const params = new URLSearchParams(window.location.search);
    const lang = params.get('lang');
    if (lang) {
        if (!window.googleTranslateLoaded) {
            loadGoogleTranslate();
            window.googleTranslateLoaded = true;
        }
        document.cookie = 'googtrans=/es/' + lang + ';path=/';
        toggleLanguageBar();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLangBarToggle);
} else {
    initLangBarToggle();
}
