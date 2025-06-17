// js/ia-tools.js
// Handles IA tool actions: summary, translation, research and web search.

document.addEventListener('DOMContentLoaded', () => {
    const summaryBtn = document.getElementById('ia-summary-btn');
    const researchBtn = document.getElementById('ia-research-btn');
    const websearchBtn = document.getElementById('ia-websearch-btn');
    const translateBtn = document.getElementById('ia-translate-btn');

    const output = ensureOutputContainer();

    if (summaryBtn) {
        summaryBtn.addEventListener('click', () => handleSummary(output));
    }

    if (translateBtn) {
        translateBtn.addEventListener('click', () => handleTranslation(output));
    }

    if (researchBtn) {
        researchBtn.addEventListener('click', () => handleResearch(output));
    }

    if (websearchBtn) {
        websearchBtn.addEventListener('click', () => handleWebSearch(output));
    }

    // Gemini Chat Functionality
    const chatInput = document.getElementById('gemini-chat-input');
    const chatSubmitBtn = document.getElementById('gemini-chat-submit');
    const chatArea = document.getElementById('gemini-chat-area');

    if (chatSubmitBtn && chatInput && chatArea) {
        chatSubmitBtn.addEventListener('click', () => handleChatMessage(chatInput, chatArea));
        chatInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                handleChatMessage(chatInput, chatArea);
            }
        });
    }
});

function ensureOutputContainer() {
    let cont = document.getElementById('ia-tools-response');
    if (!cont) {
        cont = document.createElement('div');
        cont.id = 'ia-tools-response';
        cont.className = 'ia-tools-response hidden';
        // Use the new panel ID for AI tools output
        const sidebar = document.getElementById('gemini-ai-panel');
        if (sidebar) {
            // Insert before ia-tools-menu if it exists, otherwise append to sidebar
            const toolsMenu = sidebar.querySelector('#ia-tools-menu');
            if (toolsMenu) {
                sidebar.insertBefore(cont, toolsMenu);
            } else {
                sidebar.appendChild(cont);
            }
        } else {
            document.body.appendChild(cont);
        }
    }
    return cont;
}

function showOutput(container, html) {
    container.innerHTML = html;
    container.classList.remove('hidden');
}

function getMainText() {
    const main = document.querySelector('main');
    return main ? main.textContent.trim() : document.body.textContent.trim();
}

function handleSummary(output) {
    const text = getMainText();
    if (!text) {
        showOutput(output, '<p class="ia-tool-error">No se encontró texto para resumir.</p>');
        return;
    }
    showOutput(output, '<p><em>Generando resumen...</em></p>');
    fetch('/ajax_actions/get_summary.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ text_to_summarize: text })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.summary) {
            showOutput(output, data.summary);
        } else if (data.error) {
            showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
        } else {
            showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
    });
}

function handleTranslation(output) {
    const text = getMainText();
    const target = prompt('Código de idioma destino (ej. en-ai, fr-ai, de-ai):', 'en-ai');
    if (!target) return;
    showOutput(output, '<p><em>Generando traducción...</em></p>');
    fetch('/ajax_actions/get_translation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ text_to_translate: text, target_lang: target })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.translation) {
            showOutput(output, data.translation);
        } else if (data.error) {
            showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
        } else {
            showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
    });
}

function handleResearch(output) {
    const query = prompt('Tema o pregunta a investigar:');
    if (!query) return;
    showOutput(output, '<p><em>Buscando información...</em></p>');
    fetch('/ajax_actions/get_research.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ query })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.research) {
            showOutput(output, data.research);
        } else if (data.error) {
            showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
        } else {
            showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
    });
}

function handleWebSearch(output) {
    const query = prompt('Búsqueda en la web:');
    if (!query) return;
    showOutput(output, '<p><em>Generando búsqueda...</em></p>');
    fetch('/ajax_actions/get_web_search.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ query })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.results) {
            showOutput(output, data.results);
        } else if (data.error) {
            showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
        } else {
            showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
    });

// Simple demo request to the Flask API
function demoFlaskRequest() {
    fetch('http://localhost:5000/api/resource')
        .then(res => res.json())
        .then(data => console.log('API resources', data))
        .catch(err => console.error('API error', err));
}

// --- Gemini Chat Helper Functions ---

/**
 * Appends a message to the chat area.
 * @param {HTMLElement} chatArea The chat area element.
 * @param {string} text The message text.
 * @param {string} sender 'user', 'ai', 'ai-status', or 'ai-error'.
 */
function appendMessage(chatArea, text, sender) {
    if (!chatArea) return;
    const messageDiv = document.createElement('div');
    let messageClass = 'chat-message';
    let displayText = text;

    // Basic text sanitization
    const sanitizedText = text.replace(/</g, "&lt;").replace(/>/g, "&gt;");

    switch (sender) {
        case 'user':
            messageClass += ' user-message';
            displayText = `<strong>Tú:</strong> ${sanitizedText}`;
            break;
        case 'ai':
            messageClass += ' ai-message';
            displayText = `<strong>IA:</strong> ${sanitizedText}`;
            break;
        case 'ai-status':
            messageClass += ' ai-status-message'; // For styling 'Pensando...'
            // displayText is already '<em>Pensando...</em>', allow HTML for emphasis
            break;
        case 'ai-error':
            messageClass += ' ai-error-message'; // For styling error messages
            displayText = `<strong>IA Error:</strong> ${sanitizedText}`;
            break;
        default:
            messageClass += ' system-message';
            displayText = sanitizedText;
    }

    messageDiv.className = messageClass;
    messageDiv.innerHTML = displayText; // Use innerHTML for formatted status/error messages
    chatArea.appendChild(messageDiv);
    chatArea.scrollTop = chatArea.scrollHeight; // Scroll to bottom
}

/**
 * Handles sending a chat message and receiving a response.
 * @param {HTMLInputElement} chatInput The input element for chat messages.
 * @param {HTMLElement} chatArea The chat area element to display messages.
 */
function handleChatMessage(chatInput, chatArea) {
    if (!chatInput || !chatArea) return;
    const userText = chatInput.value.trim();
    if (!userText) return;

    appendMessage(chatArea, userText, 'user');
    chatInput.value = '';

    // Using the enhanced appendMessage for "Thinking..." state
    appendMessage(chatArea, '<em>Pensando...</em>', 'ai-status');

    fetch('/ajax_actions/get_gemini_chat_response.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ prompt: userText })
    })
    .then(res => {
        if (!res.ok) { // Check for non-2xx HTTP status codes
            return res.json().then(errorData => { // Try to parse error response
                throw new Error(errorData.error || `Error HTTP ${res.status}`);
            }).catch(() => { // Fallback if error response isn't JSON
                throw new Error(`Error HTTP ${res.status}`);
            });
        }
        return res.json();
    })
    .then(data => {
        // Remove "Thinking..." message
        const thinkingMessage = chatArea.querySelector('.ai-status-message');
        if (thinkingMessage) thinkingMessage.remove();

        if (data.success && data.response) {
            appendMessage(chatArea, data.response, 'ai');
        } else if (data.error) {
            appendMessage(chatArea, data.error, 'ai-error');
        } else {
            appendMessage(chatArea, 'Respuesta inesperada del servidor.', 'ai-error');
        }
    })
    .catch(err => {
        const thinkingMessage = chatArea.querySelector('.ai-status-message');
        if (thinkingMessage) thinkingMessage.remove();
        appendMessage(chatArea, err.message || 'Error de conexión desconocido.', 'ai-error');
    });
}
