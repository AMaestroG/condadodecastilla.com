# Lista de tareas

- [x] Implementar menús deslizantes que compriman el contenido lateral.
- [x] Aplicar paleta de morado y oro viejo según `docs/style-guide.md`.
- [x] Añadir degradados de alto contraste en títulos.
- [x] Crear un foro con cinco agentes expertos que dinamicen la comunidad.

## Nuevas tareas

- [x] Documentar el crawler y la base de datos de grafo de conocimiento.
- [x] Revisar la accesibilidad y el rendimiento en dispositivos móviles.
- [x] Integrar el modo Homonexus y el chat IA en todas las páginas.
- [x] Garantizar que los menús y botones permanezcan fijos al hacer scroll para que solo el contenido sea desplazable.
- [x] Documentar la nueva gráfica de Influencia Romana realizada con D3 y compatible con modo oscuro.
- [x] Añadir tests de `GraphDBInterface` para `add_or_update_resource`, `resource_exists` y `add_link`.
- [x] Añadir test de Puppeteer `linternaGradientTest.js` para verificar `bg-linterna-gradient` en la galería.
- [x] Auditar ARIA y documentar los breakpoints (480 px, 768 px, 992 px) del encabezado y los menús deslizantes.

## Notas

La auditoría de accesibilidad se centró en los roles ARIA de los botones y paneles de navegación.
Se confirmó que el encabezado y los menús deslizantes usan puntos de corte de **480 px**, **768 px** y **992 px**,
garantizando que su posición fija no obstaculice la navegación en móviles.
Tras preparar el entorno con `setup_environment.sh` (ver [script_catalog.md](script_catalog.md)) y servir la web con `php -S`, se
verificó que el botón `#consolidated-menu-button` permanece visible al hacer
scroll en pantallas de 768 px o menos.

## Tareas en curso

- [ ] Creacion del componente de foro en React.
- [ ] Extension de la API Flask para comentarios.
- [ ] Nuevas pruebas de `GraphDBInterface`.

### Nuevas tareas

- [ ] Documentar la extensión de la API Flask y la estructura del foro en React.

Todos los cambios deben mantener la paleta de morado y oro viejo sobre fondos de alabastro segun [docs/style-guide.md](style-guide.md).
