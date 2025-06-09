(function() {
    const loadFragment = (selector, url) => {
        const container = document.querySelector(selector);
        if (!container) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => { container.innerHTML = html; })
            .catch(err => console.error('Error loading menu fragment', url, err));
    };

    loadFragment('#main-menu-placeholder', '/fragments/menus/main-menu.html');
    loadFragment('#admin-menu-placeholder', '/fragments/menus/admin-menu.php');
    loadFragment('#social-menu-placeholder', '/fragments/menus/social-menu.html');
})();
