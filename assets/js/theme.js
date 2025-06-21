export function initTheme() {
    const paletteClasses = ['palette-dawn','palette-day','palette-dusk','palette-night'];

    const detectPalette = () => {
        const h = new Date().getHours();
        if (h >= 5 && h < 10) return 'dawn';
        if (h >= 10 && h < 17) return 'day';
        if (h >= 17 && h < 21) return 'dusk';
        return 'night';
    };

    const applyPalette = (p) => {
        document.body.classList.remove(...paletteClasses);
        document.body.classList.add(`palette-${p}`);
    };

    const storedPalette = localStorage.getItem('palette');
    if (storedPalette && storedPalette !== 'auto') {
        applyPalette(storedPalette);
    } else {
        applyPalette(detectPalette());
    }

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        const storedTheme = localStorage.getItem('theme');
        let activeTheme = storedTheme;
        if (!activeTheme) {
            activeTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        if (activeTheme === 'dark') {
            document.body.classList.add('dark-mode');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
        themeToggle.setAttribute('aria-pressed', activeTheme === 'dark' ? 'true' : 'false');
        if (!storedTheme) {
            localStorage.setItem('theme', activeTheme);
        }
        themeToggle.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark-mode');
            if (icon) {
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
            }
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeToggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });
    }

    const moonToggle = document.getElementById('moon-toggle');
    if (moonToggle) {
        const storedMoon = localStorage.getItem('moon');
        if (storedMoon === 'on') {
            document.body.classList.add('luna');
        }
        moonToggle.addEventListener('click', () => {
            const active = document.body.classList.toggle('luna');
            if (active) {
                localStorage.setItem('moon', 'on');
            } else {
                localStorage.removeItem('moon');
            }
        });
    }

    const paletteToggle = document.getElementById('palette-toggle');
    if (paletteToggle) {
        const order = ['auto','dawn','day','dusk','night'];
        let index = order.indexOf(storedPalette || 'auto');
        paletteToggle.addEventListener('click', () => {
            index = (index + 1) % order.length;
            const p = order[index];
            if (p === 'auto') {
                localStorage.removeItem('palette');
                applyPalette(detectPalette());
            } else {
                localStorage.setItem('palette', p);
                applyPalette(p);
            }
        });
    }
}
