(function(){
    function handleMenuToggle(open){
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

    window.audioController = { handleMenuToggle };
})();
