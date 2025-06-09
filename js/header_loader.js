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
            loadFragment('#admin-menu-placeholder', '/fragments/menus/admin-menu.html'),
            loadFragment('#social-menu-placeholder', '/fragments/menus/social-menu.html')
        ]);
        if (window.initLanguageBar) {
            window.initLanguageBar();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
