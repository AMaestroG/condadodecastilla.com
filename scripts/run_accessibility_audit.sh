#!/bin/bash
set -euo pipefail

# Pages to audit. Update this list to cover the most important routes.
PAGES=(
    "http://localhost:8080/index.php"
    "http://localhost:8080/museo/museo.php"
    "http://localhost:8080/blog.php"
    "http://localhost:8080/contacto/contacto.php"
)

OUTPUT_DIR="reports/accessibility"
mkdir -p "$OUTPUT_DIR"

# Launch a local PHP server if none is running
php -S localhost:8080 >/dev/null 2>&1 &
PHP_PID=$!
trap 'kill $PHP_PID' EXIT
sleep 2

for page in "${PAGES[@]}"; do
    fname=$(echo "$page" | sed 's|https*://||;s|/|_|g')
    echo "Auditing $page..."
    npx --yes lighthouse "$page" \
        --quiet --output html \
        --output-path "$OUTPUT_DIR/${fname}.html" >/dev/null
    echo "Saved report to $OUTPUT_DIR/${fname}.html"
done

echo "All accessibility reports generated in $OUTPUT_DIR"
