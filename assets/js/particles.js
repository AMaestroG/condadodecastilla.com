// Floating particles effect with density control

document.addEventListener('DOMContentLoaded', () => {
  const container = document.createElement('div');
  container.id = 'particle-container';
  document.body.appendChild(container);

  let density = 15; // particles per second
  let intervalId;

  function createParticle() {
    const p = document.createElement('span');
    p.className = 'particle';
    const size = 5 + Math.random() * 5;
    p.style.width = size + 'px';
    p.style.height = size + 'px';
    p.style.left = Math.random() * 100 + '%';
    p.style.animationDelay = Math.random() * 20 + 's';
    container.appendChild(p);
    p.addEventListener('animationend', () => p.remove());
  }

  function start() {
    clearInterval(intervalId);
    intervalId = setInterval(createParticle, 1000 / density);
  }

  start();

  const hero = document.querySelector('.section.hero');
  const timeline = document.querySelector('.section.timeline-section-summary');

  if ('IntersectionObserver' in window && (hero || timeline)) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.target === hero) {
          density = entry.isIntersecting ? 25 : 15;
          start();
        } else if (entry.target === timeline) {
          density = entry.isIntersecting ? 5 : 15;
          start();
        }
      });
    }, { threshold: 0.5 });
    if (hero) observer.observe(hero);
    if (timeline) observer.observe(timeline);
  }
});
