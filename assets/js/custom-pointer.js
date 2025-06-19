document.addEventListener('DOMContentLoaded', () => {
    const isTouch = window.matchMedia('(pointer: coarse)').matches || 'ontouchstart' in window;
    if (isTouch) {
        return;
    }

    document.body.classList.add('cursor-none');

    const pointer = document.createElement('div');
    pointer.id = 'custom-pointer';

    const outer = document.createElement('div');
    outer.className = 'pointer-outer border border-yellow-300 rounded-full w-10 h-10 opacity-30';
    const inner = document.createElement('div');
    inner.className = 'pointer-inner bg-[#d4af37] w-2.5 h-2.5 rounded-full';

    pointer.appendChild(outer);
    pointer.appendChild(inner);
    document.body.appendChild(pointer);

    let currentX = 0, currentY = 0, targetX = 0, targetY = 0;
    const lerp = (start, end, amt) => start + (end - start) * amt;

    function animate() {
        currentX = lerp(currentX, targetX, 0.2);
        currentY = lerp(currentY, targetY, 0.2);
        pointer.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
        requestAnimationFrame(animate);
    }
    animate();

    window.addEventListener('mousemove', e => {
        targetX = e.clientX;
        targetY = e.clientY;
    });

    function updateIcon(e) {
        const target = e.target.closest('.interactivo');
        pointer.classList.remove('icon-lupa', 'icon-columna');
        if (!target) return;
        if (target.dataset.cursor === 'columna') {
            pointer.classList.add('icon-columna');
        } else {
            pointer.classList.add('icon-lupa');
        }
    }

    document.addEventListener('mouseover', updateIcon);
});
