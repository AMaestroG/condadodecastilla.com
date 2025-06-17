// assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
    // Main Consolidated Menu (Right Panel)
    const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
    const consolidatedMenuItems = document.getElementById('consolidated-menu-items');

    if (consolidatedMenuButton && consolidatedMenuItems) {
        consolidatedMenuButton.addEventListener('click', () => {
            consolidatedMenuItems.classList.toggle('active');
            const isExpanded = consolidatedMenuItems.classList.contains('active');
            consolidatedMenuButton.setAttribute('aria-expanded', isExpanded.toString());
            // Optional: If AI panel should close when main menu closes, add logic here
            // if (!isExpanded && aiChatPanel && aiChatPanel.classList.contains('active')) {
            //    aiChatPanel.classList.remove('active');
            //    // Also update ai-chat-trigger's aria-expanded if it exists
            // }
        });
    } else {
        console.error('Consolidated menu button or items panel not found.');
    }

    // AI Chat Panel (Left Panel) - Triggered from within the consolidated menu
    const aiChatTriggerButton = document.getElementById('ai-chat-trigger');
    const aiChatPanel = document.getElementById('ai-chat-panel');

    if (aiChatTriggerButton && aiChatPanel) {
        aiChatTriggerButton.addEventListener('click', () => {
            aiChatPanel.classList.toggle('active');
            const isAIExpanded = aiChatPanel.classList.contains('active');
            aiChatTriggerButton.setAttribute('aria-expanded', isAIExpanded.toString());

            // Optional: Close main consolidated menu when AI chat opens
            // if (isAIExpanded && consolidatedMenuItems && consolidatedMenuItems.classList.contains('active')) {
            //     consolidatedMenuItems.classList.remove('active');
            //     consolidatedMenuButton.setAttribute('aria-expanded', 'false');
            // }
        });
    } else {
        console.error('AI chat trigger button or AI chat panel not found.');
    }

    // AI Chat Panel - Internal Close Button (from ai-drawer.html)
    const closeAiDrawerButton = document.getElementById('close-ai-drawer'); // ID is from ai-drawer.html
    if (closeAiDrawerButton && aiChatPanel) {
        closeAiDrawerButton.addEventListener('click', () => {
            aiChatPanel.classList.remove('active');
            if (aiChatTriggerButton) {
                aiChatTriggerButton.setAttribute('aria-expanded', 'false');
            }
        });
    } else {
        // Note: #close-ai-drawer is inside #ai-chat-panel, so it might not be found if panel is empty
        // console.warn('AI drawer close button not found. This is normal if AI chat panel content is not loaded.');
    }

    // Theme Toggle Button - Functionality is in js/layout.js (initializeThemeToggle)
    // No new JS needed here for theme toggle, just ensure the button #theme-toggle exists in HTML.
    const themeToggleButton = document.getElementById('theme-toggle');
    if (!themeToggleButton) {
        console.error('Theme toggle button not found.');
    }

    // Optional: Close menus when clicking outside
    document.addEventListener('click', (event) => {
        // Close consolidated menu if click is outside
        if (consolidatedMenuItems && consolidatedMenuItems.classList.contains('active') &&
            !consolidatedMenuItems.contains(event.target) && !consolidatedMenuButton.contains(event.target)) {
            consolidatedMenuItems.classList.remove('active');
            consolidatedMenuButton.setAttribute('aria-expanded', 'false');
        }

        // Close AI chat panel if click is outside
        // (and not on its trigger, which would toggle it back open immediately)
        if (aiChatPanel && aiChatPanel.classList.contains('active') &&
            !aiChatPanel.contains(event.target) &&
            aiChatTriggerButton && !aiChatTriggerButton.contains(event.target)) {
            aiChatPanel.classList.remove('active');
            aiChatTriggerButton.setAttribute('aria-expanded', 'false');
        }
    });

    // Optional: Close menus with Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (consolidatedMenuItems && consolidatedMenuItems.classList.contains('active')) {
                consolidatedMenuItems.classList.remove('active');
                consolidatedMenuButton.setAttribute('aria-expanded', 'false');
            }
            if (aiChatPanel && aiChatPanel.classList.contains('active')) {
                aiChatPanel.classList.remove('active');
                if (aiChatTriggerButton) {
                    aiChatTriggerButton.setAttribute('aria-expanded', 'false');
                }
            }
        }
    });
});
