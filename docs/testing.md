# Guía de Testing

Esta guía explica cómo preparar el entorno y ejecutar las pruebas automatizadas del proyecto.

## Configuración inicial

Asegúrate de instalar las dependencias básicas antes de ejecutar las pruebas:

```bash
pip install -r requirements.txt   # instala Flask, filelock y demás paquetes de Python
npm ci                             # instala Puppeteer y el resto de dependencias de Node
composer install                   # genera `vendor/bin/phpunit`
```

Si por algún motivo no se crea el binario de PHPUnit, ejecuta:

```bash
composer require --dev phpunit/phpunit
```

## Paso a paso

1. **Prepara el entorno de desarrollo**. Puedes ejecutar `scripts/setup_environment.sh` para instalar todo de forma automática o instalar cada grupo de dependencias manualmente:

   ```bash
   pip install -r requirements.txt
   npm install
   composer install
   ```

   Esto asegura que las librerías de Python, las dependencias de Node (incluido `puppeteer`) y `phpunit` estén disponibles.

2. **Compila los estilos de CSS**. El script `npm run build` ahora ejecuta PostCSS con Autoprefixer tras compilar Tailwind y Sass:

   ```bash
   npm run build
   ```

   Esto generará `assets/css/custom.css` con los prefijos necesarios para los navegadores soportados.

3. Si vas a ejecutar la suite de interfaz, abre otro terminal y arranca un servidor PHP local:

```bash
php -S localhost:8080
```

4. Ejecuta las pruebas de PHP:

```bash
vendor/bin/phpunit
```

5. Ejecuta las pruebas de Python:
   - Solo la suite de la interfaz de grafo:

```bash
python -m unittest tests/test_graph_db_interface.py
```

   - Comprobar que todas las traducciones contienen las mismas claves:

```bash
python -m unittest tests/test_translation_keys.py
```

- Toda la batería de pruebas de Python:

```bash
python -m unittest discover -s tests
```

6. Ejecuta las pruebas de interfaz con Puppeteer:

```bash
npm run test:puppeteer
```

7. Ejecuta la batería de Playwright en los navegadores Chromium, Firefox y WebKit:

```bash
npm run test:playwright
```

### Requisitos adicionales de PHP

Las pruebas de PHP necesitan la interfaz **php-cgi** y la extensión
**pdo_pgsql** para acceder a la base de datos de pruebas. Instálalas con:

```bash
sudo apt-get install php-cgi php-pgsql php-xml
```

Además asegúrate de que un servidor de PostgreSQL esté disponible y las
credenciales se definan en `.env` antes de lanzar PHPUnit.

### Dependencias de Node para Puppeteer

Antes de lanzar la suite de interfaz instala las dependencias de Node. Ejecuta:

```bash
npm install
```

o bien lanza `scripts/setup_environment.sh`, que realiza la misma instalación.

### Auditorías de accesibilidad

Consulta las opciones del script en [script_catalog.md](script_catalog.md).

```bash
./scripts/run_accessibility_audit.sh
```

### Dependencias de Python sin conexión

Si necesitas ejecutar las pruebas en un entorno sin acceso a Internet, descarga
los paquetes indicados en `requirements.txt` y guárdalos en el directorio
`vendor/python-deps/`:

```bash
pip download -d vendor/python-deps -r requirements.txt
```

Al lanzar `setup_environment.sh` se detectará el contenido de esa carpeta y se
ejecutará `pip install --no-index --find-links vendor/python-deps -r
requirements.txt` para instalar desde esos archivos locales.

## Solucion de problemas

- Si al ejecutar `npm run test:puppeteer` obtienes un TimeoutError, comprueba que tienes un servidor PHP en marcha con:

```bash
php -S localhost:8080
```

- Si las dependencias fallaron al instalarse, consulta [script_catalog.md](script_catalog.md) para volver a ejecutar el script de preparación.
- Si Puppeteer informa que no se encuentra Chromium, ejecuta `npm install` para reinstalar las dependencias.

## Resultados del 20/06/2025

- `vendor/bin/phpunit`: 34 tests executed with 3 errors and 19 failures. Tras instalar `php-cgi` y `pdo_pgsql` las pruebas que requieren conexión a PostgreSQL siguieron fallando porque la base de datos no estaba disponible.

- `python -m unittest`: todas las pruebas pasaron.

- `npm run test:puppeteer`: la suite falló por un tiempo de espera mientras esperaba `#google_translate_element`.

Consulta la sección [Solucion de problemas](#solucion-de-problemas) para intentar corregir estos fallos.
