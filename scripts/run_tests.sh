#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

pip install -r requirements.txt
# Install Node dependencies if missing
if [[ ! -d node_modules || ! -f package-lock.json ]]; then
  echo "Node packages not found. Installing..."
  npm ci
fi

python -m unittest discover -s tests
