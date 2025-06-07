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
});

function ensureOutputContainer() {
    let cont = document.getElementById('ia-tools-response');
    if (!cont) {
        cont = document.createElement('div');
        cont.id = 'ia-tools-response';
        cont.className = 'ia-tools-response hidden';
        const sidebar = document.getElementById('ia-chat-sidebar');
        if (sidebar) {
            sidebar.insertBefore(cont, sidebar.querySelector('#ia-tools-menu'));
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
        showOutput(output, '<p style="color:red;">No se encontró texto para resumir.</p>');
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
            showOutput(output, `<p style="color:red;">${data.error}</p>`);
        } else {
            showOutput(output, '<p style="color:red;">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p style="color:red;">${err.message}</p>`);
    });
}

function handleTranslation(output) {
    const text = getMainText();
    const target = prompt('Código de idioma destino (ej. en-ai, fr-ai):', 'en-ai');
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
            showOutput(output, `<p style="color:red;">${data.error}</p>`);
        } else {
            showOutput(output, '<p style="color:red;">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p style="color:red;">${err.message}</p>`);
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
            showOutput(output, `<p style="color:red;">${data.error}</p>`);
        } else {
            showOutput(output, '<p style="color:red;">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p style="color:red;">${err.message}</p>`);
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
            showOutput(output, `<p style="color:red;">${data.error}</p>`);
        } else {
            showOutput(output, '<p style="color:red;">Respuesta inesperada.</p>');
        }
    })
    .catch(err => {
        showOutput(output, `<p style="color:red;">${err.message}</p>`);
    });
}

function makeDraggable(element, handle) {
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let origX = 0;
    let origY = 0;

    const dragHandle = handle || element;

    dragHandle.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return; // only left click
        isDragging = true;
        const rect = element.getBoundingClientRect();
        startX = e.clientX;
        startY = e.clientY;
        origX = rect.left;
        origY = rect.top;
        element.classList.add('dragging');
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        element.style.left = origX + dx + 'px';
        element.style.top = origY + dy + 'px';
    });

    document.addEventListener('mouseup', () => {
        if (isDragging) {
            isDragging = false;
            element.classList.remove('dragging');
        }
    });
}
