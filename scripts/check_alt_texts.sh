#!/bin/bash
set -euo pipefail

# Scan HTML and PHP files for <img> tags without an alt attribute.
# Usage: ./scripts/check_alt_texts.sh [directory]

SEARCH_DIR="${1:-.}"

# Find lines with <img> tags and filter those lacking the alt attribute.
output=$(grep -rHn --exclude-dir=vendor --include='*.php' --include='*.html' '<img[^>]*>' "$SEARCH_DIR" | grep -v 'alt=' || true)

if [[ -n "$output" ]]; then
    echo "The following <img> tags are missing alt attributes:"
    echo "$output"
    exit 1
else
    echo "All <img> tags contain alt attributes."
fi
