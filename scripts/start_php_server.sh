#!/usr/bin/env bash
# Simple helper to start PHP built-in server if php is available.

if ! command -v php >/dev/null 2>&1; then
  echo "Warning: php executable not found; skipping PHP server startup." >&2
  exit 0
fi

php -S localhost:8080 >/dev/null 2>&1 &
echo $! > .php_server.pid

