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


function toggleFlagPanel() {
    const panel = document.getElementById('language-panel');
    const btn = document.getElementById('flag-toggle');
    if (!panel) return;
    const open = panel.classList.toggle('active');
    if (open && !window.googleTranslateLoaded) {
        loadGoogleTranslate();
        window.googleTranslateLoaded = true;
    }
    if (btn) btn.setAttribute('aria-expanded', open);
    document.body.classList.toggle('menu-open-right', open);
    document.body.classList.toggle('menu-compressed', open);
}

function initFlagPanel() {
    const btn = document.getElementById('flag-toggle');
    if (btn) {
        btn.addEventListener('click', toggleFlagPanel);
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
