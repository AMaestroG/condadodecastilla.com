#!/bin/bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Determine pip executable
if [[ -x "venv/bin/pip" ]]; then
    PIP="venv/bin/pip"
else
    PIP="$(command -v pip || command -v pip3)"
fi

if [[ -z "$PIP" ]]; then
    echo "pip no encontrado" >&2
    exit 1
fi

"$PIP" install -r requirements.txt

echo "Dependencias de Python instaladas."
