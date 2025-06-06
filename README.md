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

## Servidor de desarrollo

Para probar el sitio de forma local puedes usar el servidor embebido de PHP. Sitúate en la raíz del repositorio y ejecuta:

```bash
php -S localhost:8000
```

Esto iniciará el servidor en `http://localhost:8000`.

## API

La API del museo está disponible en la ruta `/api/museo/piezas`, gestionada por el script `api_museo.php`. Desde ahí es posible obtener o crear registros de piezas del museo.


## Scripts de mantenimiento

Este repositorio incluye utilidades en Python dentro del directorio `scripts/`. El script `auto_update.py` ejecuta tres tareas clave:

1. Procesar las fichas de personajes y generar el archivo `characters_enriched.json`.
2. Crear `sitemap.xml` con todas las páginas del sitio.
3. Analizar el grafo de conocimiento para verificar su consistencia.

### Instalación de dependencias

Se recomienda usar un entorno virtual de Python. Desde la raíz del proyecto:

```bash
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### Ejecución manual

```bash
python scripts/auto_update.py
```

### Programar con cron

Para ejecutar el script automáticamente cada día puedes añadir una entrada al `crontab` del usuario:

```bash
0 3 * * * /ruta/al/proyecto/venv/bin/python /ruta/al/proyecto/scripts/auto_update.py >> /ruta/al/proyecto/auto_update.log 2>&1
```

Esto lanzará la actualización a las 3:00 AM y registrará la salida en `auto_update.log`.
