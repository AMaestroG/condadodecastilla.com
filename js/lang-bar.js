function debugLog(...args) { if (window.DEBUG_MODE) { console.debug(...args); } }

function setupLanguageBar() {
    debugLog("setupLanguageBar: Initializing language bar setup.");
    try {
        const lang = new URLSearchParams(window.location.search).get('lang');
        debugLog(`setupLanguageBar: Initial language from URL: ${lang}`);
        const flagLinks = document.querySelectorAll('.language-bar .lang-flag');
        const moreToggle = document.getElementById('more-lang-toggle');
        const additional = document.getElementById('additional-flags');

        flagLinks.forEach(link => {
            link.classList.remove('active-lang');
            link.removeAttribute('aria-current');
        });

        flagLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href').split('lang=')[1];
                debugLog(`setupLanguageBar: Flag clicked, target language: ${target}`);
                handleTranslation(target, flagLinks);
            });
        });

        if (moreToggle && additional) {
            moreToggle.addEventListener('click', () => {
                const expanded = moreToggle.getAttribute('aria-expanded') === 'true';
                moreToggle.setAttribute('aria-expanded', String(!expanded));
                additional.style.display = expanded ? 'none' : 'inline';
            });
        }

        if (lang) {
            flagLinks.forEach(link => {
                if (link.getAttribute('href').includes(`lang=${lang}`)) {
                    link.classList.add('active-lang');
                    link.setAttribute('aria-current', 'true');
                }
            });
            debugLog(`setupLanguageBar: Determined language for translation: ${lang}`);
            loadGoogleTranslate(lang);
        } else {
            // Optional: if no 'lang', highlight 'es' by default if that flag exists
            flagLinks.forEach(link => {
                if (link.getAttribute('href').includes('lang=es')) {
                    link.classList.add('active-lang');
                    link.setAttribute('aria-current', 'true');
                }
            });
            debugLog("setupLanguageBar: No language parameter in URL, or default language selected.");
        }
    } catch (error) {
        console.error("setupLanguageBar: Error during setup:", error);
    }
}

function handleTranslation(lang, flagLinks) {
    const params = new URLSearchParams(window.location.search);
    params.set('lang', lang);
    history.replaceState(null, '', '?' + params.toString());

    flagLinks.forEach(l => {
        if (l.getAttribute('href').includes('lang=' + lang)) {
            l.classList.add('active-lang');
            l.setAttribute('aria-current', 'true');
        } else {
            l.classList.remove('active-lang');
            l.removeAttribute('aria-current');
        }
    });

    if (typeof google !== 'undefined' && google.translate && google.translate.TranslateElement) {
        translatePage(lang);
    } else {
        loadGoogleTranslate(lang);
    }
}

