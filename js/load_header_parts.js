document.addEventListener('DOMContentLoaded', () => {
    const loadFragment = (selector, url) => {
        const container = document.querySelector(selector);
        if (!container) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(err => console.error('Error loading fragment', url, err));
    };

    loadFragment('#header-language-bar-placeholder', '/fragments/header/language-bar.html');
    loadFragment('#header-toggles-placeholder', '/fragments/header/toggles.html');
    loadFragment('#header-navigation-placeholder', '/fragments/header/navigation.html');
    loadFragment('#header-ia-chat-placeholder', '/fragments/header/ia-chat.html');
});
