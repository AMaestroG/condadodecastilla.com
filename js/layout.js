document.addEventListener('DOMContentLoaded', function() {
    // Always initialize sidebar navigation. For PHP pages, elements are already there.
    // For static HTML pages, this will run, and then the header fetch below will populate the necessary elements.
    // The initializeSidebarNavigation function itself checks for element existence.
    initializeSidebarNavigation();
    loadIAToolsScript();

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
    const body = document.body;

    if (sidebarToggle && sidebar && body) { // Added body check
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-visible');
            body.classList.toggle('sidebar-active'); // For main content shift
            // Optional: Change toggle button text/icon and ARIA attribute
            if (sidebar.classList.contains('sidebar-visible')) {
                sidebarToggle.setAttribute('aria-expanded', 'true');
                // sidebarToggle.textContent = '✕'; // Example: Change to X
            } else {
                sidebarToggle.setAttribute('aria-expanded', 'false');
                // sidebarToggle.textContent = '☰'; // Example: Change back to burger
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
