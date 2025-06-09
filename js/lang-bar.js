function setupLanguageBar() {
    const lang = new URLSearchParams(window.location.search).get('lang');
    const flagLinks = document.querySelectorAll('.language-bar .lang-flag');

    flagLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href').split('lang=')[1];
            const params = new URLSearchParams(window.location.search);
            if (target === 'es') {
                params.delete('lang');
            } else {
                params.set('lang', target);
            }
            window.location.search = params.toString();
        });
    });

    if (lang && lang !== 'es') {
        loadGoogleTranslate(lang);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupLanguageBar);
} else {
    setupLanguageBar();
}

window.initLanguageBar = setupLanguageBar;

function loadGoogleTranslate(targetLang) {
    window._targetLang = targetLang;
    if (!window.googleTranslateElementInit) {
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement({pageLanguage: 'es', includedLanguages: 'en,es,fr,de,it,pt,ru,zh-CN,ja,ko,ar,hi'}, 'google_translate_element');
            translatePage(window._targetLang);
        };
        const script = document.createElement('script');
        script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        document.head.appendChild(script);
    } else {
        translatePage(targetLang);
    }
}

function translatePage(lang) {
    const combo = document.querySelector('select.goog-te-combo');
    if (combo) {
        combo.value = lang;
        combo.dispatchEvent(new Event('change'));
    }
}
