#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

if command -v composer >/dev/null 2>&1; then
    composer install --no-interaction --prefer-dist
else
    echo "Error: Composer not found" >&2
    exit 1
fi

if command -v pip >/dev/null 2>&1; then
    pip install -r requirements.txt
else
    echo "Error: pip not found" >&2
    exit 1
fi
