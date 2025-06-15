document.addEventListener("DOMContentLoaded", function() {
    loadGsap();
    loadAos();
    loadPageCss();
    loadHeaderCss();
    loadFixedTogglesCss();
    // initializeSidebarNavigation(); // Now called by header_loader.js
    // loadIAToolsScript(); // Now called by header_loader.js
    // initializeIAChatSidebar(); // Now called by header_loader.js

    let headerPlaceholder = document.getElementById('header-placeholder');
    if (!headerPlaceholder) {
        headerPlaceholder = document.createElement('div');
        headerPlaceholder.id = 'header-placeholder';
        document.body.insertBefore(headerPlaceholder, document.body.firstChild);
    }
    fetch('/_header.html')
        .then(response => response.text())
        .then(data => {
            insertHtmlWithScripts(headerPlaceholder, data);
        })
        .catch(error => console.error('Error fetching _header.html:', error));

    let footerPlaceholder = document.getElementById('footer-placeholder');
    if (!footerPlaceholder) {
        footerPlaceholder = document.createElement('div');
        footerPlaceholder.id = 'footer-placeholder';
        document.body.appendChild(footerPlaceholder);
    }
    fetch('/_footer.php')
        .then(response => response.text())
        .then(data => {
            footerPlaceholder.innerHTML = data;
        })
        .catch(error => console.error('Error fetching _footer.php:', error));

    // Theme toggle initialization
    initializeThemeToggle();
    initializeLinterna();
});

// NEW: Function to handle sidebar interactions
function initializeSidebarNavigation() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const body = document.body;

    if (sidebarToggle && sidebar && body) { // Added body check
        sidebarToggle.addEventListener('click', () => {
            // alert("Sidebar toggle clicked! Test Non-Firefox PC."); // Temporary debug
            const opening = !sidebar.classList.contains('sidebar-visible');
            sidebar.classList.toggle('sidebar-visible');
            body.classList.toggle('sidebar-active'); // For main content shift
            // const iaToggle = document.getElementById('ia-chat-toggle');
            // if (iaToggle) iaToggle.click(); // Prevent AI chat from opening with main sidebar
            if (window.gsap) {
                if (opening) {
                    gsap.fromTo(sidebar, { x: '-100%' }, { x: '0%', duration: 0.35, ease: 'power2.out', clearProps: 'transform' });
                } else {
                    gsap.to(sidebar, { x: '-100%', duration: 0.35, ease: 'power2.in', onComplete: () => { sidebar.style.transform = ''; } });
                }
            }
            // Optional: Change toggle button text/icon and ARIA attribute
            if (sidebar.classList.contains('sidebar-visible')) {
                sidebarToggle.setAttribute('aria-expanded', 'true');
                // sidebarToggle.textContent = '✕'; // Example: Change to X
            } else {
                sidebarToggle.setAttribute('aria-expanded', 'false');
                // sidebarToggle.textContent = '☰'; // Example: Change back to burger
            }
        });

        const links = sidebar.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768 && sidebar.classList.contains('sidebar-visible')) {
                    sidebar.classList.remove('sidebar-visible');
                    body.classList.remove('sidebar-active');
                    if (window.gsap) {
                        gsap.to(sidebar, { x: '-100%', duration: 0.35, ease: 'power2.in', onComplete: () => { sidebar.style.transform = ''; } });
                    }
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }
            });
            link.addEventListener('mouseenter', () => {
                if (window.gsap) {
                    gsap.to(link, { paddingLeft: '20px', duration: 0.2, ease: 'power2.out' });
                }
            });
            link.addEventListener('mouseleave', () => {
                if (window.gsap) {
                    gsap.to(link, { paddingLeft: '12px', duration: 0.2, ease: 'power2.in' });
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && sidebar.classList.contains('sidebar-visible')) {
                sidebar.classList.remove('sidebar-visible');
                body.classList.remove('sidebar-active');
                if (window.gsap) {
                    gsap.to(sidebar, { x: '-100%', duration: 0.35, ease: 'power2.in', onComplete: () => { sidebar.style.transform = ''; } });
                }
                sidebarToggle.setAttribute('aria-expanded', 'false');
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

function initializeLinterna() {
    const linterna = document.getElementById('linterna-condado');
    if (!linterna) return;
    const sections = document.querySelectorAll('.section');
    function update(e) {
        requestAnimationFrame(() => {
            linterna.style.setProperty('--mouse-x', e.clientX + 'px');
            linterna.style.setProperty('--mouse-y', e.clientY + 'px');
        });
    }
    function activate() {
        requestAnimationFrame(() => {
            linterna.style.setProperty('--linterna-opacity', '0.65');
            linterna.style.setProperty('--linterna-radio', '250px');
        });
    }
    function deactivate() {
        requestAnimationFrame(() => {
            linterna.style.setProperty('--linterna-opacity', '0');
        });
    }
    document.body.addEventListener('mousemove', update);
    sections.forEach(s => {
        s.addEventListener('mouseenter', activate);
        s.addEventListener('mouseleave', deactivate);
    });
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
    if (!document.getElementById('lighting-css')) {
        const link = document.createElement('link');
        link.id = 'lighting-css';
        link.rel = 'stylesheet';
        link.href = '/assets/css/lighting.css';
        document.head.appendChild(link);
    }
}

function loadFixedTogglesCss() {
    if (!document.getElementById('fixed-toggles-css')) {
        const link = document.createElement('link');
        link.id = 'fixed-toggles-css';
        link.rel = 'stylesheet';
        link.href = '/assets/css/header/fixed-toggles.css';
        document.head.appendChild(link);
    }
}

function loadGsap() {
    if (!window.gsap) {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js';
        document.head.appendChild(script);
    }
}

function loadAos() {
    if (!window.AOS) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css';
        document.head.appendChild(link);
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js';
        script.onload = () => { if (window.AOS) AOS.init({ once: true }); };
        document.head.appendChild(script);
    } else {
        AOS.init({ once: true });
    }
}

function insertHtmlWithScripts(container, html) {
    container.innerHTML = html;
    const scripts = container.querySelectorAll('script');
    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });
        newScript.textContent = oldScript.textContent;
        document.head.appendChild(newScript);
        oldScript.remove();
    });
}
