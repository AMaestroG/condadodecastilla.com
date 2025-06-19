// assets/js/audio-controller.js - simple volume controller
(function(window, document) {
    function adjustVolume(lowered) {
        document.querySelectorAll('audio, video').forEach(el => {
            if (!el.dataset.originalVolume) {
                el.dataset.originalVolume = el.volume;
            }
            const orig = parseFloat(el.dataset.originalVolume);
            el.volume = lowered ? Math.max(0, orig * 0.3) : orig;
        });
    }

    function handleMenuToggle(anyOpen) {
        adjustVolume(anyOpen);
    }

    document.addEventListener('menu-toggled', e => {
        handleMenuToggle(!!e.detail.open);
    });

    window.audioController = { handleMenuToggle };
})(window, document);
