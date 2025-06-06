document.addEventListener('DOMContentLoaded', () => {
    const summaryBtn = document.getElementById('btn-summary');
    const correctBtn = document.getElementById('btn-correct');
    const langBtns = document.querySelectorAll('.lang-btn');
    const content = document.getElementById('post-content');
    const aiResult = document.getElementById('ai-result');
    const originalHTML = content ? content.innerHTML : '';

    async function postData(url = '', data = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }

    if (summaryBtn) {
        summaryBtn.addEventListener('click', async () => {
            aiResult.textContent = 'Generando resumen...';
            const res = await postData('/ajax_actions/get_summary.php', {text_to_summarize: content.textContent});
            aiResult.innerHTML = res.success ? res.summary : res.error;
        });
    }

    if (correctBtn) {
        correctBtn.addEventListener('click', async () => {
            aiResult.textContent = 'Corrigiendo...';
            const res = await postData('/ajax_actions/get_correction.php', {text_to_correct: content.textContent});
            aiResult.innerHTML = res.success ? res.correction : res.error;
        });
    }

    langBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const lang = btn.getAttribute('data-lang');
            aiResult.textContent = 'Traduciendo...';
            const res = await postData('/ajax_actions/get_translation.php', {text: content.textContent, target_lang: lang});
            aiResult.innerHTML = res.success ? res.translation : res.error;
        });
    });
});
