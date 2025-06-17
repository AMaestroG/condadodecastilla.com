document.addEventListener('DOMContentLoaded', () => {
    const themeToggleButton = document.getElementById('frt-theme-toggle');
    if (!themeToggleButton) {
        console.warn('Botón de cambio de tema #frt-theme-toggle no encontrado.');
        return;
    }

    const sunIconClass = 'fa-sun'; // Clase para el icono de sol
    const moonIconClass = 'fa-moon'; // Clase para el icono de luna
    const themeIcon = themeToggleButton.querySelector('i'); // Asume que el icono es un elemento <i>
    const darkThemeClass = 'dark-theme'; // Clase para el body en tema oscuro

    // Función para aplicar el tema y actualizar el icono
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add(darkThemeClass);
            if (themeIcon) {
                themeIcon.classList.remove(sunIconClass);
                themeIcon.classList.add(moonIconClass);
            }
            themeToggleButton.setAttribute('aria-label', 'Cambiar a tema claro');
        } else {
            document.body.classList.remove(darkThemeClass);
            if (themeIcon) {
                themeIcon.classList.remove(moonIconClass);
                themeIcon.classList.add(sunIconClass);
            }
            themeToggleButton.setAttribute('aria-label', 'Cambiar a tema oscuro');
        }
    }

    // Cargar tema guardado al inicio
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        // Opcional: detectar preferencia del sistema
        // if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        //     applyTheme('dark');
        // } else {
        //     applyTheme('light');
        // }
        // Por ahora, si no hay nada guardado, se queda el tema por defecto (claro)
        // y el icono de sol ya está puesto por defecto en el HTML.
        // Aseguramos el aria-label inicial:
        themeToggleButton.setAttribute('aria-label', 'Cambiar a tema oscuro');
    }

    // Event listener para el botón
    themeToggleButton.addEventListener('click', () => {
        const isDark = document.body.classList.contains(darkThemeClass);
        if (isDark) {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
        } else {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Opcional: Escuchar cambios en la preferencia del sistema
    // window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    //     const newColorScheme = e.matches ? 'dark' : 'light';
    //     applyTheme(newColorScheme);
    //     localStorage.setItem('theme', newColorScheme);
    // });
});
