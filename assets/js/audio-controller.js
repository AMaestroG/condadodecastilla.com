(function(){
    const previousVolumes = new Map();

    const setMutedState = (state) => {
        document.querySelectorAll('audio, video').forEach(el => {
            el.muted = state;
        });
    };

    const exposeController = (btn) => {
        window.audioController = {
            handleMenuToggle(open) {
                document.querySelectorAll('audio, video').forEach(el => {
                    if (open) {
                        previousVolumes.set(el, el.volume);
                        el.volume = Math.max(0, el.volume * 0.2);
                    } else if (previousVolumes.has(el)) {
                        el.volume = previousVolumes.get(el);
                    }
                });
                if (!open) previousVolumes.clear();
            }
        };

        let muted = false;

        const updateState = () => {
            btn.setAttribute('aria-pressed', muted ? 'true' : 'false');
            btn.textContent = muted ? '🔇' : '🔊';
        };

        updateState();

        btn.addEventListener('click', () => {
            muted = !muted;
            setMutedState(muted);
            updateState();
        });
    };

    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('mute-toggle');
        if(btn) {
            exposeController(btn);
        } else {
            // Even if the button is missing, expose the controller for menu events
            window.audioController = {
                handleMenuToggle(open) {
                    document.querySelectorAll('audio, video').forEach(el => {
                        if (open) {
                            previousVolumes.set(el, el.volume);
                            el.volume = Math.max(0, el.volume * 0.2);
                        } else if (previousVolumes.has(el)) {
                            el.volume = previousVolumes.get(el);
                        }
                    });
                    if (!open) previousVolumes.clear();
                }
            };
        }
    });
})();
