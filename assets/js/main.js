document.addEventListener('DOMContentLoaded', () => {
    // Consolidated Menu Toggle
    const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
    const consolidatedMenuItems = document.getElementById('consolidated-menu-items');

    if (consolidatedMenuButton && consolidatedMenuItems) {
        consolidatedMenuButton.addEventListener('click', () => {
            const isExpanded = consolidatedMenuButton.getAttribute('aria-expanded') === 'true';
            if (consolidatedMenuItems.style.display === 'block') {
                consolidatedMenuItems.style.display = 'none';
                consolidatedMenuButton.setAttribute('aria-expanded', 'false');
            } else {
                consolidatedMenuItems.style.display = 'block'; // Or 'flex' if that's better for layout
                consolidatedMenuButton.setAttribute('aria-expanded', 'true');
            }
        });
    } else {
        console.error('Consolidated menu button or items not found.');
    }

    // AI Drawer Toggle
    const aiDrawerToggle = document.getElementById('ai-drawer-toggle');
    const aiDrawer = document.getElementById('ai-drawer'); // Assuming this is the ID of the main AI drawer container

    if (aiDrawerToggle && aiDrawer) {
        aiDrawerToggle.addEventListener('click', () => {
            // Toggle a class that controls visibility (e.g., 'ai-drawer-open')
            // The specific class and its CSS definition will be handled in the CSS step.
            // For now, let's use a simple display toggle, assuming CSS will refine this.
            if (aiDrawer.style.display === 'flex' || aiDrawer.classList.contains('ai-drawer-open')) { // Check for class if already styled that way
                aiDrawer.style.display = 'none';
                aiDrawer.classList.remove('ai-drawer-open');
                // Consider updating aria-expanded if the button controls the drawer directly
            } else {
                aiDrawer.style.display = 'flex'; // Or 'block', depending on drawer's CSS
                aiDrawer.classList.add('ai-drawer-open');
            }
        });
    } else {
        console.error('AI drawer toggle button or AI drawer element not found.');
    }

    // Existing close button for AI drawer (from fragments/header/ai-drawer.html)
    const closeAiDrawerButton = document.getElementById('close-ai-drawer');
    if (closeAiDrawerButton && aiDrawer) {
        closeAiDrawerButton.addEventListener('click', () => {
            aiDrawer.style.display = 'none';
            aiDrawer.classList.remove('ai-drawer-open');
        });
    }

    // Ensure theme toggle is initialized (it's in layout.js but good to ensure it's called after DOM is ready)
    // Check if initializeThemeToggle is available globally, otherwise this might need to be in layout.js
    if (typeof initializeThemeToggle === 'function') {
        // initializeThemeToggle(); // This is already called in layout.js
    }
});
