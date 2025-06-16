async function loadMessages() {
    try {
        const response = await fetch('/api/foro/messages');
        const data = await response.json();
        const list = document.getElementById('message-list');
        list.innerHTML = '';
        data.forEach(msg => {
            const item = document.createElement('li');
            item.textContent = `${msg.author}: ${msg.content}`;
            list.appendChild(item);
        });
    } catch (err) {
        console.error('Error loading messages', err);
    }
}

async function sendMessage(event) {
    event.preventDefault();
    const author = document.getElementById('author').value;
    const content = document.getElementById('content').value;
    await fetch('/api/foro/messages', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({author, content})
    });
    document.getElementById('content').value = '';
    loadMessages();
}

document.addEventListener('DOMContentLoaded', () => {
    loadMessages();
    document.getElementById('message-form').addEventListener('submit', sendMessage);
});
