# Guía de contribución

Gracias por tu interés en colaborar en **condadodecastilla.com**. Esta guía resume los pasos básicos para preparar el entorno de desarrollo e instalar las dependencias necesarias.

## Requisitos previos

Asegúrate de contar con:

- PHP 8.1 o superior y [Composer](https://getcomposer.org/).
- Node.js 18 o superior con `npm`.
- Python 3.10 o superior con `pip`.

## Instalación rápida

Desde la raíz del proyecto ejecuta:

```bash
./scripts/setup_environment.sh
```

El script descarga las dependencias de PHP con `composer install`, instala los paquetes de Python listados en `requirements.txt` y ejecuta `npm install` para preparar las bibliotecas de Node. Si algún runtime no está disponible, intentará instalarlo de forma automática.

## Procedimiento manual

Si prefieres controlar cada paso por separado puedes lanzar manualmente:

```bash
composer install
pip install -r requirements.txt
npm install
```

Con esto tendrás listo el entorno para ejecutar las pruebas descritas en [docs/testing.md](docs/testing.md).

## Ejecutar pruebas

1. Instala las dependencias con `npm install`, `pip install -r requirements.txt` y `composer install`.
2. Usa `npm run test` para correr las pruebas de Node. Este comando ejecuta `scripts/start_php_server.sh` para iniciar un servidor PHP temporal en `localhost:8080`.
3. Si lo prefieres, tambien puedes lanzar las pruebas de PHP y Python manualmente:
   ```bash
   vendor/bin/phpunit
   python -m unittest discover -s tests
   ```

