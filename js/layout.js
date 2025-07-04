document.addEventListener("DOMContentLoaded", function() {
    loadGsap();
    loadAos();
    loadPageCss();
    loadHeaderCss();
    loadFixedTogglesCss();
    loadIAToolsScript();
    // initializeIAChatSidebar();
    prepareInteractiveMap();


    // Other interactive effects
    initializeLinterna();
});

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
        script.onerror = () => {
            const localScript = document.createElement('script');
            localScript.src = '/assets/vendor/js/gsap.min.js';
            document.head.appendChild(localScript);
        };
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
        link.onerror = () => { link.href = '/assets/vendor/css/aos.css'; };
        document.head.appendChild(link);

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js';
        script.integrity = 'sha384-n1AULnKdMJlK1oQCLNDL9qZsDgXtH6jRYFCpBtWFc+a9Yve0KSoMn575rk755NJZ';
        script.crossOrigin = 'anonymous';
        script.onload = () => { if (window.AOS) AOS.init({ once: true }); };
        script.onerror = () => {
            const localScript = document.createElement('script');
            localScript.src = '/assets/vendor/js/aos.js';
            localScript.onload = () => { if (window.AOS) AOS.init({ once: true }); };
            document.head.appendChild(localScript);
        };
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

function loadLeaflet() {
    return new Promise(resolve => {
        if (window.L) { resolve(); return; }
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        css.integrity = 'sha256-sA+e2Fj9uo6pfaSb0OPCfGze5krs+Bx0Yo1ZPKKXFxA=';
        css.crossOrigin = '';
        document.head.appendChild(css);
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.integrity = 'sha256-o9N1j7kG3Y1mCqo4HCpO2m6EaWEzhbso5Lr2Rsu7m9I=';
        script.crossOrigin = '';
        script.onload = () => resolve();
        script.onerror = () => resolve();
        document.head.appendChild(script);
    });
}

function initializeInteractiveMap() {
    if (!window.L || !window.alfozPlaces) return;
    const mapEl = document.getElementById('interactive-map');
    if (!mapEl) return;
    const purple = getComputedStyle(document.documentElement).getPropertyValue('--color-primario-purpura').trim() || '#4A0D67';
    const gold = getComputedStyle(document.documentElement).getPropertyValue('--color-secundario-dorado').trim() || '#B8860B';
    const map = L.map(mapEl).setView([42.55, -3.3], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    window.alfozPlaces.forEach((place, idx) => {
        const lat = 42.55 + 0.1 * Math.sin(idx / 5);
        const lng = -3.3 + 0.1 * Math.cos(idx / 5);
        L.circleMarker([lat, lng], {
            color: purple,
            fillColor: gold,
            fillOpacity: 0.9,
            radius: 6
        }).addTo(map).bindPopup(`<a href="${place.path}">${place.name}</a><br>${place.description}`);
    });
}

function prepareInteractiveMap() {
    if (!document.getElementById('interactive-map')) return;
    loadLeaflet().then(() => {
        const waitForData = () => {
            if (window.alfozPlaces && window.alfozPlaces.length) {
                initializeInteractiveMap();
            } else {
                setTimeout(waitForData, 100);
            }
        };
        waitForData();
    });
}
