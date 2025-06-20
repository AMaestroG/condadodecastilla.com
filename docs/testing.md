# Guía de Testing

Esta guía explica cómo preparar el entorno y ejecutar las pruebas automatizadas del proyecto.

## Configurar el entorno

Antes de lanzar **cualquier** suite de pruebas ejecuta `setup_environment.sh`. Este script instala de una sola vez las dependencias de PHP, Python y Node.
Si alguno de estos gestores no está disponible, el script mostrará una advertencia y continuará con el resto, de modo que la configuración puede completarse de forma parcial.
El proyecto requiere como mínimo **PHP&nbsp;8.1**, **Node&nbsp;18** y **Python&nbsp;3.10**. El propio script comprueba estas versiones y avisa en caso de no encontrarlas o si son antiguas.
Tras solventar cualquier ausencia podrás ejecutar manualmente el paso correspondiente y repetir la preparación.

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
