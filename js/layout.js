document.addEventListener("DOMContentLoaded", function() {
    loadPageCss();
    loadHeaderCss();
    // Always initialize sidebar navigation. For PHP pages, elements are already there.
    // For static HTML pages, this will run, and then the header fetch below will populate the necessary elements.
    // The initializeSidebarNavigation function itself checks for element existence.
    initializeSidebarNavigation();
    loadIAToolsScript(); // Ensure IA tools script loads for pages with static headers
    initializeIAChatSidebar(); // Initialize right sidebar chat
    // IA tools script will also be loaded after the header is inserted dynamically.

    const headerPlaceholder = document.getElementById('header-placeholder');
    if (headerPlaceholder) {
        fetch('/_header.html')
            .then(response => response.text())
            .then(data => {
                headerPlaceholder.innerHTML = data;
                // Re-initialize sidebar navigation if header was loaded dynamically,
                // in case the elements weren't ready on the first call.
                initializeSidebarNavigation();
                loadIAToolsScript();
                // Ensure chat sidebar is initialized when header is loaded
                initializeIAChatSidebar();
            })
            .catch(error => console.error('Error fetching _header.html:', error));
    }

    const footerPlaceholder = document.getElementById('footer-placeholder');
    if (footerPlaceholder) {
        fetch('/_footer.html')
            .then(response => response.text())
            .then(data => {
                footerPlaceholder.innerHTML = data;
            })
            .catch(error => console.error('Error fetching _footer.html:', error));
    }

    // Theme toggle initialization
    initializeThemeToggle();
});

// NEW: Function to handle sidebar interactions
function initializeSidebarNavigation() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('screen-overlay');
    const body = document.body;

    if (sidebarToggle && sidebar && body) { // Added body check
        sidebarToggle.addEventListener('click', () => {
            const visible = sidebar.classList.toggle('sidebar-visible');
            body.classList.toggle('sidebar-active', visible);
            body.classList.toggle('overlay-active', visible || body.classList.contains('ia-chat-active'));
            sidebarToggle.setAttribute('aria-expanded', visible ? 'true' : 'false');
        });

        if (overlay) {
            overlay.addEventListener('click', () => {
                if (sidebar.classList.contains('sidebar-visible')) {
                    sidebar.classList.remove('sidebar-visible');
                    body.classList.remove('sidebar-active');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }
                body.classList.remove('overlay-active');
            });
        }

        const links = sidebar.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768 && sidebar.classList.contains('sidebar-visible')) {
                    sidebar.classList.remove('sidebar-visible');
                    body.classList.remove('sidebar-active');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                    body.classList.remove('overlay-active');
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && sidebar.classList.contains('sidebar-visible')) {
                sidebar.classList.remove('sidebar-visible');
                body.classList.remove('sidebar-active');
                sidebarToggle.setAttribute('aria-expanded', 'false');
                body.classList.remove('overlay-active');
            }
        });
    } else {
        console.error("Sidebar toggle, sidebar element, or body not found.");
    }
}

// Initialize theme toggle button
function initializeThemeToggle() {
    const toggleButton = document.getElementById('theme-toggle');
    const body = document.body;
    if (!toggleButton) return;

    const icon = toggleButton.querySelector('i');
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'dark') {
        body.classList.add('dark-mode');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }

    toggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const isDark = body.classList.contains('dark-mode');
        icon.classList.toggle('fa-moon', !isDark);
        icon.classList.toggle('fa-sun', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
}

function loadIAToolsScript() {
    if (!document.getElementById('ia-tools-script')) {
        const script = document.createElement('script');
        script.id = 'ia-tools-script';
        script.src = '/js/ia-tools.js';
        document.body.appendChild(script);
    }
}

