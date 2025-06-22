(function() {
  var TRANSITION_SOUND_PATH = '/assets/sounds/transition.mp3';
  function isMuted() {
    var btn = document.getElementById('mute-toggle');
    return btn && btn.getAttribute('aria-pressed') === 'true';
  }

  function playTransitionSound() {
    if (isMuted()) return;
    try {
      var audio = new Audio(TRANSITION_SOUND_PATH);
      audio.play();
    } catch (err) {
      console.error('Error playing transition sound:', err);
    }
  }

  function cleanupOverlay() {
    var overlay = document.getElementById('page-transition-overlay');
    if (!overlay) {
      playTransitionSound();
      return;
    }
    overlay.classList.add('fade-out');
    playTransitionSound();
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
