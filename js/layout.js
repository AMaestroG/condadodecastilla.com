document.addEventListener('DOMContentLoaded', function() {
    fetch('/_header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-placeholder').innerHTML = data;
            // Initialize mobile navigation after header is loaded
            initializeMobileNavigation();
        });

    fetch('/_footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-placeholder').innerHTML = data;
        });
});

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
