document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('sidebar-toggle');
    const navMenuContainer = document.getElementById('sidebar'); // This is the <nav> element

    if (navToggle && navMenuContainer) {
        navToggle.addEventListener('click', () => {
            navMenuContainer.classList.toggle('active'); // This class will control visibility of the nav

            // Update aria-expanded attribute for accessibility
            const isExpanded = navMenuContainer.classList.contains('active');
            navToggle.setAttribute('aria-expanded', isExpanded);
        });
    }
});

// Theme Toggle Logic (Enhanced)
document.addEventListener('DOMContentLoaded', () => { // Ensure DOM is loaded for theme toggle too
    const themeToggleButton = document.getElementById('theme-toggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (themeToggleButton) themeToggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Sun icon for dark mode
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            if (themeToggleButton) themeToggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Moon icon for light mode
            localStorage.setItem('theme', 'light');
        }
    }

    const currentTheme = localStorage.getItem('theme');
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
});

// AI Drawer Logic
document.addEventListener('DOMContentLoaded', () => {
    const aiSubmit = document.getElementById('ai-submit');
    const aiInput = document.getElementById('ai-input');
    const aiResponse = document.getElementById('ai-response');
    const aiDrawer = document.getElementById('ai-drawer');
    const closeAiDrawerButton = document.getElementById('close-ai-drawer');
    const iaChatToggle = document.getElementById('ia-chat-toggle');

    if (iaChatToggle && aiDrawer) {
        iaChatToggle.addEventListener('click', () => {
            aiDrawer.classList.toggle('active');
            const isExpanded = aiDrawer.classList.contains('active');
            iaChatToggle.setAttribute('aria-expanded', isExpanded);
        });
    }

    if (closeAiDrawerButton && aiDrawer) {
        closeAiDrawerButton.addEventListener('click', () => {
            aiDrawer.classList.remove('active');
            if (iaChatToggle) iaChatToggle.setAttribute('aria-expanded', 'false');
        });
    }

    if (aiSubmit && aiInput && aiResponse) {
        aiSubmit.addEventListener('click', async () => {
            const prompt = aiInput.value;
            const apiKey = 'A_TUA_CLAVE_DE_API_DE_GEMINI'; // Placeholder - User must replace
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
                    aiResponse.innerHTML = 'No se recibió una respuesta válida o el contenido fue bloqueado sin detalles adicionales.';
                }
            } catch (error) {
                console.error('Error llamando a la API de Gemini:', error);
                aiResponse.innerHTML = `Error al contactar el asistente de IA: ${error.message}`;
            }
            aiInput.value = ''; // Clear input
        });
    }
});
