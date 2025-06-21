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

## Configuración del entorno

1. Copia el archivo `.env.example` a `.env`:

   ```bash
   cp .env.example .env
   ```

2. Sustituye los valores de ejemplo por tus credenciales locales (base de datos, claves de API y ajustes de depuración).


## Puesta en marcha rápida

1. Clona este repositorio y verifica que tengas **Docker** y **Docker Compose** instalados.
2. Ejecuta:

```bash
docker-compose up --build
```

Consulta el archivo [docker-compose.yml](docker-compose.yml) para conocer los servicios disponibles (`frontend`, `backend` y `db`).
El frontend se sirve en `http://localhost:4321`, la API de Flask en `http://localhost:5000` y la base de datos PostgreSQL en `localhost:5432`.

## Museo en Astro

Se añadió una página experimental en `frontend/astro-app` que usa **Astro** y **TailwindCSS** para mostrar las piezas del museo definidas en `museo/piezas.json`.
Para ejecutarla desde la raíz del proyecto basta con:

```bash
npm run dev:astro
```

Consulta [frontend/astro-app/README.md](frontend/astro-app/README.md) si prefieres lanzarla dentro de su carpeta. Allí se detallan también la ubicación del directorio de salida y otros apuntes.

La página principal se genera en `/piezas` y presenta las piezas en una cuadrícula adaptable.

## Mapa interactivo

Visita `lugares/mapa_interactivo.php` para explorar los principales monumentos y poblaciones sobre un mapa dinámico.

### Forum Application
See [docs/frontend-structure.md](docs/frontend-structure.md) for details. To build:

```bash
cd frontend/forum-app
npm install
npm run build
```

## Actualización diaria del grafo de conocimiento

El script `scripts/daily_agent.py` se ejecuta cada noche mediante GitHub Actions para revisar y actualizar `knowledge_graph_db.json`.

Para detalles sobre la paleta de colores y la tipografía consulta [docs/style-guide.md](docs/style-guide.md), en especial las líneas 1‑24 que enumeran todas las variables CSS.

## Testing

Sigue la [Guía de Testing](docs/testing.md) para preparar el entorno y ejecutar todas las pruebas. Los pasos básicos son:

1. Instala las dependencias con [`scripts/setup_environment.sh`](scripts/setup_environment.sh).
2. Si la suite incluye pruebas de interfaz, arranca un servidor PHP con:
   ```bash
   php -S localhost:8080
   ```
3. Ejecuta las pruebas de PHP:
   ```bash
   vendor/bin/phpunit
   ```
4. Ejecuta la batería completa de pruebas de Python:
   ```bash
   python -m unittest discover -s tests
   ```
5. Ejecuta las pruebas de Node (Puppeteer):
   ```bash
   npm run test:puppeteer
   ```

También se proporciona `scripts/run_tests.sh` para instalar `requirements.txt` y lanzar la suite de forma directa.
