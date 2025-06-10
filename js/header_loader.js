(function(){
    async function loadFragment(selector, url) {
        const container = document.querySelector(selector);
        if (!container) return;
        try {
            const html = await fetch(url).then(r => r.text());
            container.innerHTML = html;
        } catch (err) {
            console.error('Error loading fragment', url, err);
        }
    }

    async function init() {
        await Promise.all([
            loadFragment('#header-language-bar-placeholder', '/fragments/header/language-bar.html'),
            loadFragment('#header-toggles-placeholder', '/fragments/header/toggles.html'),
            loadFragment('#header-navigation-placeholder', '/fragments/header/navigation.html'),
            loadFragment('#header-ia-chat-placeholder', '/fragments/header/ia-chat.html')
        ]);
        await Promise.all([
            loadFragment('#main-menu-placeholder', '/fragments/menus/main-menu.html'),
            loadFragment('#admin-menu-placeholder', '/fragments/menus/admin-menu.php'),
            loadFragment('#social-menu-placeholder', '/fragments/menus/social-menu.html')
        ]);
        if (window.initLanguageBar) {
            try {
                window.initLanguageBar();
            } catch (e) {
                console.error("Error initializing language bar:", e);
            }
        }

        // Call layout initializers after all fragments are loaded
        if (window.initializeSidebarNavigation) {
            try {
                window.initializeSidebarNavigation();
            } catch (e) {
                console.error("Error initializing sidebar navigation:", e);
            }
        }
        if (window.initializeThemeToggle) { // Called by layout.js too, but good to ensure after toggles.html
            try {
                window.initializeThemeToggle();
            } catch (e) {
                console.error("Error initializing theme toggle:", e);
            }
        }
        if (window.initializeHomonexusToggle) { // Called by layout.js too
            try {
                window.initializeHomonexusToggle();
            } catch (e) {
                console.error("Error initializing homonexus toggle:", e);
            }
        }
        if (window.initializeIAChatSidebar) {
            try {
                window.initializeIAChatSidebar();
            } catch (e) {
                console.error("Error initializing IA chat sidebar:", e);
            }
        }
        if (window.loadIAToolsScript) {
            try {
                window.loadIAToolsScript();
            } catch (e) {
                console.error("Error loading IA tools script:", e);
            }
        }
        alert("header_loader.js init() completed. Test PC.");
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
