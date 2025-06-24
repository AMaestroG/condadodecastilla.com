# condadodecastilla.com

## Misión

Promocionar el turismo de **Cerezo de Río Tirón** y proteger su patrimonio arqueológico y cultural.

Los colores predominantes son el morado principal, el oro viejo y el alabastro. Encontrarás la paleta completa en [docs/style-guide.md](docs/style-guide.md). Para referencia rápida puedes usar las variables `--color-primario-purpura` y `--color-secundario-dorado`.

## Estructura principal

- `assets/` – imágenes, estilos y scripts.
- `includes/` – fragmentos PHP reutilizables y conexión a la base de datos.
- `museo/` – páginas del museo y fichas de piezas.
- `foro/` – área de debate gestionada por cinco agentes expertos.
- `backend/` – API en Python (Flask) y herramientas de IA.
- `nuevaweb/` – rediseño experimental del sitio ([nuevaweb/README.md](nuevaweb/README.md)). Incluye un API Flask de demo (`flask_app.py`) independiente de la API principal.
- `docs/` – documentación completa.
- `scripts/` – utilidades de desarrollo y mantenimiento.
- `scripts_admin.php` – interfaz protegida para ejecutar esos scripts y revisar la salida.
- `tests/` – pruebas automáticas.
- `legacy/` – versiones antiguas conservadas por compatibilidad. La página principal anterior (`index.html`) ahora reside aquí y **`index.php`** es el punto de entrada actual.

## Controles del encabezado

La cabecera fija (`fragments/header.php`) incorpora accesos rápidos para el chat con IA y la selección de idioma. Cada botón emplea `data-menu-target` para abrir el panel correspondiente mediante `assets/js/sliding-menu.js`:

```html
<button id="open-unified-panel-button" data-menu-target="unified-panel"></button>
<button id="ai-chat-trigger" data-menu-target="ai-chat-panel"></button>
<button id="flag-toggle" data-menu-target="language-panel"></button>
```

Al pulsarlos, el script añade clases como `menu-open-left` o `menu-open-top` al `<body>` y muestra los paneles `#unified-panel`, `#ai-chat-panel` o `#language-panel`. Consulta [docs/index-guide.md](docs/index-guide.md) para más detalles.

## Tecnologías recomendadas

El proyecto emplea PHP y Python con Flask. Para nuevos módulos se aconseja usar los frameworks modernos listados en [docs/fullstack-tools-2025.md](docs/fullstack-tools-2025.md): React, Next.js, Vue 3, Svelte/SvelteKit, Astro, SolidJS, Vite y TailwindCSS en el frontend, junto con FastAPI o NestJS para la capa de servicios. Se sugiere Docker y Docker Compose para un entorno reproducible.

## Configuración del entorno

1. Copia el archivo `.env.example` a `.env`:

   ```bash
   cp .env.example .env
   ```

2. Sustituye los valores de ejemplo por tus credenciales locales (base de datos, claves de API y ajustes de depuración).
3. Opcionalmente define `HEADER_BANNER_URL` para personalizar la imagen del escudo que aparece en la cabecera.

### Solución de problemas de base de datos

Si la aplicación no puede conectarse a PostgreSQL:

1. Verifica que las variables de conexión en tu archivo `.env` sean correctas.
2. Ejecuta `scripts/check_db.sh` para comprobar la disponibilidad de la base de datos y detectar valores faltantes.
3. Asegúrate de que el servicio de PostgreSQL esté ejecutándose y accesible desde la dirección configurada.
4. Algunas pruebas necesitan la extensión **pdo_pgsql**; consulta [docs/testing.md](docs/testing.md) para instalarla en caso de errores de conexión.


## Instalación de dependencias

Antes de ejecutar el proyecto asegúrate de contar con las versiones adecuadas de PHP, Python y Node.js:

- **PHP 8.1** o superior
- **Python 3.10** o superior
- **Node.js 18** o superior

