# condadodecastilla.com

Este repositorio contiene el código del sitio web del proyecto **Condado de Castilla**.

## Misión

Promocionar el turismo en **Cerezo de Río Tirón** y gestionar de forma activa su patrimonio arqueológico y cultural.

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

El archivo `includes/db_connect.php` define los parámetros para conectarse a la base de datos PostgreSQL. Se proporciona con valores de ejemplo y **debe** modificarse con las credenciales reales antes de usar el proyecto en producción.

Fragmento relevante de `includes/db_connect.php`:

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

El valor se inyectará en la etiqueta `<meta name="gemini-api-key">` generada por `includes/head_common.php` y será utilizado por las funciones del servidor definidas en `includes/ai_utils.php` para realizar las peticiones.

## Ejecución de pruebas

Instala **todas** las dependencias antes de lanzar las suites de pruebas. Las pruebas dependen de PHP, Composer y las extensiones indicadas, por lo que no se ejecutarán si alguno de estos componentes falta:

```bash
composer install
pip install -r requirements.txt
```

Con las dependencias ya presentes, ejecuta cada conjunto de tests de forma explícita:

```bash
vendor/bin/phpunit
python -m unittest tests/test_flask_api.py
npm run test:puppeteer
```

`vendor/bin/phpunit` lanzará todos los tests definidos en `phpunit.xml`. Se incluyen pruebas que cargan las páginas principales del sitio y comprueban la presencia del contenedor `#fixed-header-elements`.

## Elementos del menú principal

El archivo `fragments/menus/main-menu.html` define los enlaces de navegación que
deben aparecer en todas las páginas. A continuación se enumeran las rutas
esperadas para cada elemento del menú principal:

- `/index.php` – Inicio
- `/historia/historia.php` – Nuestra Historia
- `/historia_cerezo/index.php` – Historia de Cerezo
- `/historia/subpaginas/obispado_auca_cerezo.php` – Obispado de Auca
- `/alfoz/alfoz.php` – El Alfoz
 - `/lugares/lugares.php` – Lugares Emblemáticos
 - `/ruinas/index.php` – Ruinas y Vestigios
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

## Foro y agentes expertos

El proyecto define cinco agentes que participan en el foro para orientar las conversaciones:

- **Alicia la Historiadora** – especialista en la historia de Castilla y de Cerezo de Río Tirón.
- **Bruno el Arqueólogo** – explora y protege el patrimonio arqueológico.
- **Clara la Guía Turística** – orienta a los visitantes en sus recorridos.
- **Diego el Gestor Cultural** – coordina eventos y dinamiza la cultura local.
- **Elena la Tecnóloga** – aplica innovación tecnológica al servicio del patrimonio.

Para ajustar sus biografías o añadir nuevos perfiles edita el archivo `config/forum_agents.php` y actualiza el array de configuración.

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
