// assets/js/main.js
// alert('main.js loaded'); // Alerta 1: Script cargado

document.addEventListener('DOMContentLoaded', () => {
    // alert('DOM fully loaded and parsed'); // Alerta 2: DOM listo

    // Main Consolidated Menu (Right Panel)
    const consolidatedMenuButton = document.getElementById('consolidated-menu-button');
    const consolidatedMenuItems = document.getElementById('consolidated-menu-items');

    if (!consolidatedMenuButton) {
        // alert('Error: consolidatedMenuButton not found!'); // Alerta 3a
    }
    if (!consolidatedMenuItems) {
        // alert('Error: consolidatedMenuItems not found!'); // Alerta 3b
    }

    if (consolidatedMenuButton && consolidatedMenuItems) {
        // alert('Menu button and items panel FOUND.'); // Alerta 4: Elementos encontrados
        consolidatedMenuButton.addEventListener('click', () => {
            // alert('Consolidated menu button CLICKED!'); // Alerta 5: Clic en el botón
            consolidatedMenuItems.classList.toggle('active');
            const isExpanded = consolidatedMenuItems.classList.contains('active');
            consolidatedMenuButton.setAttribute('aria-expanded', isExpanded.toString());
            // alert('Active class toggled. Panel should be ' + (isExpanded ? 'visible' : 'hidden')); // Alerta 6
        });
    } else {
        console.error('Consolidated menu button or items panel not found (logged to console).');
        // No se añade alerta aquí porque ya se alertó arriba si alguno es nulo.
    }

    // AI Chat Panel (Left Panel) - Triggered from within the consolidated menu
    const aiChatTriggerButton = document.getElementById('ai-chat-trigger');
    const aiChatPanel = document.getElementById('ai-chat-panel');

    if (!aiChatTriggerButton) {
        // alert('Debug: aiChatTriggerButton not found!'); // Comentado para no abrumar, pero útil si hay problemas con AI chat
    }
    if (!aiChatPanel) {
        // alert('Debug: aiChatPanel not found!');
    }

    if (aiChatTriggerButton && aiChatPanel) {
        aiChatTriggerButton.addEventListener('click', () => {
            // alert('AI Chat trigger button CLICKED!'); // Alerta para el trigger del chat IA
            aiChatPanel.classList.toggle('active');
            const isAIExpanded = aiChatPanel.classList.contains('active');
            aiChatTriggerButton.setAttribute('aria-expanded', isAIExpanded.toString());
        });
    }

    // AI Chat Panel - Internal Close Button (from ai-drawer.html)
    const closeAiDrawerButton = document.getElementById('close-ai-drawer');
    if (closeAiDrawerButton && aiChatPanel) {
        closeAiDrawerButton.addEventListener('click', () => {
            // alert('AI Chat CLOSE button CLICKED!'); // Alerta para el botón de cerrar chat IA
            aiChatPanel.classList.remove('active');
            if (aiChatTriggerButton) {
                aiChatTriggerButton.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Theme Toggle Button
    const themeToggleButton = document.getElementById('theme-toggle');
    if (!themeToggleButton) {
        // alert('Debug: Theme toggle button not found.');
    }

    // Optional: Close menus when clicking outside
    document.addEventListener('click', (event) => {
        if (consolidatedMenuItems && consolidatedMenuItems.classList.contains('active') &&
            !consolidatedMenuItems.contains(event.target) && consolidatedMenuButton && !consolidatedMenuButton.contains(event.target)) {
            // alert('Clicked outside main menu.'); // Puede ser muy ruidoso
            consolidatedMenuItems.classList.remove('active');
            consolidatedMenuButton.setAttribute('aria-expanded', 'false');
        }
        if (aiChatPanel && aiChatPanel.classList.contains('active') &&
            !aiChatPanel.contains(event.target) &&
            aiChatTriggerButton && !aiChatTriggerButton.contains(event.target)) {
            // alert('Clicked outside AI chat panel.'); // Puede ser muy ruidoso
            aiChatPanel.classList.remove('active');
            if (aiChatTriggerButton) {
                aiChatTriggerButton.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Optional: Close menus with Escape key
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            // alert('Escape key pressed.'); // Puede ser ruidoso
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
    // alert('main.js event listeners attached.'); // Alerta 7: Fin de la configuración
});