Una vez verificados los requisitos, instala las librerías principales ejecutando en la raíz del proyecto:

```bash
composer install
pip install -r requirements.txt
npm install
```

Con las dependencias listas, puedes lanzar las pruebas de PHP y de Python para comprobar que todo funciona:

```bash
vendor/bin/phpunit
python -m unittest discover -s tests
```


## Puesta en marcha rápida

1. Clona este repositorio y verifica que tengas **Docker** y **Docker Compose** instalados.
2. Ejecuta:

```bash
docker-compose up --build
```

Consulta el archivo [docker-compose.yml](docker-compose.yml) para conocer los servicios disponibles (`frontend`, `backend`, `nuevaweb_php`, `nuevaweb_flask` y `db`).
El frontend se sirve en `http://localhost:4321`, la API de Flask principal en `http://localhost:5000`, la web PHP de `nuevaweb` en `http://localhost:8082`, la API secundaria de Flask en `http://localhost:5002` y la base de datos PostgreSQL en `localhost:5432`.

## Ejecuci\u00f3n manual

Si no deseas usar Docker Compose puedes arrancar cada servicio por separado:

1. **API de Flask (raíz)**:
   ```bash
   pip install -r requirements.txt
   python flask_app.py
   ```
2. **Frontend** (Vite):
   ```bash
   cd frontend
   pnpm install
   pnpm run dev --host 0.0.0.0
   ```
3. **Web PHP de nuevaweb**:
   ```bash
   php -S localhost:8082 -t nuevaweb
   ```
4. **API de Flask de nuevaweb**:
   ```bash
   pip install flask
   python nuevaweb/flask_app.py
   ```
   Es un servicio de demostración que solo expone ``/api/hello`` y no sustituye a la API principal de Flask.
5. **PostgreSQL**:
   ```bash
   docker run --name condado_db -p 5432:5432 \
      -e POSTGRES_DB=condado_castilla_db \
      -e POSTGRES_USER=condado_user \
      -e POSTGRES_PASSWORD=condado_password \
      postgres:16
   ```

## Museo en Astro

Se añadió una página experimental en `frontend/astro-app` que usa **Astro** y **TailwindCSS** para mostrar las piezas del museo definidas en `museo/piezas.json`.
Para ejecutarla desde la raíz del proyecto basta con:

```bash
npm run dev:astro
```

Consulta [frontend/astro-app/README.md](frontend/astro-app/README.md) si prefieres lanzarla dentro de su carpeta. Allí se detallan también la ubicación del directorio de salida y otros apuntes.

Si modificas el código del foro es necesario regenerar sus archivos estáticos. Basta con ejecutar desde la raíz:

```bash
./scripts/build_forum.sh
```

El script compila el proyecto con Vite y deja la salida en `assets/forum-app/`.

La página principal se genera en `/piezas` y presenta las piezas en una cuadrícula adaptable.

## Demo Tailwind

El archivo `tailwind_index.php` es un prototipo que muestra la paleta morada y oro viejo con menús deslizantes. Para probarlo ejecuta desde la raíz:

```bash
php -S localhost:8000
```

Luego abre `http://localhost:8000/tailwind_index.php` en tu navegador.

## Mapa interactivo

Visita `lugares/mapa_interactivo.php` para explorar los principales monumentos y poblaciones sobre un mapa dinámico.

## Nueva web experimental

Encontraras un nuevo conjunto de paginas en `nuevaweb/` que reutilizan la paleta de colores y los menus deslizantes descritos en [docs/style-guide.md](docs/style-guide.md). Consulta [nuevaweb/README.md](nuevaweb/README.md) para mas informacion.

Para probar el sitio, inicia la API Flask y despues arranca la web PHP:

```bash
python flask_app.py
php -S localhost:8082 -t nuevaweb
```

Luego abre `http://localhost:8082/index.php` en tu navegador.

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

Para conocer las licencias de las fuentes Cinzel y Lora revisa [assets/fonts/README.md](assets/fonts/README.md)

