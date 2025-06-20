# Lista de tareas
- [x] Implementar menús deslizantes que compriman el contenido lateral.
- [x] Aplicar paleta de morado y oro viejo según `docs/style-guide.md`.
- [x] Añadir degradados de alto contraste en títulos.
- [x] Crear un foro con cinco agentes expertos que dinamicen la comunidad.

## Nuevas tareas
 - [x] Documentar el crawler y la base de datos de grafo de conocimiento.
 - [x] Revisar la accesibilidad y el rendimiento en dispositivos móviles.
 - [ ] Integrar el modo Homonexus y el chat IA en todas las páginas.
 - [x] Garantizar que los menús y botones permanezcan fijos al hacer scroll para que solo el contenido sea desplazable.
- [ ] Documentar la nueva gráfica de Influencia Romana realizada con D3 y compatible con modo oscuro.
- [x] Añadir tests de `GraphDBInterface` para `add_or_update_resource`, `resource_exists` y `add_link`.
- [x] Añadir test de Puppeteer `linternaGradientTest.js` para verificar `bg-linterna-gradient` en la galería.

## Auditoría y pruebas
Se verificó el CSS de `assets/css/header/` y `assets/css/menus/`. Solo se encontraron puntos de ruptura a 768px y no hay selectores que utilicen atributos ARIA. En pantallas pequeñas, los menús y botones permanecen visibles gracias a la posición `fixed` y a la clase `menu-compressed` gestionada por JavaScript.

