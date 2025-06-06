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
Si dicha variable no está definida, `dashboard/db_connect.php` lanzará una
`RuntimeException` indicando el problema.

## Servidor de desarrollo

Para probar el sitio de forma local puedes usar el servidor embebido de PHP. Sitúate en la raíz del repositorio y ejecuta:

```bash
php -S localhost:8000
```

Esto iniciará el servidor en `http://localhost:8000`.

## API

La API del museo está disponible en la ruta `/api/museo/piezas`, gestionada por el script `api_museo.php`. Desde ahí es posible obtener o crear registros de piezas del museo.


## Ejemplo de uso de la API de Gemini

Para realizar una prueba con la API de Gemini puedes utilizar el script `scripts/gemini_request.sh`. Este script envía una solicitud al modelo *gemini-2.0-flash* y muestra la respuesta.

Antes de ejecutarlo, define la variable de entorno `GEMINI_API_KEY` con tu clave de API de Google:

```bash
export GEMINI_API_KEY="tu_clave"
./scripts/gemini_request.sh
```

El script realizará una petición sencilla que pregunta *"Explain how AI works in a few words"*.