## Transiciones de página

Se añadió un módulo de **transiciones de página** que aprovecha `GSAP` para animar la entrada y salida de contenido. Gracias a la suavidad de estos efectos, la visita resulta más atractiva y ayuda a cumplir la misión de *promocionar el turismo en Cerezo de Río Tirón y proteger su patrimonio arqueológico y cultural*.

Para empaquetar los scripts y estilos utiliza:

```bash
npm run build
```

El comando genera los archivos finales en `dist/` y `assets/css`. Consulta [docs/page-transitions.md](docs/page-transitions.md) para obtener todos los detalles de configuración.

## Testing

Sigue la [Guía de Testing](docs/testing.md) para preparar el entorno y ejecutar todas las pruebas. Los pasos básicos son:
1. Instala todas las dependencias ejecutando `./scripts/setup_environment.sh`. El script descarga los paquetes de **PHP**, **Node** y **Python** necesarios para la suite completa y deja listo el entorno para las pruebas de PHP con `phpunit`, las pruebas de Node con **Puppeteer** y las pruebas de Python. También puedes hacerlo manualmente:

   ```bash
    pip install -r requirements.txt
    npm install
    composer install
    ```

Antes de lanzar `npm run test`, asegúrate de instalar las dependencias de Node
ejecutando `npm install`. Esto descargará paquetes como `puppeteer` y
`playwright`, imprescindibles para las pruebas de interfaz.

   Estos pasos garantizan que `phpunit`, `puppeteer` y las librerías de Python estén disponibles.

2. Si la suite incluye pruebas de interfaz, arranca un servidor PHP con:
   ```bash
   php -S localhost:8080
   ```
> **Nota:** `npm run test` lanza `scripts/start_php_server.sh` para iniciar este servidor de forma temporal.
3. Ejecuta las pruebas de PHP:
   ```bash
   vendor/bin/phpunit
   ```
4. Ejecuta la batería completa de pruebas de Python:
   ```bash
   python -m unittest discover -s tests
   ```
5. Instala el paquete **Puppeteer** si aún no lo tienes. Puedes hacerlo con `npm install` o ejecutando `scripts/setup_environment.sh`. Las pruebas de Node dependen de esta librería, de modo que asegúrate de instalarla antes de lanzar:
   ```bash
   npm run test:puppeteer
   ```

### Ejecutar pytest

Si prefieres usar `pytest` en lugar de `unittest`, instala antes las dependencias de Python:

```bash
pip install -r requirements.txt
pytest
```

Los errores `ModuleNotFoundError` o `ImportError` suelen indicar que faltan paquetes. Comprueba que has instalado todo lo indicado en [requirements.txt](requirements.txt).

También se proporciona `scripts/run_tests.sh` para instalar `requirements.txt` y lanzar la suite de forma directa.

Para verificar la accesibilidad del sitio ejecuta `scripts/run_accessibility_audit.sh`. El script inicia un servidor PHP local, ejecuta **Lighthouse** sobre las páginas principales y guarda los informes HTML en `reports/accessibility/`. Tras la ejecución, abre esos archivos en tu navegador para revisar los resultados.

## Estilo de mensajes de commit

Utiliza títulos breves en modo imperativo que describan el cambio, p.ej. `Add GSAP page transitions with overlay and tests`. Si añades un cuerpo opcional, explica _qué_ y _por qué_. Consulta [docs/commit-style.md](docs/commit-style.md) para ver la guía completa.

Se incluye `.gitmessage.txt` como plantilla de ejemplo. Puedes activarla con `git config commit.template .gitmessage.txt` para redactar mensajes consistentes.

## Cómo contribuir

Para instalar las dependencias y preparar tu entorno ejecuta:

```bash
./scripts/setup_environment.sh
```

El script se encarga de lanzar `composer install`, `pip install -r requirements.txt` y `npm install`. Consulta [CONTRIBUTING.md](CONTRIBUTING.md) para detalles adicionales.
