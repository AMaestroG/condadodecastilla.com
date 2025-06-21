// assets/js/page-transitions.js - simple GSAP page transitions

document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.createElement('div');
  overlay.id = 'page-transition-overlay';
  document.body.appendChild(overlay);

  const fade = () => gsap.fromTo(overlay, {opacity: 0}, {opacity: 1, duration: 0.5});
  const slide = () => gsap.fromTo(overlay, {x: '-100%', opacity: 0}, {x: '0%', opacity: 1, duration: 0.5});
  const rotate = () => gsap.fromTo(overlay, {rotation: 0, opacity: 0}, {rotation: 360, opacity: 1, duration: 0.6});
  const transitions = [fade, slide, rotate];

  const runTransition = (link) => {
    overlay.classList.add('active');
    const anim = transitions[Math.floor(Math.random() * transitions.length)];
    anim();
    gsap.delayedCall(0.6, () => { window.location.href = link.href; });
  };

  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a) return;
    const url = new URL(a.href, location.href);
    if (url.origin !== location.origin || url.hash || a.target === '_blank' || a.hasAttribute('download')) return;
    e.preventDefault();
    if (!window.gsap) {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js';
      script.onload = () => runTransition(a);
      script.onerror = () => {
        const fallback = document.createElement('script');
        fallback.src = '/assets/vendor/js/gsap.min.js';
        fallback.onload = () => runTransition(a);
        document.head.appendChild(fallback);
      };
      document.head.appendChild(script);
    } else {
      runTransition(a);
    }
  });
});
