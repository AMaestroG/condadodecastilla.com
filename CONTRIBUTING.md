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

## Pruebas

Antes de ejecutar `npm test`, instala primero las dependencias de Node:

```bash
npm install
```

Las pruebas basadas en Node usan **puppeteer**, por lo que este paquete debe estar disponible para que la suite se ejecute correctamente.
