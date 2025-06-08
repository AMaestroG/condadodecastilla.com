document.addEventListener('DOMContentLoaded', () => {
    const loadFragment = (selector, url, cssUrl) => {
        const container = document.querySelector(selector);
        if (!container) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(err => console.error('Error loading fragment', url, err));

        if (cssUrl && !document.querySelector(`link[href="${cssUrl}"]`)) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssUrl;
            document.head.appendChild(link);
        }
    };

    loadFragment('#header-language-bar-placeholder', '/fragments/header/language-bar/index.html', '/fragments/header/language-bar/style.css');
    loadFragment('#header-toggles-placeholder', '/fragments/header/toggles/index.html', '/fragments/header/toggles/style.css');
    loadFragment('#header-navigation-placeholder', '/fragments/header/navigation/index.html', '/fragments/header/navigation/style.css');
    loadFragment('#header-ia-chat-placeholder', '/fragments/header/ia-chat/index.html', '/fragments/header/ia-chat/style.css');
});
