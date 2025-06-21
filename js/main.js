/**
 * main.js: Script para gestionar la interactividad del sistema de menús.
 * Incluye:
 * - Control del menú hamburguesa para móviles.
 * - Despliegue de submenús en móviles.
 * - Funcionalidad del panel deslizante (ej. para perfil de usuario).
 * - Mejoras de accesibilidad (manejo de ARIA y foco).
 */
document.addEventListener('DOMContentLoaded', () => {
    // Selección de elementos del DOM para el menú
    const navToggle = document.getElementById('navToggle'); // Botón hamburguesa
    const mainNavList = document.getElementById('mainNavList'); // Contenedor <ul> del menú principal

    // Selección de elementos del DOM para el panel deslizante
    const openSlidingPanelButton = document.getElementById('openSlidingPanel'); // Botón que abre el panel
    const slidingPanel = document.getElementById('slidingPanel'); // El panel en sí
    const closeSlidingPanelButton = document.getElementById('closeSlidingPanel'); // Botón para cerrar el panel

    // Selección de todos los botones que despliegan submenús (generalmente en móvil)
    const allSubmenuToggles = document.querySelectorAll('.submenu-toggle');

    // ==========================================================================
    // 1. Funcionalidad del Menú Hamburguesa (Móvil)
    // ==========================================================================
    if (navToggle && mainNavList) {
        navToggle.addEventListener('click', () => {
            // Alterna la clase 'is-active' para mostrar/ocultar el menú (controlado por CSS)
            mainNavList.classList.toggle('is-active');
            // Alterna la clase 'is-active' en el botón para animar el icono (ej. hamburguesa a 'X')
            navToggle.classList.toggle('is-active');

            const isExpanded = mainNavList.classList.contains('is-active');
            // Actualiza el atributo ARIA para accesibilidad
            navToggle.setAttribute('aria-expanded', isExpanded.toString());

            // Si el menú se cierra, también se cierran todos los submenús que pudieran estar abiertos.
            if (!isExpanded) {
                closeAllSubmenus();
            }
        });
    }

    // ==========================================================================
    // 2. Funcionalidad de Despliegue de Submenús (principalmente para Móvil)
    // ==========================================================================
    allSubmenuToggles.forEach(toggle => {
        toggle.addEventListener('click', (event) => {
            // Previene la acción por defecto si el toggle fuera un enlace <a>
            event.preventDefault();

            // El submenú es el siguiente elemento hermano del botón toggle en el DOM
            const submenu = toggle.nextElementSibling;
            // El elemento <li> que contiene tanto el link/botón del submenú como el submenú en sí
            const parentMenuItem = toggle.closest('.has-submenu');

            // Asegurarse que el elemento 'submenu' existe y es un submenú válido
            if (submenu && (submenu.classList.contains('nav-submenu') || submenu.classList.contains('nav-submenu-nested'))) {
                // Alterna la clase 'is-open' para mostrar/ocultar el submenú (controlado por CSS)
                const isOpen = submenu.classList.toggle('is-open');
                // Alterna la clase 'is-open' en el botón para animar su icono (ej. flecha)
                toggle.classList.toggle('is-open');
                // Actualiza el atributo ARIA
                toggle.setAttribute('aria-expanded', isOpen.toString());

                // Alterna una clase en el <li> padre si se necesita un estilo específico
                if (parentMenuItem) {
                    parentMenuItem.classList.toggle('is-active-submenu', isOpen);
                }

                // Opcional: Implementación de "acordeón" para cerrar otros submenús del mismo nivel.
                // Para la estructura actual, el toggle individual es a menudo suficiente.
                // if (isOpen) {
                //     const parentList = submenu.parentElement.parentElement;
                //     parentList.querySelectorAll('.nav-submenu.is-open, .nav-submenu-nested.is-open').forEach(otherSubmenu => {
                //         if (otherSubmenu !== submenu) {
                //             otherSubmenu.classList.remove('is-open');
                //             const otherToggle = otherSubmenu.previousElementSibling;
                //             if (otherToggle) {
                //                 otherToggle.classList.remove('is-open');
                //                 otherToggle.setAttribute('aria-expanded', 'false');
                //             }
                //             otherSubmenu.closest('.has-submenu').classList.remove('is-active-submenu');
                //         }
                //     });
                // }
            }
        });
    });

    /**
     * Cierra todos los submenús que estén actualmente abiertos.
     * Útil al cerrar el menú principal móvil o al implementar lógica de acordeón.
     */
    function closeAllSubmenus() {
        document.querySelectorAll('.nav-submenu.is-open, .nav-submenu-nested.is-open').forEach(submenu => {
            submenu.classList.remove('is-open');
            const toggle = submenu.previousElementSibling; // El botón que controla este submenú
            if (toggle && (toggle.classList.contains('submenu-toggle') || toggle.matches('a[aria-haspopup="true"]'))) {
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
            const parentMenuItem = submenu.closest('.has-submenu');
            if (parentMenuItem) {
                parentMenuItem.classList.remove('is-active-submenu');
            }
        });
    }

    // ==========================================================================
    // 3. Funcionalidad del Panel Deslizante
    // ==========================================================================
    if (openSlidingPanelButton && slidingPanel && closeSlidingPanelButton) {
        let previousActiveElement; // Para restaurar el foco después de cerrar el panel

        /** Abre el panel deslizante. */
        const openPanel = () => {
            previousActiveElement = document.activeElement; // Guarda el elemento activo
            slidingPanel.classList.add('is-open'); // Muestra el panel (controlado por CSS)
            slidingPanel.setAttribute('aria-hidden', 'false'); // Indica que el panel es visible para lectores de pantalla

            // Mueve el foco al primer elemento enfocable del panel (ej. botón de cierre)
            // Se usa setTimeout para asegurar que la transición CSS haya comenzado y el elemento sea enfocable.
            setTimeout(() => closeSlidingPanelButton.focus(), 50);

            // Añade listeners para manejo de teclado (Escape y atrapar foco)
            document.addEventListener('keydown', handlePanelKeyDown);
        };

        /** Cierra el panel deslizante. */
        const closePanel = () => {
            slidingPanel.classList.remove('is-open'); // Oculta el panel
            slidingPanel.setAttribute('aria-hidden', 'true'); // Indica que está oculto

            // Devuelve el foco al elemento que lo tenía antes de abrir el panel
            if (previousActiveElement) {
                previousActiveElement.focus();
            }
            // Elimina listeners de teclado
            document.removeEventListener('keydown', handlePanelKeyDown);
        };

        // Event listener para el botón que abre el panel
        openSlidingPanelButton.addEventListener('click', (event) => {
            event.preventDefault(); // Si el botón es un <a>
            openPanel();
        });

        // Event listener para el botón que cierra el panel
        closeSlidingPanelButton.addEventListener('click', closePanel);

        // Event listener para cerrar el panel si se hace clic fuera de él
        document.addEventListener('click', (event) => {
            if (slidingPanel.classList.contains('is-open') &&  // Si el panel está abierto
                !slidingPanel.contains(event.target) &&        // Y el clic no fue dentro del panel
                event.target !== openSlidingPanelButton &&     // Y el clic no fue en el botón que lo abre
                !openSlidingPanelButton.contains(event.target)) { // (Considerando si el botón tiene hijos)
                closePanel();
            }
        });

        /**
         * Maneja eventos de teclado para el panel:
         * - Cierra el panel con la tecla "Escape".
         * - Atrapa el foco dentro del panel (Tab y Shift+Tab).
         */
        const handlePanelKeyDown = (event) => {
            if (event.key === 'Escape') {
                closePanel();
                return;
            }

            // Lógica para atrapar el foco (Trap Focus)
            if (event.key === 'Tab' && slidingPanel.classList.contains('is-open')) {
                // Lista de todos los elementos enfocables dentro del panel
                const focusableElements = slidingPanel.querySelectorAll(
                    'a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled])'
                );
                if (focusableElements.length === 0) return;

                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];

                if (event.shiftKey) { // Si se presiona Shift + Tab
                    if (document.activeElement === firstElement) {
                        lastElement.focus(); // Mueve el foco al último elemento
                        event.preventDefault();
                    }
                } else { // Si se presiona Tab
                    if (document.activeElement === lastElement) {
                        firstElement.focus(); // Mueve el foco al primer elemento
                        event.preventDefault();
                    }
                }
            }
        };
    }

    // ==========================================================================
    // 4. Inicialización de Estados ARIA
    // ==========================================================================
    // Asegura que los estados ARIA iniciales sean correctos al cargar la página.

    // Para los toggles de submenús (móvil)
    allSubmenuToggles.forEach(toggle => {
        const submenu = toggle.nextElementSibling;
        if (submenu && (submenu.classList.contains('nav-submenu') || submenu.classList.contains('nav-submenu-nested'))) {
            const isOpen = submenu.classList.contains('is-open');
            toggle.setAttribute('aria-expanded', isOpen.toString());
        }
    });

    // Para el menú principal móvil
    if (navToggle && mainNavList) {
        const isExpanded = mainNavList.classList.contains('is-active');
        navToggle.setAttribute('aria-expanded', isExpanded.toString());
    }

    // Para el panel deslizante
    if (slidingPanel) {
        slidingPanel.setAttribute('aria-hidden', !slidingPanel.classList.contains('is-open').toString());
    }

    // Nota sobre submenús de escritorio:
    // La interactividad (hover/focus) de los submenús de escritorio se maneja principalmente con CSS.
    // Los atributos ARIA como `aria-expanded` son más cruciales para interacciones basadas en JS (clics),
    // que es el caso de los submenús en móvil. Si los submenús de escritorio también se activaran
    // por clic, se necesitaría una lógica similar a la de `allSubmenuToggles`.
    // La clase `is-active-link` para marcar la página actual requeriría JS adicional
    // para comparar `window.location.href` con los `href` de los enlaces del menú.
});
