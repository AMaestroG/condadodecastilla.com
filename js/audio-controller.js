(function(){
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
})();
