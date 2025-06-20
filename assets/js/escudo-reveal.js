document.addEventListener('DOMContentLoaded', () => {
  const escudo = document.getElementById('header-escudo-overlay');
  if (!escudo) return;
  const audio = new Audio('https://actions.google.com/sounds/v1/cartoon/cartoon_boing.ogg');
  escudo.addEventListener('click', () => {
    escudo.classList.remove('reveal');
    void escudo.offsetWidth; // trigger reflow for replay
    escudo.classList.add('reveal');
    audio.currentTime = 0;
    audio.play().catch(err => console.error('Audio play failed', err));
  });
});
