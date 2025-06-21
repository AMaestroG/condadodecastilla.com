#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

if command -v composer >/dev/null 2>&1; then
    composer install --no-interaction --prefer-dist
else
    echo "Warning: Composer not found. Consider running scripts/setup_environment.sh" >&2
fi

pip install -r requirements.txt
python -m unittest discover -s tests
