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
            navToggle.setAttribute('aria-expanded', isExpanded.toString());

            if (isExpanded) {
                // Animar ítems del menú móvil al abrir
                const menuItems = mainNavList.querySelectorAll('.nav-menu-item');
                menuItems.forEach((item, index) => {
                    // Resetear estilo por si se abre y cierra rápido
                    item.style.animationDelay = '';
                    item.style.opacity = ''; // Asegurar que la animación CSS pueda empezar desde opacity 0
                    item.style.transform = ''; // Asegurar que la animación CSS pueda empezar desde transformX -20px

                    // Aplicar delay para efecto escalonado
                    // La animación 'fadeInSlideIn' se define en CSS y se aplica cuando .nav-menu tiene .is-active
                    item.style.animationDelay = `${index * 0.07}s`;
                });
            } else {
                // Si el menú se cierra, también se cierran todos los submenús.
                closeAllSubmenus();
                // Opcional: limpiar delays si es necesario, aunque la animación solo corre al añadir .is-active
                const menuItems = mainNavList.querySelectorAll('.nav-menu-item');
                menuItems.forEach(item => {
                    item.style.animationDelay = '';
                });
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
            previousActiveElement = document.activeElement;
            slidingPanel.classList.add('is-open');
            slidingPanel.setAttribute('aria-hidden', 'false');

            setTimeout(() => closeSlidingPanelButton.focus(), 50);

            // Animar elementos internos del panel
            const panelItems = slidingPanel.querySelectorAll('.sliding-panel-item');
            panelItems.forEach((item, index) => {
                item.style.animationDelay = ''; // Reset
                item.style.opacity = '';      // Reset
                item.style.transform = '';    // Reset
                item.style.animationDelay = `${0.1 + index * 0.07}s`; // Delay base + escalonado
            });

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
    // 4. Inicialización de Estados ARIA y Enlace Activo
    // ==========================================================================

    /**
     * Establece los atributos ARIA iniciales para los componentes interactivos.
     */
    function initializeAriaStates() {
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
    }

    /**
     * Identifica y marca el enlace de navegación activo basado en la URL actual.
     * Añade la clase '.is-active-link' al <li> padre del enlace activo.
     * Añade la clase '.has-active-child' a los <li> padres en la jerarquía si el activo es un submenú.
     */
    function setActiveLink() {
        const currentLocation = window.location.pathname; // Obtiene la ruta, ej. "/productos.html" o "/"
        const allLinks = document.querySelectorAll('.nav-menu-link, .nav-submenu-link');

        allLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;
            const listItem = link.closest('.nav-menu-item, .nav-submenu-item');

            if (listItem) {
                listItem.classList.remove('is-active-link', 'has-active-child');
            }

            // Considerar la página de inicio (index.html o /) de forma especial
            let isActive = false;
            if (currentLocation === linkPath) {
                isActive = true;
            } else if (linkPath === '/index.html' && currentLocation === '/') { // Caso para servidor que sirve index.html en /
                isActive = true;
            }


            if (isActive && listItem) {
                listItem.classList.add('is-active-link');
                link.setAttribute('aria-current', 'page'); // Marcar el enlace actual para accesibilidad

                // Propagar 'has-active-child' a los padres
                let parent = listItem.parentElement.closest('.has-submenu');
                while (parent) {
                    parent.classList.add('has-active-child');
                    // Si estamos en móvil y el submenú padre está colapsado, podríamos abrirlo
                    // if (window.innerWidth <= 768) {
                    //    const parentSubmenu = parent.querySelector('.nav-submenu, .nav-submenu-nested');
                    //    const parentToggle = parent.querySelector('.submenu-toggle');
                    //    if (parentSubmenu && parentToggle && !parentSubmenu.classList.contains('is-open')) {
                    //        parentSubmenu.classList.add('is-open');
                    //        parentToggle.classList.add('is-open');
                    //        parentToggle.setAttribute('aria-expanded', 'true');
                    //    }
                    // }
                    parent = parent.parentElement.closest('.has-submenu');
                }
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    // ==========================================================================
    // 5. Cierre de Submenús Móviles al Hacer Clic Fuera
    // ==========================================================================
    document.addEventListener('click', (event) => {
        if (window.innerWidth <= 768) { // Solo aplicar en vista móvil
            const openMobileMenu = mainNavList && mainNavList.classList.contains('is-active');
            if (!openMobileMenu) return; // No hacer nada si el menú principal móvil no está abierto

            const openSubmenus = document.querySelectorAll('.nav-submenu.is-open, .nav-submenu-nested.is-open');
            if (openSubmenus.length === 0) return; // No hay submenús abiertos

            // Verificar si el clic fue en un toggle de submenú o dentro de un submenú abierto
            const clickedOnToggle = event.target.closest('.submenu-toggle');
            const clickedInsideOpenSubmenu = event.target.closest('.nav-submenu.is-open, .nav-submenu-nested.is-open');

            if (!clickedOnToggle && !clickedInsideOpenSubmenu) {
                // Si el clic fue fuera de cualquier submenú abierto y no en un toggle, cerrar todos.
                // Esto es un comportamiento simple. Podríamos refinarlo para cerrar solo el específico si es necesario.
                closeAllSubmenus();
            }
        }
    });


    // Inicializaciones al cargar el DOM
    initializeAriaStates();
    setActiveLink();

    // Nota sobre submenús de escritorio:
    // La interactividad (hover/focus) de los submenús de escritorio se maneja principalmente con CSS.
    // Los atributos ARIA como `aria-expanded` son más cruciales para interacciones basadas en JS (clics),
    // que es el caso de los submenús en móvil. Si los submenús de escritorio también se activaran
    // por clic, se necesitaría una lógica similar a la de `allSubmenuToggles`.

    // ==========================================================================
    // 6. Funcionalidad de Cambio de Tema (Dark/Light Mode)
    // ==========================================================================
    const themeToggleButton = document.getElementById('themeToggleBtn');
    // Iconos dentro del botón de tema, si existen.
    const sunIcon = themeToggleButton ? themeToggleButton.querySelector('.theme-icon-sun') : null;
    const moonIcon = themeToggleButton ? themeToggleButton.querySelector('.theme-icon-moon') : null;
    // Podríamos añadir un span para anunciar cambios de tema a lectores de pantalla.
    // <span id="theme-status" class="sr-only" aria-live="polite"></span>

    /**
     * Aplica el tema especificado (light/dark) al documento y guarda la preferencia.
     * Actualiza el icono del botón de tema y su etiqueta ARIA.
     * @param {string} theme - El tema a aplicar ("light" o "dark").
     */
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // const themeStatus = document.getElementById('theme-status'); // Para anunciar el cambio

        if (theme === 'dark') {
            if (sunIcon) sunIcon.style.display = 'none';
            if (moonIcon) moonIcon.style.display = 'inline-block'; // Asegurar que sea visible
            if (themeToggleButton) themeToggleButton.setAttribute('aria-label', 'Activar tema claro');
            // if (themeStatus) themeStatus.textContent = "Tema oscuro activado.";
        } else {
            if (sunIcon) sunIcon.style.display = 'inline-block'; // Asegurar que sea visible
            if (moonIcon) moonIcon.style.display = 'none';
            if (themeToggleButton) themeToggleButton.setAttribute('aria-label', 'Activar tema oscuro');
            // if (themeStatus) themeStatus.textContent = "Tema claro activado.";
        }
    }

    /**
     * Carga el tema preferido desde localStorage o detecta la preferencia del sistema.
     */
    function loadTheme() {
        const storedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (storedTheme) {
            applyTheme(storedTheme);
        } else if (systemPrefersDark) {
            applyTheme('dark');
        } else {
            applyTheme('light'); // Default theme
        }
    }

    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }

    // Cargar tema al inicio
    loadTheme();

    // ==========================================================================
    // 7. Efecto Ripple en Clics
    // ==========================================================================
    function createRipple(event) {
        const button = event.currentTarget; // El elemento que disparó el evento

        // Crear el elemento span para el ripple
        const ripple = document.createElement("span");
        ripple.classList.add("ripple");

        // Calcular el tamaño del ripple (basado en el tamaño del botón)
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        // Calcular la posición del clic relativa al botón
        const rect = button.getBoundingClientRect();
        const rippleX = event.clientX - rect.left - radius;
        const rippleY = event.clientY - rect.top - radius;

        // Establecer el tamaño y la posición del ripple
        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${rippleX}px`;
        ripple.style.top = `${rippleY}px`;

        // Añadir el ripple al botón
        button.appendChild(ripple);

        // Eliminar el ripple después de que la animación termine
        ripple.addEventListener('animationend', () => {
            ripple.remove();
        });
    }

    const rippleElements = document.querySelectorAll(
        '.nav-menu-link, .nav-submenu-link, .sliding-panel ul li a, .theme-toggle-button, .nav-toggle, .submenu-toggle, .sliding-panel-close'
    );

    rippleElements.forEach(element => {
        // Asegurar que los elementos tengan position: relative y overflow: hidden si no lo tienen ya
        // (CSS se encarga de .nav-menu-link, .nav-submenu-link, .sliding-panel ul li a)
        // Los botones como theme-toggle-button, nav-toggle, etc., también necesitan estas propiedades
        // si no las tienen directamente.
        const computedStyle = getComputedStyle(element);
        if (computedStyle.position === 'static') {
            element.style.position = 'relative';
        }
        if (computedStyle.overflow !== 'hidden') {
            // No forzar overflow:hidden aquí si rompe el diseño de otros elementos (ej. el ::after de los links)
            // Es mejor manejarlo en CSS donde sea posible. .nav-menu-link ya lo tiene.
            // Los botones simples generalmente no tienen contenido que se desborde, por lo que el ripple
            // simplemente se superpondrá y se desvanecerá.
        }

        element.addEventListener('mousedown', createRipple);
    });

});
