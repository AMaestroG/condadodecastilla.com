#!/bin/bash
set -uo pipefail

# Determine repository root and switch to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Install PHP dependencies
if command -v composer >/dev/null 2>&1; then
    if ! composer install --no-interaction --no-progress --prefer-dist; then
        echo "Warning: composer install failed." >&2
    fi
else
    echo "Warning: Composer not found; skipping PHP dependencies." >&2
fi

# Install Python dependencies
if command -v pip >/dev/null 2>&1; then
    if ! pip install -r requirements.txt; then
        echo "Warning: pip install failed." >&2
    fi
else
    echo "Warning: pip not found; skipping Python dependencies." >&2
fi

# Install Node dependencies
if command -v npm >/dev/null 2>&1; then
    if ! npm ci; then
        echo "Warning: npm install failed." >&2
    fi
else
    echo "Warning: npm not found; skipping Node dependencies." >&2
fi

cat <<MSG
Environment setup completed.
MSG

