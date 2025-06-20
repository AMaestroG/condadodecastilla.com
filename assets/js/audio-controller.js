(function(){
    const api = {};

    api.handleMenuToggle = function(open) {
        document.querySelectorAll('audio, video').forEach(el => {
            if (open) {
                if (!el.hasAttribute('data-prev-volume')) {
                    el.dataset.prevVolume = el.volume;
                    el.volume = Math.max(0, Math.min(1, el.volume * 0.2));
                }
            } else {
                if (el.hasAttribute('data-prev-volume')) {
                    el.volume = parseFloat(el.dataset.prevVolume);
                    el.removeAttribute('data-prev-volume');
                }
            }
        });
    };

    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('mute-toggle');
        if(!btn) return;

        let muted = false;
        const updateState = () => {
            btn.setAttribute('aria-pressed', muted ? 'true' : 'false');
            btn.textContent = muted ? '🔇' : '🔊';
        };
        updateState();

        btn.addEventListener('click', () => {
            muted = !muted;
            document.querySelectorAll('audio, video').forEach(el => {
                el.muted = muted;
            });
            updateState();
        });
    });

    window.audioController = api;
})();
