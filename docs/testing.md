# Guía de Testing

Esta guía explica cómo preparar el entorno y ejecutar las pruebas automatizadas del proyecto.

## Configurar el entorno

Antes de lanzar **cualquier** suite de pruebas ejecuta `setup_environment.sh`.
Este script instala de una sola vez las dependencias de PHP, Python y Node. Si
detecta que alguna de estas herramientas no está presente intentará instalarla
mediante `apt-get` (por ejemplo **PHP CLI**, **Composer** o **Node.js 18**).
El proyecto requiere como mínimo **PHP&nbsp;8.1**, **Node&nbsp;18** y
**Python&nbsp;3.10**. El propio script comprueba estas versiones y avisa en caso
de no encontrarlas o si son antiguas. Tras solventar cualquier ausencia podrás
ejecutar manualmente el paso correspondiente y repetir la preparación.

```bash
./packages/devops/setup_environment.sh
```

## Servidor PHP local para Puppeteer

Las pruebas de Puppeteer requieren servir los archivos PHP de forma local. Inicia un servidor en otro terminal antes de lanzar la suite:

```bash
php -S localhost:8080
```

## Orden de ejecución de las pruebas

Con las dependencias instaladas y el servidor en marcha, ejecuta las suites en el siguiente orden:

```bash
# 1. Pruebas de PHP
vendor/bin/phpunit

# 2. Pruebas de Python
python -m unittest tests/test_flask_api.py tests/test_graph_db_interface.py

# 3. Pruebas de interfaz con Puppeteer
npm run test:puppeteer
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

Antes de lanzar la suite de interfaz instala las dependencias de Node:

```bash
npm ci
```

Esto descargará `puppeteer` y los binarios de Chrome.



## Solucion de problemas

- Si al ejecutar `npm run test:puppeteer` obtienes un TimeoutError, comprueba que tienes un servidor PHP en marcha con:
```bash
php -S localhost:8080
```
- Si las dependencias fallaron al instalarse, vuelve a lanzar `./packages/devops/setup_environment.sh`.
- Si Puppeteer informa que no se encuentra Chromium, ejecuta `npm ci` para reinstalar las dependencias.
