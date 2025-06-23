let bannerObserver;

function observeTranslateBanner() {
    if (bannerObserver) return;

    const body = document.body;
    const checkBanner = () => {
        const visible = !!document.querySelector('.goog-te-banner-frame');
        body.classList.toggle('lang-bar-visible', visible);
        if (!visible && bannerObserver) {
            bannerObserver.disconnect();
            bannerObserver = null;
        }
    };

    bannerObserver = new MutationObserver(checkBanner);
    bannerObserver.observe(document.documentElement, { childList: true, subtree: true });
    checkBanner();
}

function loadGoogleTranslate(callback) {
    if (window.googleTranslateElementInit) {
        window.googleTranslateElementInit();
        observeTranslateBanner();
        if (typeof callback === 'function') callback();
        return;
    }
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement({
            pageLanguage: 'es',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');
        observeTranslateBanner();
        if (typeof callback === 'function') callback();
    };
    const script = document.createElement('script');
    script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    script.async = true;
    script.onload = function() { window.googleTranslateLoaded = true; };
    script.onerror = function() { console.error('Failed to load Google Translate script'); };
    document.head.appendChild(script);
}



function primeTranslateLoad() {
    if (!window.googleTranslateLoaded) {
        loadGoogleTranslate();
        window.googleTranslateLoaded = true;
    }
}

function initFlagPanel() {
    const btn = document.getElementById('flag-toggle');
    if (btn) {
        btn.addEventListener('click', primeTranslateLoad);
    }
    document.querySelectorAll('#language-panel img[data-lang]').forEach(flag => {
        flag.addEventListener('click', () => {
            const lang = flag.getAttribute('data-lang');
            const translate = () => {
                document.cookie = 'googtrans=/es/' + lang + ';path=/';
                location.reload();
            };
            if (!window.googleTranslateLoaded) {
                loadGoogleTranslate(translate);
                window.googleTranslateLoaded = true;
            } else {
                translate();
            }
        });
    });
}


function setupLanguageBar() {
    initFlagPanel();
}


if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupLanguageBar);
} else {
    setupLanguageBar();
}