// Initialize IA chat sidebar and messaging
function initializeIAChatSidebar() {
    const toggle = document.getElementById('ia-chat-toggle');
    const sidebar = document.getElementById('ia-chat-sidebar');
    const overlay = document.getElementById('screen-overlay');
    const form = document.getElementById('ia-chat-form');
    const input = document.getElementById('ia-chat-input');
    const messages = document.getElementById('ia-chat-messages');
    const responseBox = document.getElementById('ia-chat-response');
    const CHAT_STORAGE_KEY = 'iaChatHistory';

    function saveChatHistory() {
        if (!messages) return;
        const history = Array.from(messages.children).map(p => ({
            role: p.dataset.role,
            html: p.innerHTML
        }));
        localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(history));
    }

    function loadChatHistory() {
        if (!messages) return;
        const data = localStorage.getItem(CHAT_STORAGE_KEY);
        if (!data) return;
        try {
            const history = JSON.parse(data);
            history.forEach(m => {
                const p = document.createElement('p');
                p.className = `chat-${m.role} chat-message`;
                p.dataset.role = m.role;
                p.contentEditable = 'true';
                p.innerHTML = m.html;
                messages.appendChild(p);
            });
            messages.scrollTop = messages.scrollHeight;
        } catch (e) {
            console.error('Failed to load chat history', e);
        }
    }
    const handle = sidebar ? sidebar.querySelector('.drag-handle') : null;

    loadChatHistory();

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            const visible = sidebar.classList.toggle('sidebar-visible');
            document.body.classList.toggle('ia-chat-active', visible);
            document.body.classList.toggle('overlay-active', visible || document.body.classList.contains('sidebar-active'));
            toggle.setAttribute('aria-expanded', visible ? 'true' : 'false');
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('sidebar-visible')) {
                sidebar.classList.remove('sidebar-visible');
                document.body.classList.remove('ia-chat-active');
                document.body.classList.remove('overlay-active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
        if (overlay) {
            overlay.addEventListener('click', () => {
                if (sidebar.classList.contains('sidebar-visible')) {
                    sidebar.classList.remove('sidebar-visible');
                    document.body.classList.remove('ia-chat-active');
                    toggle.setAttribute('aria-expanded', 'false');
                }
                document.body.classList.remove('overlay-active');
            });
        }
    }

    if (handle && sidebar) {
        let startX, startY, startLeft, startTop;
        handle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            startX = e.clientX;
            startY = e.clientY;
            startLeft = sidebar.offsetLeft;
            startTop = sidebar.offsetTop;
            sidebar.classList.add('dragging');
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });
        function onMouseMove(e) {
            const newLeft = startLeft + (e.clientX - startX);
            const newTop = startTop + (e.clientY - startY);
            sidebar.style.left = `${newLeft}px`;
            sidebar.style.top = `${newTop}px`;
        }
        function onMouseUp() {
            sidebar.classList.remove('dragging');
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }
    }

    if (form && input && messages) {
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = `${input.scrollHeight}px`;
        });
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;
            appendMessage('user', text);
            input.value = '';
            const typingEl = appendMessage('typing', 'Gemini está escribiendo...');
            fetch('/ajax_actions/get_history_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.reply) {
                    typingEl.className = 'chat-ai chat-message';
                    typingEl.innerHTML = data.reply;
                    if (responseBox) {
                        responseBox.innerHTML = data.reply;
                    }
                } else if (data.error) {
                    typingEl.className = 'chat-error chat-message';
                    typingEl.innerHTML = data.error;
                    if (responseBox) {
                        responseBox.innerHTML = data.error;
                    }
                } else {
                    typingEl.className = 'chat-error chat-message';
                    typingEl.innerHTML = 'Error inesperado';
                    if (responseBox) {
                        responseBox.innerHTML = 'Error inesperado';
                    }
                }
                messages.scrollTop = messages.scrollHeight;
            })
            .catch(err => {
                typingEl.className = 'chat-error chat-message';
                typingEl.textContent = err.message;
                if (responseBox) {
                    responseBox.textContent = err.message;
                }
            });
        });
    }

    function appendMessage(role, text) {
        const p = document.createElement('p');
        p.className = `chat-${role} chat-message`;
        p.dataset.role = role;
        p.contentEditable = 'true';
        if (role === 'user') {
            p.textContent = text;
        } else {
            p.innerHTML = text;
        }
        messages.appendChild(p);
        messages.scrollTop = messages.scrollHeight;
        saveChatHistory();
        return p;
    }
}

/*
// OLD function to be removed or commented out:
function initializeMobileNavigation() {
    // Script para el menú de navegación móvil
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');

    // Check if elements exist before adding event listeners
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', () => {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true' || false;
            navToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('active');
        });

        // Opcional: Cerrar menú al hacer clic en un enlace
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                // Check if navLinks is active before trying to remove class and set attribute
                if (navLinks.classList.contains('active')) {
                    navToggle.setAttribute('aria-expanded', 'false');
                    navLinks.classList.remove('active');
                }
            });
        });
    } else {
        console.error("Mobile navigation elements not found. Header might not be loaded correctly.");
    }
}
*/

// --- Section Spotlight Effect ---
document.addEventListener('DOMContentLoaded', () => {
    const sectionsWithSpotlight = document.querySelectorAll('.section.spotlight-active');

    sectionsWithSpotlight.forEach(section => {
        section.style.setProperty('--spotlight-opacity', '1'); // Make spotlight visible

        section.addEventListener('mousemove', (e) => {
            const rect = section.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const size = Math.min(rect.width, rect.height) * 0.5; // Adjust size based on section

            section.style.setProperty('--spotlight-x', `${(x / rect.width) * 100}%`);
            section.style.setProperty('--spotlight-y', `${(y / rect.height) * 100}%`);
            section.style.setProperty('--spotlight-size', `${size}px`);
        });

        section.addEventListener('mouseleave', () => {
            // Optional: Reset to center or fade out
            section.style.setProperty('--spotlight-x', '50%');
            section.style.setProperty('--spotlight-y', '50%');
            // section.style.setProperty('--spotlight-opacity', '0'); // Or fade slowly
        });
    });
});

function loadPageCss() {
    let page = window.location.pathname.split('/').pop();
    if (!page) { page = 'index'; }
    page = page.split('.')[0];
    const cssPath = `/assets/css/pages/${page}.css`;
    fetch(cssPath, { method: 'HEAD' }).then(res => {
        if (res.ok) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssPath;
            document.head.appendChild(link);
        }
    }).catch(() => {});
}

function loadHeaderCss() {
    if (!document.getElementById('header-css')) {
        const link = document.createElement('link');
        link.id = 'header-css';
        link.rel = 'stylesheet';
        link.href = '/assets/css/header.css';
        document.head.appendChild(link);
    }
}
