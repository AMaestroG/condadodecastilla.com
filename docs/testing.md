# Guía de Testing

Esta guía explica cómo preparar el entorno y ejecutar las pruebas automatizadas del proyecto.

## Configurar el entorno

Ejecuta el script `setup_environment.sh` para instalar de una sola vez las dependencias de PHP, Python y Node. Es necesario contar con `composer`, `pip` y `npm` en el sistema.

```bash
./scripts/setup_environment.sh
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
