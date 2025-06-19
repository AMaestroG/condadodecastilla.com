// assets/js/audio-controller.js - simple volume controller
(function (window, document) {
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

    let muted = false;

    function updateMuteState(btn) {
        if (!btn) return;
        btn.setAttribute('aria-pressed', muted ? 'true' : 'false');
        btn.textContent = muted ? '🔇' : '🔊';
    }

    function toggleMute(btn) {
        muted = !muted;
        document.querySelectorAll('audio, video').forEach(el => {
            el.muted = muted;
        });
        updateMuteState(btn);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mute-toggle');
        if (btn) {
            updateMuteState(btn);
            btn.addEventListener('click', () => toggleMute(btn));
        }
    });

    document.addEventListener('menu-toggled', e => {
        handleMenuToggle(!!e.detail.open);
    });

    window.audioController = { handleMenuToggle };
})(window, document);
