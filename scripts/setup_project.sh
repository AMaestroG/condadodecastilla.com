#!/bin/bash
set -euo pipefail

# Determine repository root and switch to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Ensure Composer is available
COMPOSER_CMD=""
if command -v composer >/dev/null 2>&1; then
    COMPOSER_CMD="composer"
elif [ -f "$PROJECT_ROOT/composer.phar" ]; then
    COMPOSER_CMD="php $PROJECT_ROOT/composer.phar"
else
    echo "Composer no encontrado. Descargando composer.phar..."
    curl -sS https://getcomposer.org/installer -o composer-setup.php
    php composer-setup.php --quiet
    rm composer-setup.php
    COMPOSER_CMD="php $PROJECT_ROOT/composer.phar"
fi

# Install PHP dependencies
$COMPOSER_CMD install --ignore-platform-req=ext-dom \
                      --ignore-platform-req=ext-xmlwriter \
                      --ignore-platform-req=ext-xml

# Setup frontend libraries
"$PROJECT_ROOT"/scripts/setup_frontend_libs.sh

# Copy environment file if needed
if [ ! -f "$PROJECT_ROOT/.env" ] && [ -f "$PROJECT_ROOT/.env.example" ]; then
    cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
    echo "Archivo .env creado a partir de .env.example"
fi

# Create upload directories
mkdir -p "$PROJECT_ROOT"/uploads/galeria \
         "$PROJECT_ROOT"/uploads_storage/museo_piezas \
         "$PROJECT_ROOT"/uploads_storage/tienda_productos

# Optional database check
if [ -n "${CONDADO_DB_PASSWORD:-}" ] && [ -x "$PROJECT_ROOT/scripts/check_db.sh" ]; then
    "$PROJECT_ROOT"/scripts/check_db.sh || echo "No se pudo comprobar la base de datos"
fi

cat <<MSG
Configuración completada.
MSG
