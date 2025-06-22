(function() {
  function cleanupOverlay() {
    const overlay = document.getElementById('page-transition-overlay');
    if (!overlay) return;
    overlay.classList.add('fade-out');
    overlay.addEventListener('transitionend', function() {
      if (overlay && overlay.parentNode) {
        overlay.parentNode.removeChild(overlay);
      }
    });
    // Fallback in case transitionend doesn't fire
    setTimeout(function() {
      if (overlay && overlay.parentNode) {
        overlay.parentNode.removeChild(overlay);
      }
    }, 1000);
  }

  if (document.readyState === 'complete') {
    cleanupOverlay();
  } else {
    window.addEventListener('load', cleanupOverlay);
    document.addEventListener('DOMContentLoaded', cleanupOverlay);
  }
})();
