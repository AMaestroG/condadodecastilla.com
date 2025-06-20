#!/bin/bash
set -euo pipefail

# Simple check for PostgreSQL connectivity and environment variable

DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-}"
DB_NAME="${DB_NAME:-}"
DB_USER="${DB_USER:-}"

missing=""
for var in DB_HOST DB_PORT DB_NAME DB_USER; do
  if [ -z "${!var}" ]; then
    missing="$missing $var"
  fi
done
if [ -n "$missing" ]; then
  echo "Missing required environment variables:$missing" >&2
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
