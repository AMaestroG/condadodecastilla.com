# condadodecastilla.com

Este repositorio contiene el código del sitio web del proyecto **Condado de Castilla**.

## Misión

Promocionar el turismo en **Cerezo de Río Tirón** y gestionar de forma activa su patrimonio arqueológico y cultural.

## Requisitos

- **PHP** 7.4 o superior con la extensión PDO habilitada.
- **PostgreSQL** 9.6 o superior.

## Configuración de la base de datos

El archivo `dashboard/db_connect.php` define los parámetros para conectarse a la base de datos PostgreSQL. Se proporciona con valores de ejemplo y **debe** modificarse con las credenciales reales antes de usar el proyecto en producción.

Fragmento relevante de `dashboard/db_connect.php`:

```php
$db_host = "localhost";         // Host de la base de datos
$db_name = "condado_castilla_db"; // Nombre de la base de datos
$db_user = "condado_user";        // Usuario
$db_pass = "tu_contraseña_muy_segura"; // CONTRASEÑA - reemplazar por la real
$db_port = "5432";                // Puerto de PostgreSQL
```

Ajusta esos valores según tu entorno para que la aplicación pueda acceder a la base de datos.

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

Para exponer la API Flask primero instala las dependencias de Python y lanza el servicio:

```bash
pip install -r requirements.txt
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

Tras clonar el repositorio instala las dependencias de PHP y descarga las
bibliotecas de JavaScript necesarias ejecutando:

```bash
composer install --ignore-platform-req=ext-dom --ignore-platform-req=ext-xmlwriter --ignore-platform-req=ext-xml
./scripts/setup_frontend_libs.sh
```

Este proyecto utiliza la librería **league/commonmark** para transformar a HTML los archivos Markdown del blog. La dependencia se instala automáticamente con el comando anterior.

El script descarga las bibliotecas **jQuery**, **Bootstrap** y **Tailwind CSS**
en `assets/vendor`. Tras ello genera la hoja `assets/vendor/css/tailwind.min.css`
si ejecutas:

```bash
npx tailwindcss@3.4.4 -i assets/css/tailwind_base.css -o assets/vendor/css/tailwind.min.css --minify
```

A continuación copia el archivo de ejemplo `.env.example` a `.env`
y sustituye sus valores por tus credenciales reales para
`CONDADO_DB_PASSWORD`, `GEMINI_API_KEY`
y cualquier otra variable necesaria.


Las variables definidas en `.env` se cargan de forma automática gracias a `includes/env_loader.php`.

## Configuración de la clave Gemini

Define la variable `GEMINI_API_KEY` en tu archivo `.env` con la clave proporcionada por el servicio Gemini:

```bash
GEMINI_API_KEY=tu_clave_personal
```
Solo se admite la variable `GEMINI_API_KEY`; otros nombres anteriores han sido eliminados.

El valor se inyectará en la etiqueta `<meta name="gemini-api-key">` generada por `includes/head_common.php` y será leído por `assets/js/main.js` para realizar las peticiones.

## Ejecución de pruebas

Para instalar PHPUnit y ejecutar la suite de tests ejecuta:

```bash
composer install
vendor/bin/phpunit
```

El primer comando descargará las dependencias necesarias, incluido PHPUnit, y el
segundo lanzará todos los tests definidos en `phpunit.xml`.

### Pruebas de la API Flask

Para ejecutar las pruebas escritas en Python que validan la API Flask utiliza:

```bash
python -m unittest tests/test_flask_api.py
```

## Elementos del menú principal

El archivo `fragments/menus/main-menu.html` define los enlaces de navegación que
deben aparecer en todas las páginas. A continuación se enumeran las rutas
esperadas para cada elemento del menú principal:

- `/index.php` – Inicio
- `/historia/historia.php` – Nuestra Historia
- `/historia_cerezo/index.php` – Historia de Cerezo
- `/historia/subpaginas/obispado_auca_cerezo.php` – Obispado de Auca
- `/alfoz/alfoz.php` – El Alfoz
- `/lugares/lugares.html` – Lugares Emblemáticos
- `/ruinas/index.html` – Ruinas y Vestigios
- `/camino_santiago/camino_santiago.php` – Camino de Santiago
- `/museo/galeria.php` – Museo Colaborativo
- `/museo/museo_3d.php` – Museo 3D
- `/museo/subir_pieza.php` – Subir Pieza
- `/galeria/galeria_colaborativa.php` – Galería Colaborativa
- `/tienda/index.php` – Tienda
- `/visitas/visitas.php` – Planifica Tu Visita
- `/citas/agenda.php` – Programa de Citas
- `/cultura/cultura.php` – Cultura y Legado
- `/personajes/indice_personajes.html` – Personajes
- `/empresa/index.php` – Gestión de Yacimientos
- `/foro/index.php` – Foro
- `/blog.php` – Blog
- Los artículos se escriben en Markdown dentro de `contenido/blog/` y se convierten a HTML mediante la función `render_markdown()` que utiliza **league/commonmark**.
- `/contacto/contacto.php` – Contacto

Mantén esta lista actualizada cuando se añadan o eliminen páginas para que se
pueda validar fácilmente el contenido del menú.

El menú lateral derecho se genera a partir de `fragments/menus/tools-menu.html` y
ofrece accesos rápidos a páginas clave. Se incluye dentro del elemento
`<nav id="slide-menu-right">` en `_header.php` y comparte la misma temática de
colores morado y oro viejo que el resto de la cabecera.

## Cambios recientes

- Eliminado `js/header_loader.js` y referencias a dicho script, ya que la cabecera se carga ahora de forma estática.



## Scripts de utilidad

### Comprobación de etiquetas `<img>`

Ejecuta el script `check_alt_texts.sh` para detectar imágenes sin atributo `alt` en los archivos PHP y HTML:

```bash
./scripts/check_alt_texts.sh
```

Puedes indicar una ruta concreta como argumento si solo quieres escanear una carpeta determinada. El comando mostrará las líneas problemáticas y devolverá un código de salida distinto de cero si encuentra imágenes sin descripción.
