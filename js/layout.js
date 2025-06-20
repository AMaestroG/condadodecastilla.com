document.addEventListener("DOMContentLoaded", function() {
    loadGsap();
    loadAos();
    loadPageCss();
    loadHeaderCss();
    loadFixedTogglesCss();
    loadIAToolsScript();
    // initializeSidebarNavigation();
    // initializeIAChatSidebar();


    // Other interactive effects
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

    const appendCss = () => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = cssPath;
        document.head.appendChild(link);
    };

    if (window.fetch) {
        fetch(cssPath, { method: 'HEAD' }).then(res => {
            if (res.ok) appendCss();
        }).catch(() => {});
    } else if (XMLHttpRequest) {
        const xhr = new XMLHttpRequest();
        xhr.open('HEAD', cssPath, true);
        xhr.onreadystatechange = function(){
            if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 400) {
                appendCss();
            }
        };
        try { xhr.send(); } catch(e) {}
    }
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
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js';
        script.integrity = 'sha384-HOvlOYPIs/zjoIkWUGXkVmXsjr8GuZLV+Q+rcPwmJOVZVpvTSXQChiN4t9Euv9Vc';
        script.crossOrigin = 'anonymous';
        document.head.appendChild(script);
    }
}

function loadAos() {
    if (!window.AOS) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css';
        link.integrity = 'sha384-/rJKQnzOkEo+daG0jMjU1IwwY9unxt1NBw3Ef2fmOJ3PW/TfAg2KXVoWwMZQZtw9';
        link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js';
        script.integrity = 'sha384-n1AULnKdMJlK1oQCLNDL9qZsDgXtH6jRYFCpBtWFc+a9Yve0KSoMn575rk755NJZ';
        script.crossOrigin = 'anonymous';
        script.onload = () => { if (window.AOS) AOS.init({ once: true }); };
        document.head.appendChild(script);
    } else {
        AOS.init({ once: true });
    }
}


function loadIAToolsScript() {
    if (!document.querySelector('script[src="/js/ia-tools.js"]')) {
        const script = document.createElement('script');
        script.src = '/js/ia-tools.js';
        document.head.appendChild(script);
    }
}
