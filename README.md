# condadodecastilla.com

Este repositorio contiene el código del sitio web del proyecto **Condado de Castilla**.

## Misión

Promocionar el turismo en **Cerezo de Río Tirón** y gestionar de forma activa su patrimonio arqueológico y cultural.
## Documentación y Guías
- [Documentación detallada](docs/README.md)
- [Guía de Estilo](docs/style-guide.md)
- [Guía de index.php y menús](docs/index-guide.md)

## Respuestas generadas por IA
Este repositorio puede mostrar textos generados automáticamente. Estos contenidos son experimentales y pueden contener errores. Para más información, consulta la [política de uso responsable](docs/responsible-ai.md).


## Características de diseño

- Paleta en tonos púrpura y oro viejo sobre fondos de alabastro.
- Menús deslizantes que comprimen el contenido al abrirse.
- Menús fijos en la parte superior gracias a la variable `--menu-top-offset`.
- Textos con degradados de alto contraste.
- Paleta que cambia automáticamente según la hora del visitante (amanecer, mediodía, atardecer o noche) con opción manual.
- Foro con cinco agentes expertos para dinamizar la comunidad.
- El script `assets/js/audio-controller.js` atenúa el volumen de los elementos `<audio>` y `<video>` cuando cualquier menú deslizante está activo. Escucha el evento `menu-toggled` que dispara `assets/js/main.js` al abrir o cerrar un menú.

### Modo luna

Una variante oscura que reduce la luminosidad general. Pulsa el botón `moon-toggle`
para activar el modo luna; el script añadirá la clase `luna` al `<body>` y guardará
el valor `"moon"` en `localStorage`. Vuelve a pulsar para restaurar la apariencia
clara.

### Agentes del foro

Cinco perfiles virtuales dinamizan las conversaciones y ayudan a resolver dudas:

- **Alicia la Historiadora** – especialista en la historia de Castilla y de Cerezo de Río Tirón.
- **Bruno el Arqueólogo** – explora y protege el patrimonio arqueológico.
- **Clara la Guía Turística** – orienta a los visitantes en sus recorridos.
- **Diego el Gestor Cultural** – coordina eventos y dinamiza la cultura local.
- **Elena la Tecnóloga** – aplica innovación tecnológica al servicio del patrimonio.

Puedes ajustar sus biografías o añadir nuevos perfiles modificando el archivo `config/forum_agents.php`.

#### Estructura del archivo

```php
return [
    'historian' => [
        'name' => 'Alicia la Historiadora',
        'bio' => 'Con años de investigación tras ella, Alicia relata... ',
        'expertise' => 'Historia medieval y orígenes de Castilla'
    ],
    // ...
];
```

