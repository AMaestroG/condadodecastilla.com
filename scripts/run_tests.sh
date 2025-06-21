#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

pip install -r requirements.txt

# Ensure Node dev dependencies (e.g., Puppeteer) are available
if command -v npm >/dev/null 2>&1; then
    if ! node -e "require('puppeteer')" >/dev/null 2>&1; then
        echo "Installing Node dev dependencies..."
        npm ci
    fi
else
    echo "Warning: npm not found; skipping Node dependency setup." >&2
fi

TMP_LOG=$(mktemp)
if ! python -m unittest discover -s tests >"$TMP_LOG" 2>&1; then
    if grep -q "ModuleNotFoundError" "$TMP_LOG"; then
        echo "Error: faltan módulos de Python. Ejecuta 'pip install -r requirements.txt'." >&2
    fi
    cat "$TMP_LOG"
    exit 1
fi