function loadGoogleTranslate(targetLang, attempt = 1) {
    debugLog(`loadGoogleTranslate: Attempting to load Google Translate for language: ${targetLang}, attempt: ${attempt}`);
    try {
        window._targetLang = targetLang; // Store targetLang globally for access in callback

        // Ensure googleTranslateElementInit is defined robustly
        if (typeof window.googleTranslateElementInit !== 'function') {
            window.googleTranslateElementInit = function() {
                debugLog("loadGoogleTranslate: googleTranslateElementInit called.");
                try {
                    new google.translate.TranslateElement({
                        pageLanguage: 'es', // Assuming 'es' is the original page language
                        includedLanguages: 'ar,bn,de,el,en,es,fa,fr,he,hi,id,it,ja,ko,nl,pl,pt,ru,sv,sw,th,tr,uk,ur,vi,zh-CN', // Updated languages
                        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                    }, 'google_translate_element');
                    // It's important to call translatePage AFTER the element is initialized.
                    // However, the TranslateElement constructor itself triggers the first translation
                    // if a language cookie/param is set, or based on browser settings.
                    // We might still want to force it if the auto-detection isn't what we want.
                    translatePage(window._targetLang);
                } catch (error) {
                    console.error("loadGoogleTranslate: Error initializing TranslateElement:", error);
                }
            };
        }

        // Check if the script is already loaded or if the Translate API is available
        if (typeof google !== 'undefined' && google.translate && google.translate.TranslateElement) {
            debugLog("loadGoogleTranslate: Google Translate API already available.");
            // If API is there, but our specific init function was perhaps missed or needs re-triggering for a new language
            window.googleTranslateElementInit();
            return;
        }

        // Proceed to load the script if not already loaded
        if (!document.querySelector('script[src*="translate.google.com"]')) {
            debugLog("loadGoogleTranslate: Creating and appending Google Translate script.");
            const script = document.createElement('script');
            script.src = `//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit`;
            script.async = true;
            script.onerror = function() {
                console.error(`loadGoogleTranslate: Failed to load Google Translate script (attempt ${attempt}).`);
                document.head.removeChild(script); // Clean up failed script tag
                if (attempt < 3) {
                    setTimeout(() => loadGoogleTranslate(targetLang, attempt + 1), 1000 * attempt); // Exponential backoff
                } else {
                    console.error("loadGoogleTranslate: Max retries reached for loading Google Translate script.");
                    // Optionally, inform the user that translation services are unavailable
                    // alert("Translation services could not be loaded. Please try again later.");
                }
            };
            document.head.appendChild(script);
        } else if (typeof google === 'undefined' || !google.translate) {
            // Script tag exists, but google.translate is not yet available.
            // This can happen if the script is still loading.
            // googleTranslateElementInit will be called by the script's cb parameter.
            debugLog("loadGoogleTranslate: Google Translate script tag found, but API not yet available. Waiting for cb.");
        } else {
            // Script tag exists, and API is available.
            // This implies googleTranslateElementInit should have been called.
            // If we need to re-translate to a new language, call translatePage directly.
            debugLog("loadGoogleTranslate: Google Translate script and API present. Translating page.");
            translatePage(targetLang);
        }

    } catch (error) {
        console.error(`loadGoogleTranslate: Error loading Google Translate (attempt ${attempt}):`, error);
        if (attempt < 3) {
            setTimeout(() => loadGoogleTranslate(targetLang, attempt + 1), 1000 * attempt);
        } else {
            console.error("loadGoogleTranslate: Max retries reached after critical error.");
        }
    }
}

function translatePage(lang) {
    debugLog(`translatePage: Attempting to translate page to language: ${lang}`);
    try {
        if (typeof google === 'undefined' || typeof google.translate === 'undefined' || typeof google.translate.TranslateElement === 'undefined') {
            console.warn("translatePage: Google Translate API not available. Skipping translation.");
            return;
        }

        // The existence of 'google_translate_element' div is assumed to be handled by googleTranslateElementInit.
        // Retry mechanism for accessing the combo box.
        let attempts = 0;
        const maxAttempts = 3;
        const retryDelay = 500; // ms

        function findAndTranslate() {
            attempts++;
            debugLog(`translatePage: Attempt ${attempts} to find Google Translate combo box for language ${lang}.`);
            const combo = document.querySelector('select.goog-te-combo');

            if (combo) {
                if (combo.value !== lang) {
                    combo.value = lang;
                    debugLog(`translatePage: Set combo value to ${lang}. Dispatching change event.`);
                    combo.dispatchEvent(new Event('change'));
                } else {
                    debugLog(`translatePage: Combo value already ${lang}. No change needed.`);
                }
            } else {
                if (attempts < maxAttempts) {
                    debugLog(`translatePage: Combo box not found on attempt ${attempts}. Retrying in ${retryDelay}ms.`);
                    setTimeout(findAndTranslate, retryDelay);
                } else {
                    console.warn(`translatePage: Google Translate combo box not found after ${maxAttempts} attempts. Translation might not occur as expected. This can happen if the Translate Element hasn't fully initialized or if the page structure changed.`);
                }
            }
        }

        findAndTranslate();

    } catch (error) {
        console.error(`translatePage: Error translating page to ${lang}:`, error);
    }
}

try {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupLanguageBar);
    } else {
        setupLanguageBar();
    }
} catch (e) {
    console.error("Error during initial execution/setup of lang-bar.js:", e);
    // Optionally, a user-facing alert for diagnostics if this phase is critical
    // alert("Critical error in language bar initial setup. Some features might be affected.");
}

window.initLanguageBar = setupLanguageBar; // Ensure this is always assigned
