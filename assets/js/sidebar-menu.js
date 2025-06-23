document.addEventListener('DOMContentLoaded', function() {
    // Ensure this script targets submenus specifically within the main sidebar's navigation area
    const sidebarNav = document.getElementById('sidebar-nav'); // Or #main-menu if that's the direct UL parent for all items
    if (!sidebarNav) {
        // console.warn('Sidebar navigation container (#sidebar-nav or #main-menu) not found. Submenu script will not run.');
        return;
    }

    const submenuToggles = sidebarNav.querySelectorAll('.submenu-toggle');

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

                // Optional: Add/Remove a class to the parent LI or the button itself if needed for styling open state
                // this.closest('li.has-submenu')?.classList.toggle('open', !isExpanded);
            }
        });
    });

    // Auto-expand submenu if a child link is the current page
    // This targets links within a submenu UL that have aria-current="page"
    const activeSubmenuLink = sidebarNav.querySelector('ul.submenu li a[aria-current="page"]');
    if (activeSubmenuLink) {
        const parentSubmenuUl = activeSubmenuLink.closest('ul.submenu');
        if (parentSubmenuUl) {
            parentSubmenuUl.style.display = 'block';
            parentSubmenuUl.setAttribute('aria-hidden', 'false');

            // Find the corresponding toggle button for this submenu UL
            const toggleButton = sidebarNav.querySelector('button.submenu-toggle[aria-controls="' + parentSubmenuUl.id + '"]');
            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', 'true');
                const arrow = toggleButton.querySelector('.submenu-arrow');
                if (arrow) {
                    arrow.classList.add('rotate-180');
                }
                // Optional: Add 'open' class to parent LI or button if using it for styling
                // toggleButton.closest('li.has-submenu')?.classList.add('open');

                // Also, mark the parent button as 'active-parent' for styling, if such a class is used in CSS.
                // Note: The `activeClass` in `menu.php` is for the button if its OWN URL matches.
                // This is for when a CHILD is active.
                // toggleButton.classList.add('active-parent'); // Example, if you have CSS for .active-parent
            }
        }
    }
});
