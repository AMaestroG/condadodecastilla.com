#!/bin/bash
set -euo pipefail

# Determine repository root and switch to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Install PHP dependencies
if command -v composer >/dev/null 2>&1; then
    composer install --no-interaction --no-progress --prefer-dist
else
    echo "Composer not found. Please install Composer to manage PHP dependencies." >&2
    exit 1
fi

# Install Python dependencies
if command -v pip >/dev/null 2>&1; then
    pip install -r requirements.txt
else
    echo "pip not found. Please install pip to manage Python dependencies." >&2
    exit 1
fi

# Install Node dependencies
if command -v npm >/dev/null 2>&1; then
    npm ci
else
    echo "npm not found. Please install Node.js and npm to manage Node dependencies." >&2
    exit 1
fi

cat <<MSG
Environment setup completed.
MSG

