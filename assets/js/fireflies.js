document.addEventListener('DOMContentLoaded', () => {
  if (!document.body.classList.contains('luna')) return;

  const container = document.createElement('div');
  container.className = 'fireflies-container';
  document.body.appendChild(container);

  function createFirefly() {
    const firefly = document.createElement('div');
    firefly.className = 'firefly';
    firefly.style.left = `${Math.random() * 100}vw`;
    firefly.style.top = `${Math.random() * 100}vh`;
    const dx = (Math.random() * 20 - 10).toFixed(2);
    const dy = (Math.random() * 20 - 10).toFixed(2);
    const duration = 10 + Math.random() * 10;
    firefly.style.setProperty('--tx', `${dx}vw`);
    firefly.style.setProperty('--ty', `${dy}vh`);
    firefly.style.setProperty('--firefly-duration', `${duration}s`);
    container.appendChild(firefly);
    firefly.addEventListener('animationend', () => firefly.remove());
  }

  for (let i = 0; i < 20; i++) {
    setTimeout(createFirefly, i * 500);
  }
  setInterval(createFirefly, 3000);
});
