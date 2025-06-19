document.addEventListener('DOMContentLoaded', () => {
    if (!document.body.classList.contains('luna')) return;

    const container = document.createElement('div');
    container.className = 'fireflies-container';

    const total = 40;
    for (let i = 0; i < total; i++) {
        const f = document.createElement('div');
        f.className = 'firefly';
        const size = 1 + Math.random() * 2;
        f.style.width = `${size}px`;
        f.style.height = `${size}px`;
        f.style.left = `${Math.random() * 100}%`;
        f.style.animationDelay = `${Math.random() * 10}s`;
        f.style.animationDuration = `${10 + Math.random() * 10}s`;
        container.appendChild(f);
    }

    document.body.appendChild(container);
});