Consulta la [guía de index.php](docs/index-guide.md#configuracion-de-los-agentes-del-foro) para más detalles sobre cómo actualizar estos perfiles.

## Requisitos

- **PHP** 7.4 o superior con soporte CLI y la extensión PDO habilitada.
- Extensiones PHP recomendadas: `pdo_pgsql`, `mbstring`, `xml` y `xmlwriter`.
- **Composer** 2.x para gestionar las dependencias.
- **PostgreSQL** 9.6 o superior.
- **PHPUnit** se instala como dependencia de desarrollo con Composer.

### Instalación rápida en Linux

En distribuciones basadas en Debian puedes instalar PHP y Composer junto con las extensiones necesarias ejecutando:

```bash
sudo apt update
sudo apt install php php-cli php-xml php-xmlwriter php-mbstring php-pgsql composer
```

## Configuración de la base de datos

`includes/db_connect.php` obtiene los parámetros de conexión desde variables de entorno.
No modifiques ese archivo directamente. Copia primero `.env.example` a `.env` y
define ahí tus credenciales:

```bash
cp .env.example .env
DB_HOST="localhost"
DB_NAME="condado_castilla_db"
DB_USER="condado_user"
CONDADO_DB_PASSWORD="tu_contraseña_muy_segura"
DB_PORT="5432"
```

El script leerá automáticamente esas variables cuando se incluya en tus páginas PHP.

El script obtiene la contraseña real desde la variable de entorno `CONDADO_DB_PASSWORD`.
Si dicha variable no está definida, el sitio seguirá funcionando con los textos por defecto
y se registrará un aviso en el log del servidor.
Para preparar la base de datos ejecuta en orden los scripts de `database_setup`: `01_create_tables.sql`, `02_create_museo_piezas_table.sql` y `03_create_tienda_productos.sql`.
Si quieres cargar algunos ejemplos iniciales para el museo, puedes lanzar de forma opcional `04_insert_sample_museo_piezas.sql` tras crear la tabla correspondiente.

## Comprobación de la base de datos

Para verificar que PostgreSQL está disponible y que la variable `CONDADO_DB_PASSWORD` se ha definido, ejecuta:

```bash
export CONDADO_DB_PASSWORD="tu_contrasena"
./scripts/check_db.sh
```

El script usa `pg_isready` para comprobar la conexión y mostrará un mensaje de éxito o error.


## Servidor de desarrollo

Para probar el sitio de forma local puedes usar el servidor embebido de PHP. Sitúate en la raíz del repositorio y ejecuta:

```bash
php -S localhost:8000
```

Esto iniciará el servidor en `http://localhost:8000`.

### Puesta en marcha de la API Flask

1. (Opcional) crea y activa un entorno virtual:

```bash
python3 -m venv venv
source venv/bin/activate
```

2. Instala las dependencias definidas en `requirements.txt`:

```bash
pip install -r requirements.txt
```

3. Copia `.env.example` a `.env` y define al menos `GEMINI_API_KEY` y `CONDADO_DB_PASSWORD`.
4. Carga dichas variables en tu terminal:
   ```bash
   set -a
   source .env
   set +a
   ```
5. Inicia el servicio:

```bash
python flask_app.py
```

Esto iniciará la API en `http://localhost:5000`, que puede ejecutarse en paralelo al servidor PHP.

### Variable de entorno `FLASK_DEBUG`

Establece `FLASK_DEBUG=1` para arrancar la API en modo depuración:

```bash
export FLASK_DEBUG=1
python flask_app.py
```

Si no defines la variable, `flask_app.py` ejecutará `app.run(debug=False)` por defecto.

## Instalación de dependencias

Asegúrate primero de tener instalado **PHP CLI** y **Composer**. En sistemas basados en Debian puedes usar:

```bash
sudo apt-get install php-cli composer
```

Una vez clonado el repositorio instala todas las dependencias de PHP y descarga las
bibliotecas de JavaScript necesarias ejecutando:

```bash
composer install --ignore-platform-req=ext-dom --ignore-platform-req=ext-xmlwriter --ignore-platform-req=ext-xml
./scripts/setup_frontend_libs.sh
```

`composer install` descargará todas las librerías de PHP necesarias, incluido **PHPUnit**, que quedará disponible en `vendor/bin/phpunit`.

Este proyecto utiliza la librería **league/commonmark** para transformar a HTML los archivos Markdown del blog. La dependencia se instala automáticamente con el comando anterior.

El script descarga las bibliotecas **jQuery**, **Bootstrap** y **Tailwind CSS**
en `assets/vendor`. Tras ello genera la hoja `assets/vendor/css/tailwind.min.css`
si ejecutas:

Debes lanzar este comando al instalar el proyecto y cada vez que modifiques
`tailwind.config.js` o `assets/css/tailwind_base.css` para que los cambios de
diseño se reflejen en la hoja final:

```bash
npx tailwindcss@3.4.4 -i assets/css/tailwind_base.css -o assets/vendor/css/tailwind.min.css --minify
```

### Dependencias de npm

Ejecuta `npm install` para descargar las bibliotecas declaradas en `package.json` (por ejemplo **Puppeteer** y **Tailwind CSS**).

#### Requisito de Node.js

Se necesita **Node.js 18** o superior. Las dependencias se enumeran en `package.json` y quedan bloqueadas en `package-lock.json`.

Asegúrate de que la herramienta `npm` esté disponible en tu sistema antes de lanzar `npx tailwindcss` o `npm test`.
El comando `npm test` ejecuta la batería de pruebas automatizadas con Puppeteer.

A continuación copia el archivo de ejemplo `.env.example` a `.env` y
rellena los valores reales que utilizará el proyecto. Este paso es
imprescindible **antes** de poner en marcha el sitio o ejecutar las
pruebas para que existan `CONDADO_DB_PASSWORD`, `GEMINI_API_KEY` y
cualquier otra variable necesaria.

### Carga de variables de entorno

`includes/env_loader.php` emplea la biblioteca
`vlucas/phpdotenv` para cargar automáticamente el fichero `.env` cada vez
que se incluye en un script PHP. De esta forma páginas como
`includes/db_connect.php` o `includes/ai_utils.php` disponen de las
variables sin que tengas que exportarlas manualmente.

El servicio Flask lee estas mismas variables mediante
`os.getenv()` en `flask_app.py`. Para que estén disponibles debes
exportarlas en tu terminal antes de ejecutar la API, por ejemplo:

```bash
set -a
source .env
set +a
python flask_app.py
```

Si `GEMINI_API_KEY` no está definida, las funciones de
`includes/ai_utils.php` activarán un simulador de respuestas y el sitio
seguirá funcionando sin realizar llamadas reales al servicio de IA.

## Configuración de la API de Gemini

Para que las funcionalidades de IA puedan comunicarse con el servicio externo necesitas definir dos variables en el archivo `.env`:

```bash
GEMINI_API_KEY=tu_clave_personal
GEMINI_API_ENDPOINT=https://api.gemini.example.com/v1/generateContent
```

Variables requeridas:

- **`GEMINI_API_KEY`** – clave de acceso proporcionada por el proveedor.
- **`GEMINI_API_ENDPOINT`** – URL del punto de entrada (opcional; si se omite se
  usa `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent`).
- **`FORUM_COMMENT_COOLDOWN`** – tiempo de espera en segundos para que un usuario pueda volver a publicar en el foro (consulta `.env.example` donde el valor predeterminado es `60`).

Asegúrate de exportarlas en tu terminal o definirlas en `.env` para que
`includes/ai_utils.php` pueda realizar llamadas reales.

`GEMINI_API_KEY` guarda la clave de acceso suministrada por el proveedor. `GEMINI_API_ENDPOINT` permite especificar la URL del punto de entrada, que por defecto es `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent` si la variable se omite.

La clave **solo** se utiliza en el servidor a través de `includes/ai_utils.php`; ya no se expone en el marcado HTML. De este modo se evita compartir credenciales sensibles con el cliente.

Si `GEMINI_API_KEY` no está definida, las funciones de `includes/ai_utils.php` no intentarán contactar con el servicio real: en su lugar usarán un simulador interno que genera respuestas de ejemplo. Esto permite probar el sitio sin consumir cuota ni requerir acceso externo.

## Testing

**Es obligatorio ejecutar los siguientes comandos antes de cualquier suite de pruebas:**
Asegúrate de tener **Composer** y **PHPUnit** instalados en tu sistema antes de ejecutar las pruebas. `PHPUnit` puede instalarse de manera global o utilizar el binario que se genera en `vendor/bin/phpunit` tras ejecutar `composer install`.
Instala **todas** las dependencias antes de lanzar las suites de pruebas. Las pruebas dependen de PHP, Composer y las extensiones indicadas, por lo que no se ejecutarán si alguno de estos componentes falta. En particular es imprescindible tener disponible la interfaz de línea de comandos de PHP (PHP CLI). Ejecuta primero:

```bash
composer install
pip install -r requirements.txt
npm install
```

`composer install` debe ejecutarse **antes** de usar `vendor/bin/phpunit` y
`pip install -r requirements.txt` debe lanzarse **antes** de ejecutar las
pruebas de Python. Comprueba que PHP CLI está disponible antes de ejecutar las
pruebas de PHP:

```bash
php -v
```

Con las dependencias ya instaladas, ejecuta cada conjunto de tests de forma explícita:

```bash
vendor/bin/phpunit
python -m unittest tests/test_flask_api.py
# Asegúrate de tener un servidor local en marcha (por ejemplo `php -S localhost:8080`)
npm run test:puppeteer
node tests/moonToggleTest.js
```

`node tests/moonToggleTest.js` verifica que el botón `moon-toggle` añada la clase
`luna` y guarde `"moon"` en `localStorage`.

`vendor/bin/phpunit` lanza la suite de PHP definida en `phpunit.xml`.
`python -m unittest tests/test_flask_api.py` ejecuta el conjunto de pruebas de Python sobre la API Flask.
`npm run test:puppeteer` ejecuta las pruebas de interfaz con Puppeteer.

Si cualquiera de estos comandos devuelve un error de "command not found" lo más probable es que **PHP**, **Composer** o **PHPUnit** no estén instalados correctamente.

Además se proporcionan scripts auxiliares para validar el estado del código:

- `./check_links.sh` revisa enlaces rotos en las páginas principales.
- `./check_links_extended.sh` amplía la comprobación a los fragmentos HTML.
- `./scripts/check_alt_texts.sh` detecta imágenes sin atributo `alt`.

## Elementos del menú principal

El archivo `fragments/menus/main-menu.php` define los enlaces de navegación que
deben aparecer en todas las páginas. A continuación se enumeran las rutas
esperadas para cada elemento del menú principal:

- `index.php` – Inicio
- `historia/historia.php` – Nuestra Historia
- `historia_cerezo/index.php` – Historia de Cerezo
- `historia/subpaginas/obispado_auca_cerezo.php` – Obispado de Auca
- `alfoz/alfoz.php` – El Alfoz
- `lugares/lugares.php` – Lugares Emblemáticos
- `ruinas/index.php` – Ruinas y Vestigios
- `camino_santiago/camino_santiago.php` – Camino de Santiago
- `museo/galeria.php` – Museo Colaborativo
- `museo/museo_3d.php` – Museo 3D
- `museo/subir_pieza.php` – Subir Pieza
- `galeria/galeria_colaborativa.php` – Galería Colaborativa
- `tienda/index.php` – Tienda
- `visitas/visitas.php` – Planifica Tu Visita
- `citas/agenda.php` – Programa de Citas
- `cultura/cultura.php` – Cultura y Legado
- `personajes/indice_personajes.html` – Personajes
- `empresa/index.php` – Gestión de Yacimientos
- `foro/index.php` – Foro
- `blog.php` – Blog
- Los artículos se escriben en Markdown dentro de `contenido/blog/` y se convierten a HTML mediante la función `render_markdown()` que utiliza **league/commonmark**.
- `contacto/contacto.php` – Contacto

Mantén esta lista actualizada cuando se añadan o eliminen páginas para que se
pueda validar fácilmente el contenido del menú.

Anteriormente existía un menú de herramientas adicional, pero se ha retirado
para evitar enlaces duplicados en la navegación.

## Cambios recientes

- Eliminado `js/header_loader.js` y su inclusión en `includes/head_common.php`; la cabecera se carga ahora directamente desde `_header.php`.



## Scripts de utilidad

### Comprobación de etiquetas `<img>`

Ejecuta el script `check_alt_texts.sh` para detectar imágenes sin atributo `alt` en los archivos PHP y HTML:

```bash
./scripts/check_alt_texts.sh
```

Esta comprobación se ejecuta automáticamente en cada pull request gracias a la configuración de GitHub Actions.

Puedes indicar una ruta concreta como argumento si solo quieres escanear una carpeta determinada. El comando mostrará las líneas problemáticas y devolverá un código de salida distinto de cero si encuentra imágenes sin descripción.

## Tema predeterminado

El sitio arranca en modo oscuro. Si el navegador no tiene guardada una preferencia en `localStorage`, el script `assets/js/main.js` añade la clase `dark-mode` al elemento `<body>` y muestra el icono `fa-sun` en el botón de cambio de tema. Al pulsar dicho botón se alterna entre modo oscuro y claro, actualizando el icono (`fa-sun` o `fa-moon`) y almacenando la elección para futuras visitas.

## Efecto de compresión del menú deslizante

Cada vez que se abre un panel `.menu-panel`, `assets/js/main.js` agrega la clase `menu-compressed` al elemento `<body>` junto con `menu-open-left` o `menu-open-right` según el lado del que se despliegue. Estas clases están definidas en `assets/css/sliding_menu.css`:

```css
body.menu-compressed {
    transition: transform 0.3s ease;
    transform: scale(0.97);
}
body.menu-open-left {
    transform: translateX(260px) scale(0.97);
}
body.menu-open-right {
    transform: translateX(-260px) scale(0.97);
}
```

El contenido se desplaza ligeramente y se escala al 97%, dando la sensación de que el sitio se comprime por el lateral desde el que aparece el menú. Al cerrarlo, las clases se eliminan y la página vuelve a su posición original.

## Parallax

Se incluyen capas de parallax que se mueven con el desplazamiento de la página.
Cada capa define su velocidad mediante el atributo `data-speed` y el script
`assets/js/parallax.js` aplica la transformación `translateY` correspondiente.
Si el usuario tiene activada la preferencia de movimiento reducido, el efecto se
deshabilita automáticamente.

## Actualizar el árbol de páginas

Para regenerar el archivo `condensed_website_tree.json` que resume la estructura
del sitio basta con ejecutar:

```bash
python tree_builder.py
```

El script explora las distintas secciones y crea un fichero JSON con las URLs
ordenadas jerárquicamente.

## Generar `sitemap.xml`

Para actualizar el mapa del sitio ejecuta:

```bash
python scripts/generate_sitemap.py
```

El script leerá la variable de entorno `BASE_URL` para construir cada entrada.
Define esta variable en tu `.env` antes de ejecutar el comando si necesitas
apuntar a un dominio distinto al predeterminado.

## CDN Checksums

Los siguientes resúmenes se utilizan en los atributos `integrity` para los archivos cargados desde CDN. Se calcularon con `openssl dgst -sha384 -binary | openssl base64 -A`.

- **GSAP 3.13.0**: `sha384-HOvlOYPIs/zjoIkWUGXkVmXsjr8GuZLV+Q+rcPwmJOVZVpvTSXQChiN4t9Euv9Vc`
- **AOS 2.3.4 CSS**: `sha384-/rJKQnzOkEo+daG0jMjU1IwwY9unxt1NBw3Ef2fmOJ3PW/TfAg2KXVoWwMZQZtw9`
- **AOS 2.3.4 JS**: `sha384-n1AULnKdMJlK1oQCLNDL9qZsDgXtH6jRYFCpBtWFc+a9Yve0KSoMn575rk755NJZ`
