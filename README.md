# condadodecastilla.com

## Misión

Promocionar el turismo de **Cerezo de Río Tirón** y proteger su patrimonio arqueológico y cultural.

## Estructura principal

- `assets/` – imágenes, estilos y scripts.
- `includes/` – fragmentos PHP reutilizables y conexión a la base de datos.
- `museo/` – páginas del museo y fichas de piezas.
- `foro/` – área de debate gestionada por cinco agentes expertos.
- `backend/` – API en Python (Flask) y herramientas de IA.
- `docs/` – documentación completa.
- `scripts/` – utilidades de desarrollo y mantenimiento.
- `scripts_admin.php` – interfaz protegida para ejecutar esos scripts y revisar la salida.
- `tests/` – pruebas automáticas.

## Tecnologías recomendadas

El proyecto emplea PHP y Python con Flask. Para nuevos módulos se aconseja usar los frameworks modernos listados en [docs/fullstack-tools-2025.md](docs/fullstack-tools-2025.md): React, Next.js, Vue 3, Svelte/SvelteKit, Astro, SolidJS, Vite y TailwindCSS en el frontend, junto con FastAPI o NestJS para la capa de servicios. Se sugiere Docker y Docker Compose para un entorno reproducible.

## Puesta en marcha rápida

1. Clona este repositorio y verifica que tengas **Docker** y **Docker Compose** instalados.
2. Ejecuta:

```bash
docker-compose up --build
```

Consulta el archivo [docker-compose.yml](docker-compose.yml) para conocer los servicios disponibles.

## Museo en Astro

Se añadió una página experimental en `frontend/astro-app` que usa **Astro** y **TailwindCSS** para mostrar las piezas del museo definidas en `museo/piezas.json`.
Para ejecutarla:

```bash
npm install
npm run dev:astro
```

La página principal se genera en `/piezas` y presenta las piezas en una cuadrícula adaptable.


## Mapa interactivo

Visita `lugares/mapa_interactivo.php` para explorar los principales monumentos y poblaciones sobre un mapa dinámico.

## Actualización diaria del grafo de conocimiento

El script `scripts/daily_agent.py` se ejecuta cada noche mediante GitHub Actions para revisar y actualizar `knowledge_graph_db.json`.

Para detalles sobre la paleta de colores y la tipografía consulta [docs/style-guide.md](docs/style-guide.md), en especial las líneas 1‑24 que enumeran todas las variables CSS.
