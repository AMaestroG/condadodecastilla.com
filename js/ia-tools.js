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
        cont.style.position = 'fixed';
        cont.style.top = '10%';
        cont.style.left = '10%';
        cont.style.right = '10%';
        cont.style.maxHeight = '80vh';
        cont.style.overflow = 'auto';
        cont.style.padding = '20px';
        cont.style.background = 'var(--epic-alabaster-bg)';
        cont.style.border = '2px solid var(--epic-gold-secondary)';
        cont.style.borderRadius = 'var(--global-border-radius)';
        cont.style.boxShadow = 'var(--global-box-shadow-dark)';
        cont.style.zIndex = '2000';
        cont.style.display = 'none';
        document.body.appendChild(cont);
    }
    return cont;
}

function showOutput(container, html) {
    container.innerHTML = html;
    container.style.display = 'block';
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
