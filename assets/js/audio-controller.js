// assets/js/audio-controller.js
// Plays an ambient audio loop and toggles mute state via #mute-toggle

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('mute-toggle');
    if (!button) return;

    const audio = new Audio('/assets/audio/ambient.mp3');
    audio.loop = true;
    audio.volume = 0.5;

    const wasMuted = localStorage.getItem('ambientMuted') === 'true';
    if (!wasMuted) {
        audio.play().catch(() => {});
        button.classList.remove('muted');
    } else {
        button.classList.add('muted');
    }

    function updateState(muted) {
        if (muted) {
            audio.pause();
            button.classList.add('muted');
            localStorage.setItem('ambientMuted', 'true');
        } else {
            audio.play().catch(() => {});
            button.classList.remove('muted');
            localStorage.setItem('ambientMuted', 'false');
        }
    }

    button.addEventListener('click', () => {
        const currentlyMuted = audio.paused || button.classList.contains('muted');
        updateState(!currentlyMuted);
    });
});
