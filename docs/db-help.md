# Ayuda de Base de Datos

Este documento resume los problemas más habituales al conectarse a la base de datos.

## Comprobar variables de entorno

Asegúrate de que las variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PORT` y `CONDADO_DB_PASSWORD` estén definidas en tu entorno o en el archivo `.env`. Sin ellas `includes/db_connect.php` no podrá crear la conexión. Usa `APP_DEBUG` para activar mensajes de error en desarrollo.

## Revisar extensiones de PHP

Habilita las extensiones `pdo_pgsql` y `pgsql` para trabajar con PostgreSQL. Para pruebas con SQLite necesitarás `pdo_sqlite`.

## Errores frecuentes

- **Fallo de conexión**: verifica que el servidor de base de datos esté activo y que los datos de host y puerto sean correctos.
- **Credenciales incorrectas**: actualiza `CONDADO_DB_PASSWORD` y vuelve a iniciar el servicio PHP.
- **Extensión faltante**: ejecuta `php -m` y comprueba que las extensiones requeridas aparezcan en la lista.

Consulta `includes/db_connect.php` para más detalles sobre la configuración.
