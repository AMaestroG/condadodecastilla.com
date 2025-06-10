(function(){
    async function loadFragment(selector, url) {
        const container = document.querySelector(selector);
        if (!container) {
            // alert(`DEBUG: Container not found for selector: ${selector}`); // Optional: more granular alert
            return;
        }
        try {
            // alert(`DEBUG: Fetching ${url} for ${selector}`); // Optional
            const html = await fetch(url).then(r => r.text());
            container.innerHTML = html;
            // alert(`DEBUG: Loaded ${url} into ${selector}`); // Optional
        } catch (err) {
            console.error('Error loading fragment', url, err);
            // alert(`DEBUG: ERROR loading ${url} for ${selector}: ${err.message}`); // Optional
        }
    }

    async function init() {
        alert("INIT_START");

        await Promise.all([
            loadFragment('#header-language-bar-placeholder', '/fragments/header/language-bar.html'),
            loadFragment('#header-toggles-placeholder', '/fragments/header/toggles.html'),
            loadFragment('#header-navigation-placeholder', '/fragments/header/navigation.html'),
            loadFragment('#header-ia-chat-placeholder', '/fragments/header/ia-chat.html')
        ]);

        alert("AFTER_FIRST_PROMISE_ALL");

        await Promise.all([
            loadFragment('#main-menu-placeholder', '/fragments/menus/main-menu.html'),
            loadFragment('#admin-menu-placeholder', '/fragments/menus/admin-menu.php'),
            loadFragment('#social-menu-placeholder', '/fragments/menus/social-menu.html')
        ]);

        alert("AFTER_SECOND_PROMISE_ALL");

        if (window.initLanguageBar) {
            alert("BEFORE_INIT_LANG_BAR");
            try {
                window.initLanguageBar();
                alert("AFTER_INIT_LANG_BAR_SUCCESS");
            } catch (e) {
                console.error("Error initializing language bar:", e);
                alert("ERROR_INIT_LANG_BAR: " + e.message);
            }
        } else {
            alert("NO_INIT_LANG_BAR_FUNCTION");
        }

        // Call layout initializers after all fragments are loaded
        if (window.initializeSidebarNavigation) {
            alert("BEFORE_INIT_SIDEBAR_NAV");
            try {
                window.initializeSidebarNavigation();
                alert("AFTER_INIT_SIDEBAR_NAV_SUCCESS");
            } catch (e) {
                console.error("Error initializing sidebar navigation:", e);
                alert("ERROR_INIT_SIDEBAR_NAV: " + e.message);
            }
        } else {
            alert("NO_INIT_SIDEBAR_NAV_FUNCTION");
        }

        if (window.initializeThemeToggle) {
            alert("BEFORE_INIT_THEME_TOGGLE");
            try {
                window.initializeThemeToggle();
                alert("AFTER_INIT_THEME_TOGGLE_SUCCESS");
            } catch (e) {
                console.error("Error initializing theme toggle:", e);
                alert("ERROR_INIT_THEME_TOGGLE: " + e.message);
            }
        } else {
            alert("NO_INIT_THEME_TOGGLE_FUNCTION");
        }

        if (window.initializeHomonexusToggle) {
            alert("BEFORE_INIT_HOMONEXUS_TOGGLE");
            try {
                window.initializeHomonexusToggle();
                alert("AFTER_INIT_HOMONEXUS_TOGGLE_SUCCESS");
            } catch (e) {
                console.error("Error initializing homonexus toggle:", e);
                alert("ERROR_INIT_HOMONEXUS_TOGGLE: " + e.message);
            }
        } else {
            alert("NO_INIT_HOMONEXUS_TOGGLE_FUNCTION");
        }

        if (window.initializeIAChatSidebar) {
            alert("BEFORE_INIT_IA_CHAT_SIDEBAR");
            try {
                window.initializeIAChatSidebar();
                alert("AFTER_INIT_IA_CHAT_SIDEBAR_SUCCESS");
            } catch (e) {
                console.error("Error initializing IA chat sidebar:", e);
                alert("ERROR_INIT_IA_CHAT_SIDEBAR: " + e.message);
            }
        } else {
            alert("NO_INIT_IA_CHAT_SIDEBAR_FUNCTION");
        }

        if (window.loadIAToolsScript) {
            alert("BEFORE_LOAD_IA_TOOLS_SCRIPT");
            try {
                window.loadIAToolsScript();
                alert("AFTER_LOAD_IA_TOOLS_SCRIPT_SUCCESS");
            } catch (e) {
                console.error("Error loading IA tools script:", e);
                alert("ERROR_LOAD_IA_TOOLS_SCRIPT: " + e.message);
            }
        } else {
            alert("NO_LOAD_IA_TOOLS_SCRIPT_FUNCTION");
        }

        alert("INIT_COMPLETED_FULLY");
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
