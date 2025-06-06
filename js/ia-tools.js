// js/ia-tools.js
// Handles IA tool actions: summary, correction placeholder and translation.

document.addEventListener('DOMContentLoaded', () => {
    const summaryBtn = document.getElementById('ia-summary-btn');
    const correctionBtn = document.getElementById('ia-correction-btn');
    const translateBtn = document.getElementById('ia-translate-btn');

    const output = ensureOutputContainer();

    if (summaryBtn) {
        summaryBtn.addEventListener('click', () => handleSummary(output));
    }

    if (translateBtn) {
        translateBtn.addEventListener('click', () => handleTranslation(output));
    }

    if (correctionBtn) {
        correctionBtn.addEventListener('click', () => handleCorrection(output));
    }
});

function ensureOutputContainer() {
    let cont = document.getElementById('ia-tools-output');
    if (!cont) {
        cont = document.createElement('div');
        cont.id = 'ia-tools-output';
        cont.className = 'ia-output hidden';

        const closeBtn = document.createElement('button');
        closeBtn.className = 'ia-output-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', () => cont.classList.add('hidden'));
        cont.appendChild(closeBtn);

        const content = document.createElement('div');
        content.className = 'ia-output-content';
        cont.appendChild(content);

        makeDraggable(cont, closeBtn);

        document.body.appendChild(cont);
    }
    return cont;
}

function showOutput(container, html) {
    const content = container.querySelector('.ia-output-content');
    if (content) {
        content.innerHTML = html;
    } else {
        container.innerHTML = html;
    }
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

function handleCorrection(output) {
    const text = getMainText();
    showOutput(output, '<p><em>Generando corrección...</em></p>');
    fetch('/ajax_actions/get_correction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ text_to_correct: text })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.correction) {
            showOutput(output, data.correction);
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
