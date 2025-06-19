#!/bin/bash
set -euo pipefail

missing=()

if ! command -v php >/dev/null 2>&1; then
  missing+=("php")
fi

if ! command -v composer >/dev/null 2>&1; then
  missing+=("composer")
fi

if ! command -v npm >/dev/null 2>&1; then
  missing+=("npm")
fi

if [ ${#missing[@]} -ne 0 ]; then
  echo "Faltan los siguientes comandos: ${missing[*]}" >&2
  echo "Instálalos para poder ejecutar todas las pruebas y scripts" >&2
  exit 1
else
  echo "php, composer y npm están disponibles"
fi
