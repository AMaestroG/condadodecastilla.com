document.addEventListener('DOMContentLoaded', () => {
  const escudo = document.getElementById('header-escudo-overlay');
  if (!escudo) return;

  escudo.classList.add('draggable');

  let offsetX = 0;
  let offsetY = 0;
  let dragging = false;

  const onPointerDown = (e) => {
    dragging = true;
    escudo.setPointerCapture(e.pointerId);
    escudo.classList.add('dragging');
    const rect = escudo.getBoundingClientRect();
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
  };

  const onPointerMove = (e) => {
    if (!dragging) return;
    const x = e.clientX - offsetX;
    const y = e.clientY - offsetY;
    escudo.style.left = `${x}px`;
    escudo.style.top = `${y}px`;
  };

  const endDrag = (e) => {
    if (!dragging) return;
    dragging = false;
    escudo.releasePointerCapture(e.pointerId);
    escudo.classList.remove('dragging');
  };

  escudo.addEventListener('pointerdown', onPointerDown);
  escudo.addEventListener('pointermove', onPointerMove);
  escudo.addEventListener('pointerup', endDrag);
  escudo.addEventListener('pointercancel', endDrag);
});
