#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

if ! command -v python3 >/dev/null 2>&1; then
    echo "Error: python3 is not installed or not in PATH." >&2
    exit 1
fi

pip install -r requirements.txt
python3 -m unittest discover -s tests
