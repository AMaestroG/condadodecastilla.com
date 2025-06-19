// assets/js/parallax.js - simple parallax effect based on scroll

document.addEventListener('DOMContentLoaded', () => {
  const layers = document.querySelectorAll('[data-speed]');
  if (!layers.length) return;

  const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  let active = !mediaQuery.matches;

  const update = () => {
    const offset = window.pageYOffset;
    layers.forEach(el => {
      const speed = parseFloat(el.dataset.speed || '0');
      el.style.transform = `translateY(${offset * speed}px)`;
    });
  };

  const enable = () => {
    if (!active) {
      active = true;
      window.addEventListener('scroll', update, { passive: true });
      update();
    }
  };

  const disable = () => {
    if (active) {
      active = false;
      window.removeEventListener('scroll', update);
      layers.forEach(el => (el.style.transform = ''));
    }
  };

  mediaQuery.addEventListener('change', () => {
    if (mediaQuery.matches) {
      disable();
    } else {
      enable();
    }
  });

  if (active) {
    window.addEventListener('scroll', update, { passive: true });
    update();
  }
});
