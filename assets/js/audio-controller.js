(function(){
    function handleMenuToggle(open){
        // Check global mute state from the mute button
        const globalMuteButton = document.getElementById('mute-toggle');
        const isMuted = globalMuteButton ? globalMuteButton.getAttribute('aria-pressed') === 'true' : false;

        if (!isMuted) {
            if (open) {
                const audioOpen = new Audio('https://example.com/audio/menu-open.mp3');
                audioOpen.play().catch(error => console.error("Error playing menu open sound:", error));
            } else {
                const audioClose = new Audio('https://example.com/audio/menu-close.mp3');
                audioClose.play().catch(error => console.error("Error playing menu close sound:", error));
            }
        }

        // Existing logic for adjusting volume of page media
        document.querySelectorAll('audio, video').forEach(el => {
            if(el.dataset.originalVolume === undefined){
                el.dataset.originalVolume = el.volume;
            }
            const original = parseFloat(el.dataset.originalVolume);
            if(open){
                el.volume = original * 0.3;
            } else {
                el.volume = original;
            }
        });
    }

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

    function playTransitionSound(){
        const globalMuteButton = document.getElementById('mute-toggle');
        const isMuted = globalMuteButton ? globalMuteButton.getAttribute('aria-pressed') === 'true' : false;

        if (isMuted) {
            return Promise.resolve();
        }

        const audio = new Audio('https://example.com/audio/transition.mp3');
        audio.currentTime = 0;
        return audio.play().catch(err => console.error('Error playing transition sound:', err));
    }

    window.audioController = { handleMenuToggle, playTransitionSound };
})();
