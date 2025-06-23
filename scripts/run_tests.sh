#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# Ensure PYTHONPATH includes project root for module resolution
export PYTHONPATH="$PROJECT_ROOT:${PYTHONPATH:-}"

if ! command -v composer >/dev/null 2>&1; then
    echo "Composer not found. Installing..." >&2
    if apt-get update -y >/dev/null 2>&1 && apt-get install -y composer >/dev/null 2>&1; then
        echo "Composer installed via apt" >&2
    else
        echo "Downloading composer.phar..." >&2
        if curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1; then
            mv composer.phar /usr/local/bin/composer
            chmod +x /usr/local/bin/composer
        else
            echo "Error: failed to install Composer" >&2
            exit 1
        fi
    fi
fi

if ! command -v python3 >/dev/null 2>&1; then
    echo "Error: python3 is not installed or not in PATH." >&2
    exit 1
fi

composer install --no-interaction --prefer-dist
pip install -r requirements.txt

vendor/bin/phpunit tests/SoundAssetsTest.php
python3 -m unittest discover -s tests
