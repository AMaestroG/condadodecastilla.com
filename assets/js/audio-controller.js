(function(){
    function handleMenuToggle(open){
        document.querySelectorAll('audio, video').forEach(el => {
            if(!el.dataset.prevVolume){
                el.dataset.prevVolume = el.volume;
            }
            if(open){
                el.volume = el.dataset.prevVolume * 0.3;
            } else if(el.dataset.prevVolume !== undefined){
                el.volume = parseFloat(el.dataset.prevVolume);
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
