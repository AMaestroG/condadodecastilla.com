export function initSidebar() {
    function setupMobileAIChatTrigger() {
        if (window.innerWidth <= 768) {
            const originalTrigger = document.getElementById('ai-chat-trigger');
            const placeholderMobile = document.getElementById('ai-chat-trigger-placeholder-mobile');
            if (originalTrigger && placeholderMobile && placeholderMobile.children.length === 0) {
                const clonedTrigger = originalTrigger.cloneNode(true);
                clonedTrigger.id = 'ai-chat-trigger-mobile';
                placeholderMobile.appendChild(clonedTrigger);
            }
        }
    }
    setupMobileAIChatTrigger();

    function populateSidebarContents() {
        const mainMenuPlaceholder = document.getElementById('main-menu-placeholder');
        const adminMenuPlaceholder = document.getElementById('admin-menu-placeholder');
        const socialMenuPlaceholder = document.getElementById('social-menu-placeholder');
        const mainMenuSource = document.getElementById('main-menu');
        const adminMenuSourceContent = document.getElementById('admin-menu-source-content');
        const socialMenuSourceContent = document.getElementById('social-menu-source-content');
        if (mainMenuPlaceholder && mainMenuSource && mainMenuPlaceholder.childElementCount === 0) {
            const clonedMainMenu = mainMenuSource.cloneNode(true);
            mainMenuPlaceholder.appendChild(clonedMainMenu);
        }
        if (adminMenuPlaceholder && adminMenuSourceContent && adminMenuPlaceholder.childElementCount === 0) {
            Array.from(adminMenuSourceContent.children).forEach(child => {
                adminMenuPlaceholder.appendChild(child.cloneNode(true));
            });
        }
        if (socialMenuPlaceholder && socialMenuSourceContent && socialMenuPlaceholder.childElementCount === 0) {
            Array.from(socialMenuSourceContent.children).forEach(child => {
                socialMenuPlaceholder.appendChild(child.cloneNode(true));
            });
        }
    }
    populateSidebarContents();
}
