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
            document.body.classList.add('panel-open');

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
            document.body.classList.remove('panel-open');

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
    // 7. Efecto Ripple en Clics (movido más abajo para agrupar con otros event listeners)
    // ==========================================================================

    // ==========================================================================
    // 8. Barra de Comandos / Búsqueda Universal
    // ==========================================================================
    const commandPaletteOverlay = document.getElementById('commandPaletteOverlay');
    const commandPalette = document.getElementById('commandPalette');
    const commandPaletteSearchInput = document.getElementById('commandPaletteSearchInput');
    const commandPaletteResults = document.getElementById('commandPaletteResults');
    const commandPaletteCloseBtn = document.getElementById('commandPaletteCloseBtn');
    let searchableItems = [];
    let commandPalettePreviousActiveElement;
    let activeDescendantIndex = -1;

    function collectSearchableItems() {
        const items = [];
        // Recolectar de Menú Principal y Submenús
        document.querySelectorAll('.nav-menu-link, .nav-submenu-link, .mega-menu-list li a').forEach(link => {
            if (!link.closest('.theme-toggle-item') && link.id !== 'openSlidingPanel') { // Excluir items no navegables
                const text = link.textContent.trim();
                const href = link.getAttribute('href');
                let category = "Navegación";
                const parentMenu = link.closest('.has-megamenu') ? link.closest('.has-megamenu').querySelector('.nav-menu-link') :
                                 link.closest('.nav-submenu') ? link.closest('.nav-submenu').previousElementSibling.previousElementSibling :
                                 link.closest('.nav-submenu-nested') ? link.closest('.nav-submenu-nested').previousElementSibling.previousElementSibling :
                                 null;
                if (parentMenu && parentMenu.textContent.trim()) {
                    category = parentMenu.textContent.trim();
                } else if (link.classList.contains('nav-menu-link')) {
                     category = "Principal";
                }
                if (text && href && href !== '#') {
                    items.push({ text, href, category });
                }
            }
        });
        // Recolectar de Panel Deslizante
        document.querySelectorAll('.sliding-panel ul li a').forEach(link => {
            const text = link.textContent.trim();
            const href = link.getAttribute('href');
            if (text && href && href !== '#') {
                items.push({ text, href, category: "Panel de Usuario" });
            }
        });
        // Añadir acciones como "Cambiar Tema"
        if (themeToggleButton) {
            items.push({ text: "Cambiar Tema (Claro/Oscuro)", action: 'toggleTheme', category: "Acciones" });
        }
        // Añadir acción para abrir panel de perfil
        if (openSlidingPanelButton){
            items.push({ text: "Abrir Panel de Perfil", action: 'openProfilePanel', category: "Acciones" });
        }

        searchableItems = items;
    }

    function renderSearchResults(results) {
        commandPaletteResults.innerHTML = ''; // Limpiar resultados anteriores
        activeDescendantIndex = -1; // Resetear índice de descendiente activo
        commandPaletteSearchInput.removeAttribute('aria-activedescendant');

        if (results.length === 0) {
            commandPaletteResults.innerHTML = '<p class="command-palette-no-results">No se encontraron resultados.</p>';
            return;
        }

        results.forEach((item, index) => {
            const resultItem = document.createElement('a'); // Usar <a> para que sea navegable por defecto
            resultItem.classList.add('command-palette-result-item');
            resultItem.href = item.href || '#'; // Usar href si existe, sino '#' para acciones
            resultItem.setAttribute('role', 'option');
            resultItem.id = `command-result-item-${index}`;

            let iconSVG = '';
            // Asignar SVG basado en categoría o tipo de ítem
            // Estos son SVGs simplificados, idealmente se usaría un set consistente.
            // Width/height se controlarán por CSS: .command-palette-result-item .result-icon svg
            switch (item.category) {
                case "Acciones":
                    if (item.action === 'toggleTheme') {
                        // Usar un icono específico para tema si se desea, o el genérico de acción.
                        // Por ahora, el genérico de acción.
                        iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'; // Rayo (Acción)
                    } else {
                        iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'; // Rayo (Acción)
                    }
                    break;
                case "Panel de Usuario":
                    iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>'; // Usuario
                    break;
                case "Principal": // Para items del menú principal
                    // Podríamos tener un mapeo más específico si los textos son fijos
                    if (item.text.toLowerCase().includes("inicio")) {
                        iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>'; // Casa
                    } else if (item.text.toLowerCase().includes("productos")) {
                        iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 7.28V20H3V7.28L12 3l9 4.28M12 5.47L5 9.05v9.07h14V9.05L12 5.47m-2 9.05h4v-2h-4v2m-4-3h12v-2H6v2z"/></svg>'; // Caja
                    } else { // Default para otros principales
                        iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>'; // Documento
                    }
                    break;
                default: // Navegación general, submenús
                    iconSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>'; // Documento
            }

            resultItem.innerHTML = `
                <span class="result-icon">${iconSVG}</span>
                <span class="result-text">${item.text}</span>
                <span class="result-category">${item.category}</span>
            `;

            resultItem.addEventListener('click', (e) => {
                if (item.action) {
                    e.preventDefault(); // Prevenir navegación para acciones
                    if (item.action === 'toggleTheme' && themeToggleButton) {
                        themeToggleButton.click();
                    } else if (item.action === 'openProfilePanel' && openSlidingPanelButton) {
                        openSlidingPanelButton.click();
                    }
                }
                // Para enlaces href, la navegación es automática
                closeCommandPalette();
            });
            commandPaletteResults.appendChild(resultItem);
        });
    }

    function handleCommandPaletteSearch() {
        const searchTerm = commandPaletteSearchInput.value.toLowerCase().trim();
        if (!searchTerm) {
            renderSearchResults(searchableItems.slice(0, 8)); // Mostrar todos o algunos por defecto
            return;
        }
        const filteredItems = searchableItems.filter(item =>
            item.text.toLowerCase().includes(searchTerm) ||
            item.category.toLowerCase().includes(searchTerm)
        );
        renderSearchResults(filteredItems);
    }

    function openCommandPalette() {
        if (!commandPaletteOverlay) return;
        commandPalettePreviousActiveElement = document.activeElement;
        commandPaletteOverlay.classList.add('is-open');
        commandPaletteOverlay.setAttribute('aria-hidden', 'false');
        commandPalette.setAttribute('aria-hidden', 'false');
        commandPaletteSearchInput.value = '';
        handleCommandPaletteSearch(); // Renderizar inicial (todos o por defecto)
        commandPaletteSearchInput.focus();
        document.addEventListener('keydown', handleCommandPaletteKeyDown);
    }

    function closeCommandPalette() {
        if (!commandPaletteOverlay) return;
        commandPaletteOverlay.classList.remove('is-open');
        commandPaletteOverlay.setAttribute('aria-hidden', 'true');
        commandPalette.setAttribute('aria-hidden', 'true');
        commandPaletteSearchInput.removeAttribute('aria-activedescendant');
        if (commandPalettePreviousActiveElement) {
            commandPalettePreviousActiveElement.focus();
        }
        document.removeEventListener('keydown', handleCommandPaletteKeyDown);
    }

    function handleCommandPaletteKeyDown(event) {
        if (event.key === 'Escape') {
            closeCommandPalette();
            return;
        }

        const resultsItems = commandPaletteResults.querySelectorAll('.command-palette-result-item');
        if (resultsItems.length === 0) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeDescendantIndex = (activeDescendantIndex + 1) % resultsItems.length;
            updateActiveDescendant(resultsItems);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeDescendantIndex = (activeDescendantIndex - 1 + resultsItems.length) % resultsItems.length;
            updateActiveDescendant(resultsItems);
        } else if (event.key === 'Enter' && activeDescendantIndex >= 0) {
            event.preventDefault();
            resultsItems[activeDescendantIndex].click(); // Simular clic en el elemento activo
        }
    }

    function updateActiveDescendant(items) {
        items.forEach((item, index) => {
            if (index === activeDescendantIndex) {
                item.classList.add('is-active-descendant');
                commandPaletteSearchInput.setAttribute('aria-activedescendant', item.id);
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('is-active-descendant');
            }
        });
    }

    if (commandPaletteSearchInput) {
        commandPaletteSearchInput.addEventListener('input', handleCommandPaletteSearch);
        // También manejar keydown en el input para navegación si no está ya cubierto por el listener global
        commandPaletteSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                 // El listener global handleCommandPaletteKeyDown ya se encarga de esto
                 // si el foco está en el input o dentro de la paleta.
                 // Si no, podríamos llamar handleCommandPaletteKeyDown(e) aquí explícitamente.
            }
        });
    }
    if (commandPaletteCloseBtn) {
        commandPaletteCloseBtn.addEventListener('click', closeCommandPalette);
    }
    if (commandPaletteOverlay) {
        commandPaletteOverlay.addEventListener('click', (event) => {
            if (event.target === commandPaletteOverlay) { // Solo si se hace clic en el overlay mismo
                closeCommandPalette();
            }
        });
    }

    // Atajo para abrir la paleta
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
            event.preventDefault();
            if (commandPaletteOverlay.classList.contains('is-open')) {
                closeCommandPalette();
            } else {
                openCommandPalette();
            }
        }
    });

    // Recolectar items buscables al inicio
    collectSearchableItems();


    // ==========================================================================
    // Efecto Ripple en Clics (Reubicado)
    // ==========================================================================
    function createRipple(event) {
        // Respetar preferencia de movimiento reducido
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const element = event.currentTarget;

        const ripple = document.createElement("span");
        ripple.classList.add("ripple");

        const diameter = Math.max(element.clientWidth, element.clientHeight);
        const radius = diameter / 2;

        const rect = element.getBoundingClientRect();
        const rippleX = event.clientX - rect.left - radius;
        const rippleY = event.clientY - rect.top - radius;

        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${rippleX}px`;
        ripple.style.top = `${rippleY}px`;

        element.appendChild(ripple);

        ripple.addEventListener('animationend', () => {
            ripple.remove();
        });
    }

    const rippleElements = document.querySelectorAll(
        '.nav-menu-link, .nav-submenu-link, .mega-menu-list li a, .sliding-panel ul li a, .theme-toggle-button, .nav-toggle, .submenu-toggle, .sliding-panel-close, .command-palette-close'
    );

    rippleElements.forEach(element => {
        const computedStyle = getComputedStyle(element);
        if (computedStyle.position === 'static') {
            // Solo aplicar position relative si es necesario y no lo tiene ya un ancestro.
            // Para botones y links simples, suele ser seguro.
            // Para elementos complejos, verificar si esto rompe el layout.
            // Por ahora, lo aplicaremos directamente.
            element.style.position = 'relative';
        }
         // overflow: hidden; es mejor manejarlo en CSS para los elementos que lo necesiten específicamente (como links).
         // Los botones no suelen necesitarlo para el ripple.
        element.addEventListener('mousedown', createRipple);
    });

});
