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
    document.querySelectorAll('#language-panel button[data-lang]').forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
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
