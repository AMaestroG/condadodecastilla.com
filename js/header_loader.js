(
    function(){
        function loadFragment(selector, url) {
            const container = document.querySelector(selector);
            if (!container) return Promise.resolve();
            return fetch(url)
                .then(r => r.text())
                .then(html => { container.innerHTML = html; })
                .catch(err => console.error('Error loading fragment', url, err));
        }

        function ensureAnime() {
            return new Promise((resolve, reject) => {
                if (window.anime) return resolve();
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        function fetchMenu(type) {
            return fetch('/api_menus.php?type=' + type).then(r => r.json());
        }

        function buildMenu(items) {
            const ul = document.createElement('ul');
            ul.className = 'nav-links';
            items.forEach(item => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.textContent = item.label;
                a.href = item.url;
                if (item.icon) {
                    const icon = document.createElement('i');
                    icon.className = item.icon;
                    a.prepend(icon);
                    a.insertAdjacentText('beforeend', '');
                }
                li.appendChild(a);
                if (Array.isArray(item.children) && item.children.length) {
                    li.appendChild(buildMenu(item.children));
                }
                ul.appendChild(li);
            });
            return ul;
        }

        function loadMenus() {
            const types = ['main','admin','social'];
            const promises = types.map(type => {
                return fetchMenu(type).then(items => {
                    const container = document.querySelector(`#${type}-menu-placeholder`);
                    if (container) {
                        container.innerHTML = '';
                        container.appendChild(buildMenu(items));
                    }
                });
            });
            return Promise.all(promises).then(() => {
                if (window.anime) {
                    window.anime({
                        targets: '#sidebar li',
                        opacity: [0,1],
                        translateX: [-20,0],
                        delay: window.anime.stagger(50)
                    });
                }
            });
        }

        function init() {
            Promise.all([
                loadFragment('#header-language-bar-placeholder', '/fragments/header/language-bar.html'),
                loadFragment('#header-toggles-placeholder', '/fragments/header/toggles.html'),
                loadFragment('#header-navigation-placeholder', '/fragments/header/navigation.html'),
                loadFragment('#header-ia-chat-placeholder', '/fragments/header/ia-chat.html')
            ]).then(() => {
                return ensureAnime().then(loadMenus);
            }).then(() => {
                if (window.initLanguageBar) {
                    window.initLanguageBar();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }
)();
