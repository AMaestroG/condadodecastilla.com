// assets/js/parallax.js - simple parallax effect based on scroll

document.addEventListener('DOMContentLoaded', () => {
  const layers = document.querySelectorAll('[data-speed]');
  if (!layers.length) return;

  const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  const minWidth = 768;
  let active = !mediaQuery.matches && window.innerWidth >= minWidth;
  let rafId;

  const update = () => {
    const offset = window.pageYOffset;
    layers.forEach(el => {
      const speed = parseFloat(el.dataset.speed || '0');
      el.style.transform = `translateY(${offset * speed}px)`;
    });
  };

  const loop = () => {
    if (!active) return;
    update();
    rafId = requestAnimationFrame(loop);
  };

  const enable = () => {
    if (!active && !mediaQuery.matches && window.innerWidth >= minWidth) {
      active = true;
      rafId = requestAnimationFrame(loop);
    }
  };

  const disable = () => {
    if (active) {
      active = false;
      if (rafId) cancelAnimationFrame(rafId);
      layers.forEach(el => (el.style.transform = ''));
    }
  };

  const handleResize = () => {
    if (window.innerWidth < minWidth) {
      disable();
    } else if (!mediaQuery.matches) {
      enable();
    }
  };

  mediaQuery.addEventListener('change', () => {
    if (mediaQuery.matches) {
      disable();
    } else if (window.innerWidth >= minWidth) {
      enable();
    }
  });

  window.addEventListener('resize', handleResize);

  if (active) {
    rafId = requestAnimationFrame(loop);
  }
});
