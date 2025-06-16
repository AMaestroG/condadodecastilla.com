document.addEventListener('DOMContentLoaded', () => {
    // --- Menu Toggle Logic ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    if (menuToggle && sidebar && mainContent) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('pushed');
            // Update aria-expanded attribute for accessibility
            const isExpanded = sidebar.classList.contains('open');
            menuToggle.setAttribute('aria-expanded', isExpanded);
        });
    }

    // --- Theme Toggle Logic ---
    const themeToggleButton = document.getElementById('theme-toggle'); // New button in sidebar
    const themeIcon = themeToggleButton ? themeToggleButton.querySelector('img') : null;
    const themeSpan = themeToggleButton ? themeToggleButton.querySelector('span') : null;

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (themeIcon) themeIcon.src = 'assets/icons/sun-icon.svg';
            if (themeSpan) themeSpan.textContent = 'Modo Claro';
            localStorage.setItem('theme', 'dark');
        } else { // Light theme
            document.documentElement.setAttribute('data-theme', 'light');
            if (themeIcon) themeIcon.src = 'assets/icons/moon-icon.svg';
            if (themeSpan) themeSpan.textContent = 'Modo Escuro';
            localStorage.setItem('theme', 'light');
        }
    }

    // Initialize theme based on localStorage or preference
    const currentTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (currentTheme) {
        applyTheme(currentTheme);
    } else if (prefersDark) {
        applyTheme('dark');
    } else {
        applyTheme('light'); // Default to light
    }

    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', () => {
            const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }

    // --- AI Drawer Logic ---
    const aiDrawer = document.getElementById('ai-drawer');
    const aiDrawerToggle = document.getElementById('ai-drawer-toggle'); // New button in sidebar
    const closeAiDrawerButton = document.getElementById('close-ai-drawer');
    const aiSubmit = document.getElementById('ai-submit');
    const aiInput = document.getElementById('ai-input');
    const aiResponse = document.getElementById('ai-response');

    if (aiDrawerToggle && aiDrawer) {
        aiDrawerToggle.addEventListener('click', () => {
            aiDrawer.classList.toggle('active');
            const isExpanded = aiDrawer.classList.contains('active');
            aiDrawerToggle.setAttribute('aria-expanded', isExpanded);
        });
    }

    if (closeAiDrawerButton && aiDrawer) {
        closeAiDrawerButton.addEventListener('click', () => {
            aiDrawer.classList.remove('active');
            if (aiDrawerToggle) aiDrawerToggle.setAttribute('aria-expanded', 'false');
        });
    }

    if (aiSubmit && aiInput && aiResponse) {
        aiSubmit.addEventListener('click', async () => {
            const prompt = aiInput.value.trim();
            if (!prompt) return; // Don't send empty prompts

            const apiKeyMeta = document.querySelector('meta[name="gemini-api-key"]');
            const apiKey = apiKeyMeta ? apiKeyMeta.getAttribute('content') : '';
            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${apiKey}`;

            aiResponse.innerHTML = 'Procesando...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        "contents": [{ "parts": [{ "text": prompt }] }]
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    let errorMessage = `Error: ${response.status} ${response.statusText}`;
                    if (errorData && errorData.error && errorData.error.message) {
                        errorMessage += ` - ${errorData.error.message}`;
                    }
                    throw new Error(errorMessage);
                }

                const data = await response.json();
                if (data.candidates && data.candidates.length > 0 &&
                    data.candidates[0].content && data.candidates[0].content.parts &&
                    data.candidates[0].content.parts.length > 0) {
                    aiResponse.innerHTML = data.candidates[0].content.parts[0].text.replace(/\n/g, '<br>');
                } else if (data.promptFeedback && data.promptFeedback.blockReason) {
                    let feedbackMsg = `Pregunta bloqueada: ${data.promptFeedback.blockReason}`;
                    if (data.promptFeedback.safetyRatings) {
                        data.promptFeedback.safetyRatings.forEach(rating => {
                            if (rating.blocked) {
                                feedbackMsg += `<br>- Categoría: ${rating.category}, Probabilidad: ${rating.probability}`;
                            }
                        });
                    }
                    aiResponse.innerHTML = feedbackMsg;
                } else {
                    // Check for other possible valid responses or more detailed error messages
                    if (data.candidates && data.candidates.length > 0 && data.candidates[0].finishReason === "SAFETY") {
                         aiResponse.innerHTML = 'La respuesta fue bloqueada por motivos de seguridad. Intenta reformular tu pregunta.';
                    } else {
                         aiResponse.innerHTML = 'No se recibió una respuesta válida o el contenido fue bloqueado.';
                    }
                }
            } catch (error) {
                console.error('Error llamando a la API de Gemini:', error);
                aiResponse.innerHTML = `Error al contactar el asistente de IA: ${error.message}`;
            }
            aiInput.value = ''; // Clear input
        });

        // Allow submitting with Enter key in the input field
        aiInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent default form submission if it were in a form
                aiSubmit.click();
            }
        });
    }

    // Initialize AOS (Animate On Scroll) if it's used and loaded
    if (typeof AOS !== 'undefined') {
        AOS.init();
    }
});
// Re-submitting to ensure branch is pushed.
