function setupLanguageBar() {
    console.log("setupLanguageBar: Initializing language bar setup.");
    try {
        const lang = new URLSearchParams(window.location.search).get('lang');
        console.log(`setupLanguageBar: Initial language from URL: ${lang}`);
        const flagLinks = document.querySelectorAll('.language-bar .lang-flag');

        flagLinks.forEach(link => {
            link.classList.remove('active-lang');
            link.removeAttribute('aria-current');
        });

        flagLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href').split('lang=')[1];
                console.log(`setupLanguageBar: Flag clicked, target language: ${target}`);
                const params = new URLSearchParams(window.location.search);
                params.set('lang', target); // Always use params.set as requested
                window.location.search = params.toString();
            });
        });

        if (lang) {
            flagLinks.forEach(link => {
                if (link.getAttribute('href').includes(`lang=${lang}`)) {
                    link.classList.add('active-lang');
                    link.setAttribute('aria-current', 'true');
                }
            });
            console.log(`setupLanguageBar: Determined language for translation: ${lang}`);
            loadGoogleTranslate(lang);
        } else {
            // Optional: if no 'lang', highlight 'es' by default if that flag exists
            flagLinks.forEach(link => {
                if (link.getAttribute('href').includes('lang=es')) {
                    link.classList.add('active-lang');
                    link.setAttribute('aria-current', 'true');
                }
            });
            console.log("setupLanguageBar: No language parameter in URL, or default language selected.");
        }
    } catch (error) {
        console.error("setupLanguageBar: Error during setup:", error);
    }
}

function loadGoogleTranslate(targetLang, attempt = 1) {
    console.log(`loadGoogleTranslate: Attempting to load Google Translate for language: ${targetLang}, attempt: ${attempt}`);
    try {
        window._targetLang = targetLang; // Store targetLang globally for access in callback

        // Ensure googleTranslateElementInit is defined robustly
        if (typeof window.googleTranslateElementInit !== 'function') {
            window.googleTranslateElementInit = function() {
                console.log("loadGoogleTranslate: googleTranslateElementInit called.");
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
            console.log("loadGoogleTranslate: Google Translate API already available.");
            // If API is there, but our specific init function was perhaps missed or needs re-triggering for a new language
            window.googleTranslateElementInit();
            return;
        }

        // Proceed to load the script if not already loaded
        if (!document.querySelector('script[src*="translate.google.com"]')) {
            console.log("loadGoogleTranslate: Creating and appending Google Translate script.");
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
            console.log("loadGoogleTranslate: Google Translate script tag found, but API not yet available. Waiting for cb.");
        } else {
            // Script tag exists, and API is available.
            // This implies googleTranslateElementInit should have been called.
            // If we need to re-translate to a new language, call translatePage directly.
            console.log("loadGoogleTranslate: Google Translate script and API present. Translating page.");
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
    console.log(`translatePage: Attempting to translate page to language: ${lang}`);
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
            console.log(`translatePage: Attempt ${attempts} to find Google Translate combo box for language ${lang}.`);
            const combo = document.querySelector('select.goog-te-combo');

            if (combo) {
                if (combo.value !== lang) {
                    combo.value = lang;
                    console.log(`translatePage: Set combo value to ${lang}. Dispatching change event.`);
                    combo.dispatchEvent(new Event('change'));
                } else {
                    console.log(`translatePage: Combo value already ${lang}. No change needed.`);
                }
            } else {
                if (attempts < maxAttempts) {
                    console.log(`translatePage: Combo box not found on attempt ${attempts}. Retrying in ${retryDelay}ms.`);
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
