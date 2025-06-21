#!/bin/bash
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

pip install -r requirements.txt
python -m unittest discover -s tests

# Install PHP dependencies if needed
if [ -f composer.json ]; then
    composer install --no-interaction
fi

# Start PHP built-in server in the background as package.json does
php -S localhost:8080 >/dev/null 2>&1 & echo $! > .php_server.pid

# Ensure the server is stopped on script exit
cleanup() {
    if [ -f .php_server.pid ]; then
        kill "$(cat .php_server.pid)"
        rm .php_server.pid
    fi
}
trap cleanup EXIT

# Run PHP tests
vendor/bin/phpunit

# Run Node tests
npm run test:puppeteer
npm run test:playwright
