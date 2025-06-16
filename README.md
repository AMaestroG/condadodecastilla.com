# condadodecastilla.com

Este repositorio contiene el código del sitio web del proyecto **Condado de Castilla**.

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

## Comprobacion de la base de datos

Para verificar que PostgreSQL esta disponible y que la variable `CONDADO_DB_PASSWORD` se ha definido, ejecuta:

```bash
export CONDADO_DB_PASSWORD="tu_contrasena"
./scripts/check_db.sh
```

El script usa `pg_isready` para comprobar la conexion y mostrara un mensaje de exito o error.


## Servidor de desarrollo

Para probar el sitio de forma local puedes usar el servidor embebido de PHP. Sitúate en la raíz del repositorio y ejecuta:

```bash
php -S localhost:8000
```

Esto iniciará el servidor en `http://localhost:8000`.

## Instalación de dependencias

Antes de ejecutar el sitio por primera vez descarga las librerías necesarias:

```bash
composer install --ignore-platform-req=ext-dom --ignore-platform-req=ext-xmlwriter --ignore-platform-req=ext-xml
./scripts/setup_frontend_libs.sh
```

Tras clonar el repositorio ejecuta `./scripts/setup_frontend_libs.sh` para
descargar las versiones actuales de **jQuery** y **Bootstrap** en el directorio
`assets/vendor`.

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

El valor se inyectará en la etiqueta `<meta name="gemini-api-key">` generada por `includes/head_common.php` y será leído por `assets/js/main.js` para realizar las peticiones.

## Ejecución de pruebas

Para instalar las dependencias de desarrollo y ejecutar la batería de pruebas
usa **Composer** y **PHPUnit**:

```bash
composer install
vendor/bin/phpunit
```
