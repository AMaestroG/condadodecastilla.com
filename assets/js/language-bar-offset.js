function applyLanguageBarOffset() {
    const bar = document.querySelector('.language-bar');
    if (!bar) return;
    const margin = 10; // px
    const offset = bar.getBoundingClientRect().bottom + margin;
    document.documentElement.style.setProperty('--language-bar-offset', `${offset}px`);
}

window.addEventListener('load', applyLanguageBarOffset);
window.applyLanguageBarOffset = applyLanguageBarOffset;
