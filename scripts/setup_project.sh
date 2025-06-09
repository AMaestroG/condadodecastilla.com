#!/bin/bash
set -euo pipefail

# Ensure Composer is available
COMPOSER_CMD=""
if command -v composer >/dev/null 2>&1; then
    COMPOSER_CMD="composer"
elif [ -f composer.phar ]; then
    COMPOSER_CMD="php composer.phar"
else
    echo "Composer no encontrado. Descargando composer.phar..."
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    php composer-setup.php --quiet
    rm composer-setup.php
    COMPOSER_CMD="php composer.phar"
fi

# Install PHP dependencies
$COMPOSER_CMD install --ignore-platform-req=ext-dom \
                      --ignore-platform-req=ext-xmlwriter \
                      --ignore-platform-req=ext-xml

# Setup frontend libraries
scripts/setup_frontend_libs.sh

# Copy environment file if needed
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
    echo "Archivo .env creado a partir de .env.example"
fi

# Create upload directories
mkdir -p uploads/galeria \
         uploads_storage/museo_piezas \
         uploads_storage/tienda_productos

# Optional database check
if [ -n "${CONDADO_DB_PASSWORD:-}" ] && [ -x scripts/check_db.sh ]; then
    scripts/check_db.sh || echo "No se pudo comprobar la base de datos"
fi

cat <<MSG
Configuración completada.
MSG

