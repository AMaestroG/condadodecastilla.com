document.addEventListener('DOMContentLoaded', function() {
    // Targets submenus specifically within the unified panel's main navigation area
    const unifiedPanelMainNav = document.getElementById('unified-main-nav');
    if (!unifiedPanelMainNav) {
        // console.warn('Unified panel navigation container (#unified-main-nav) not found. Submenu script will not run.');
        return;
    }

    // It's better to query for toggles within the specific main menu UL if possible, e.g., #main-menu
    const mainMenu = unifiedPanelMainNav.querySelector('#main-menu');
    if (!mainMenu) {
        // console.warn('Main menu UL (#main-menu) not found within #unified-main-nav. Submenu script might not function correctly.');
        return;
    }

    const submenuToggles = mainMenu.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const submenuId = this.getAttribute('aria-controls');
            const submenu = document.getElementById(submenuId); // Submenu is UL
            const arrow = this.querySelector('.submenu-arrow'); // Arrow is SVG inside button

            if (submenu) {
                const isExpanded = submenu.style.display === 'block';
                submenu.style.display = isExpanded ? 'none' : 'block';
                this.setAttribute('aria-expanded', String(!isExpanded));
                submenu.setAttribute('aria-hidden', String(isExpanded));
                if (arrow) {
                    arrow.classList.toggle('rotate-180', !isExpanded);
                }
            }
        });
    });

    // Auto-expand submenu if a child link is the current page
    const activeSubmenuLink = mainMenu.querySelector('ul.submenu li a[aria-current="page"]');
    if (activeSubmenuLink) {
        const parentSubmenuUl = activeSubmenuLink.closest('ul.submenu');
        if (parentSubmenuUl) {
            parentSubmenuUl.style.display = 'block';
            parentSubmenuUl.setAttribute('aria-hidden', 'false');

            const toggleButton = mainMenu.querySelector('button.submenu-toggle[aria-controls="' + parentSubmenuUl.id + '"]');
            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', 'true');
                const arrow = toggleButton.querySelector('.submenu-arrow');
                if (arrow) {
                    arrow.classList.add('rotate-180');
                }
            }
        }
    }
});
