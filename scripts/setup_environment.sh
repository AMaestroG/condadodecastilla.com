#!/bin/bash
set -uo pipefail

# Determine repository root and switch to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Required tool versions
REQUIRED_PHP_VERSION="8.1"
REQUIRED_NODE_VERSION="18"
REQUIRED_PYTHON_VERSION="3.10"

# Basic version check helper
check_version() {
    local cmd="$1"
    local required="$2"
    local name="$3"

    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo "Error: $name $required or higher is required but not found." >&2
        return 1
    fi

    local current="$($cmd --version | head -n1 | grep -oE '[0-9]+(\.[0-9]+)+')"
    if [ "$(printf '%s\n' "$required" "$current" | sort -V | head -n1)" != "$required" ]; then
        echo "Error: $name $required or higher is required, but $current is installed." >&2
        return 1
    fi
}

# Check language runtimes
check_version php "$REQUIRED_PHP_VERSION" "PHP" || true
check_version node "$REQUIRED_NODE_VERSION" "Node.js" || true
check_version python3 "$REQUIRED_PYTHON_VERSION" "Python" || true

# Verify Composer is available before proceeding
if ! command -v composer >/dev/null 2>&1; then
    echo "Error: Composer is required to run this script. Please install it first." >&2
    exit 1
fi

# Install PHP dependencies
if ! composer install --no-interaction --no-progress --prefer-dist; then
    echo "Warning: composer install failed." >&2
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

