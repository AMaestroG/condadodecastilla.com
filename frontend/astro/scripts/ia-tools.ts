export const API_BASE_URL = (window as any).API_BASE_URL || '';

export function ensureOutputContainer(): HTMLElement {
  let cont = document.getElementById('ia-tools-response') as HTMLElement | null;
  if (!cont) {
    cont = document.createElement('div');
    cont.id = 'ia-tools-response';
    cont.className = 'ia-tools-response hidden';
    const sidebar = document.getElementById('ai-chat-panel');
    if (sidebar) {
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

export function showOutput(container: HTMLElement, html: string): void {
  container.innerHTML = html;
  container.classList.remove('hidden');
}

export function getMainText(): string {
  const main = document.querySelector('main');
  return main ? (main.textContent ?? '').trim() : document.body.textContent?.trim() ?? '';
}

export async function handleSummary(output: HTMLElement): Promise<void> {
  const text = getMainText();
  if (!text) {
    showOutput(output, '<p class="ia-tool-error">No se encontró texto para resumir.</p>');
    return;
  }
  showOutput(output, '<p><em>Generando resumen...</em></p>');
  try {
    const res = await fetch('/ajax_actions/get_summary.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ text_to_summarize: text }),
    });
    const data = await res.json();
    if (data.success && data.summary) {
      showOutput(output, data.summary);
    } else if (data.error) {
      showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
    } else {
      showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
    }
  } catch (err: any) {
    showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
  }
}

export async function handleTranslation(output: HTMLElement): Promise<void> {
  const text = getMainText();
  const target = prompt('Código de idioma destino (ej. en-ai, fr-ai, de-ai):', 'en-ai');
  if (!target) return;
  showOutput(output, '<p><em>Generando traducción...</em></p>');
  try {
    const res = await fetch('/ajax_actions/get_translation.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ text_to_translate: text, target_lang: target }),
    });
    const data = await res.json();
    if (data.success && data.translation) {
      showOutput(output, data.translation);
    } else if (data.error) {
      showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
    } else {
      showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
    }
  } catch (err: any) {
    showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
  }
}

export async function handleResearch(output: HTMLElement): Promise<void> {
  const query = prompt('Tema o pregunta a investigar:');
  if (!query) return;
  showOutput(output, '<p><em>Buscando información...</em></p>');
  try {
    const res = await fetch('/ajax_actions/get_research.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ query }),
    });
    const data = await res.json();
    if (data.success && data.research) {
      showOutput(output, data.research);
    } else if (data.error) {
      showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
    } else {
      showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
    }
  } catch (err: any) {
    showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
  }
}

export async function handleWebSearch(output: HTMLElement): Promise<void> {
  const query = prompt('Búsqueda en la web:');
  if (!query) return;
  showOutput(output, '<p><em>Generando búsqueda...</em></p>');
  try {
    const res = await fetch('/ajax_actions/get_web_search.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ query }),
    });
    const data = await res.json();
    if (data.success && data.results) {
      showOutput(output, data.results);
    } else if (data.error) {
      showOutput(output, `<p class="ia-tool-error">${data.error}</p>`);
    } else {
      showOutput(output, '<p class="ia-tool-error">Respuesta inesperada.</p>');
    }
  } catch (err: any) {
    showOutput(output, `<p class="ia-tool-error">${err.message}</p>`);
  }
}

export function appendMessage(chatArea: HTMLElement, text: string, sender: 'user' | 'ai' | 'ai-status' | 'ai-error'): void {
  const messageDiv = document.createElement('div');
  let messageClass = 'chat-message';
  let displayText = text;
  const sanitizedText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
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
      messageClass += ' ai-status-message';
      break;
    case 'ai-error':
      messageClass += ' ai-error-message';
      displayText = `<strong>IA Error:</strong> ${sanitizedText}`;
      break;
  }
  messageDiv.className = messageClass;
  messageDiv.innerHTML = displayText;
  chatArea.appendChild(messageDiv);
  chatArea.scrollTop = chatArea.scrollHeight;
}

export async function handleChatMessage(chatInput: HTMLInputElement, chatArea: HTMLElement): Promise<void> {
  const userText = chatInput.value.trim();
  if (!userText) return;
  appendMessage(chatArea, userText, 'user');
  chatInput.value = '';
  const responseBox = document.getElementById('ai-response-box') as HTMLTextAreaElement | null;
  if (responseBox) responseBox.value = '';
  appendMessage(chatArea, '<em>Pensando...</em>', 'ai-status');
  try {
    const res = await fetch('/ajax_actions/get_gemini_chat_response.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ prompt: userText }),
    });
    if (!res.ok) {
      const errorData = await res.json().catch(() => ({}));
      throw new Error(errorData.error || `Error HTTP ${res.status}`);
    }
    const data = await res.json();
    const thinking = chatArea.querySelector('.ai-status-message');
    if (thinking) thinking.remove();
    if (data.success && data.response) {
      appendMessage(chatArea, data.response, 'ai');
      if (responseBox) responseBox.value = data.response;
    } else if (data.error) {
      appendMessage(chatArea, data.error, 'ai-error');
      if (responseBox) responseBox.value = data.error;
    } else {
      appendMessage(chatArea, 'Respuesta inesperada del servidor.', 'ai-error');
      if (responseBox) responseBox.value = 'Respuesta inesperada del servidor.';
    }
    showChatDialog(chatArea.innerText);
  } catch (err: any) {
    const thinking = chatArea.querySelector('.ai-status-message');
    if (thinking) thinking.remove();
    const errMsg = err.message || 'Error de conexión desconocido.';
    appendMessage(chatArea, errMsg, 'ai-error');
    if (responseBox) responseBox.value = errMsg;
    showChatDialog(chatArea.innerText);
  }
}

export function showChatDialog(text: string): void {
  const dialog = document.getElementById('ai-dialog') as HTMLDialogElement | null;
  if (!dialog) return;
  dialog.textContent = text;
  if (typeof (dialog as any).showModal === 'function') {
    if (!dialog.open) dialog.showModal();
  } else {
    dialog.style.display = 'block';
  }
}
