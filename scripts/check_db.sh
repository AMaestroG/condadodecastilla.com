#!/bin/bash
set -euo pipefail

# Simple check for PostgreSQL connectivity and environment variable

DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-}"
DB_NAME="${DB_NAME:-}"
DB_USER="${DB_USER:-}"

if [ -z "$DB_HOST" ]; then
  echo "DB_HOST environment variable is not set" >&2
  exit 1
fi

if [ -z "$DB_PORT" ]; then
  echo "DB_PORT environment variable is not set" >&2
  exit 1
fi

if [ -z "$DB_NAME" ]; then
  echo "DB_NAME environment variable is not set" >&2
  exit 1
fi

if [ -z "$DB_USER" ]; then
  echo "DB_USER environment variable is not set" >&2
  exit 1
fi

if [ -z "$CONDADO_DB_PASSWORD" ]; then
  echo "CONDADO_DB_PASSWORD environment variable is not set" >&2
  exit 1
fi

if ! command -v pg_isready >/dev/null 2>&1; then
  echo "pg_isready command not found" >&2
  exit 1
fi

PGPASSWORD="$CONDADO_DB_PASSWORD" pg_isready -h "$DB_HOST" -p "$DB_PORT" -d "$DB_NAME" -U "$DB_USER" >/dev/null 2>&1
if [ $? -eq 0 ]; then
  echo "Database is running and environment variable works"
else
  echo "Failed to connect to database" >&2
  exit 1
fi
