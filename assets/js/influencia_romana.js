document.addEventListener('DOMContentLoaded', () => {
    const markers = document.querySelectorAll('#imperial-timeline span.rounded-full');
    markers.forEach(m => {
        m.style.filter = 'url(#glow)';
    });
});
