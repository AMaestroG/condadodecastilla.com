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

## Consideraciones de produccion

La muestra de errores PHP se mantiene deshabilitada en `dashboard/db_connect.php` e `dashboard/index.php` para no exponer detalles sensibles en produccion.
## API

La API del museo está disponible en la ruta `/api/museo/piezas`, gestionada por el script `api_museo.php`. Desde ahí es posible obtener o crear registros de piezas del museo.


## Ejemplo de uso de la API de Gemini

Para realizar una prueba con la API de Gemini puedes utilizar el script `scripts/gemini_request.sh`. Este script envía una solicitud al modelo *gemini-2.0-flash* y muestra la respuesta.

Antes de ejecutarlo, define la variable de entorno `GEMINI_API_KEY` con tu clave de API de Google (también se acepta la variable `GeminiAPI` como alternativa):

```bash
export GEMINI_API_KEY="tu_clave"
./scripts/gemini_request.sh
```

El script realizará una petición sencilla que pregunta *"Explain how AI works in a few words"*.

## Gemini API configuration

Para que las funciones de IA de `includes/ai_utils.php` puedan comunicarse con la API de Gemini
es necesario definir las siguientes variables de entorno:

- `GEMINI_API_KEY`: clave de autenticación para las peticiones.
- `GeminiAPI`: nombre alternativo para la clave de autenticación, útil si tu sistema ya define esta variable.
- `GEMINI_API_ENDPOINT`: URL del servicio al que se enviarán las solicitudes. Si no se establece,
  se usa un valor de ejemplo que activa un simulador interno.

Si está disponible, puedes revisar el script opcional `scripts/gemini_request.sh`
para ver un ejemplo básico de invocación que hace uso de `GEMINI_API_KEY`.
Encuentra la tienda en [tienda/index.php](tienda/index.php).

### Funciones de IA disponibles

El archivo `includes/ai_utils.php` proporciona utilidades que hacen uso de la API de Gemini. Entre ellas destacan:

- `get_real_ai_summary($texto)`: genera resúmenes concisos.
- `get_ai_translation($texto, $idioma)`: traduce el texto al idioma indicado.
- `get_ai_correction($texto)`: devuelve una versión corregida del texto.

Si no se configuran las credenciales reales de la API, se utilizará un simulador que produce textos de demostración.

La página `historia/atapuerca.php` incluye un selector de idioma de demostración que muestra traducciones simuladas generadas por IA. Actualmente se ofrecen versiones en inglés, francés y **alemán**.

## Galería 2D y Museo 3D

El sitio cuenta con dos formas de visualizar las piezas:

1. **Galería 2D** – disponible en `galeria/galeria_colaborativa.php` y enfocada en las imágenes aportadas por los usuarios.
2. **Museo 3D** – se accede mediante `museo/museo.php` e incluye un recorrido tridimensional generado con Three.js.
   Pulsa la tecla **ESC** para salir del modo de control en primera persona. El script ahora gestiona manualmente esta tecla por si el navegador no desbloquea automáticamente el puntero.

Ambas vistas consumen la API en `/api/museo/piezas` para obtener la lista de elementos del museo.
Inicia el servidor con `php -S localhost:8000` y visita `http://localhost:8000/museo/museo.php` para probarlo.

### Activar características de administración

Algunas acciones (por ejemplo subir piezas o eliminar fotografías) solo están disponibles para administradores.
Accede a `/dashboard/login.php` e inicia sesión con un usuario registrado en la tabla `users`.
La conexión a la base de datos requiere que la variable de entorno `CONDADO_DB_PASSWORD` contenga la contraseña correspondiente.

## Foro

Se ha añadido una sección de foro en `foro/index.html`. Por el momento funciona como una página de aviso mientras se desarrolla el espacio de discusión.

## Almacenamiento de imágenes del museo

Las imágenes subidas a través de la API del museo se guardan en el directorio `uploads_storage/museo_piezas`.
Antes de ejecutar la API asegúrate de crear esta carpeta y asignar permisos de escritura al usuario que ejecute el servidor web.

Ejemplo de creación del directorio en Linux:

```bash
mkdir -p uploads_storage/museo_piezas
chmod 775 uploads_storage/museo_piezas
```

## Agente diario

Se incluye el script `scripts/daily_agent.py` que ejecuta tareas de mantenimiento automáticas:

- Generación del `sitemap.xml`.
- Comprobación de enlaces internos.
- Opcionalmente, verificación de la base de datos y una solicitud de prueba a la API de Gemini.

Puede programarse con `cron` o utilizar el *workflow* de GitHub en `.github/workflows/daily_agent.yml`, el cual se ejecuta cada día de forma automática.
