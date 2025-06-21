#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

pip install -r requirements.txt

RESULT=0

if [ -x vendor/bin/phpunit ]; then
    echo "Ejecutando pruebas de PHP..."
    vendor/bin/phpunit || RESULT=$?
else
    echo "PHPUnit no encontrado. Omitiendo pruebas de PHP." >&2
fi

if [ -f package.json ]; then
    if command -v npm >/dev/null 2>&1; then
        echo "Ejecutando pruebas de Node..."
        npm test || RESULT=$?
    else
        echo "npm no está instalado. Omitiendo pruebas de Node." >&2
    fi
else
    echo "package.json no encontrado. Omitiendo pruebas de Node." >&2
fi

echo "Ejecutando pruebas de Python..."
python -m unittest discover -s tests || RESULT=$?

exit $RESULT
