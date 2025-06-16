#!/bin/bash
set -euo pipefail

JS_DIR="assets/vendor/js"
CSS_DIR="assets/vendor/css"

mkdir -p "$JS_DIR" "$CSS_DIR"

# jQuery
JQUERY_VERSION="3.7.1"
JQUERY_URL="https://code.jquery.com/jquery-${JQUERY_VERSION}.min.js"

curl -L "$JQUERY_URL" -o "$JS_DIR/jquery.min.js"

# Bootstrap
BOOTSTRAP_VERSION="5.3.2"
BOOTSTRAP_BASE="https://cdn.jsdelivr.net/npm/bootstrap@${BOOTSTRAP_VERSION}/dist"

curl -L "$BOOTSTRAP_BASE/js/bootstrap.bundle.min.js" -o "$JS_DIR/bootstrap.bundle.min.js"
curl -L "$BOOTSTRAP_BASE/css/bootstrap.min.css" -o "$CSS_DIR/bootstrap.min.css"

# Tailwind CSS
TAILWIND_VERSION="3.4.4"
TAILWIND_URL="https://cdn.jsdelivr.net/npm/tailwindcss@${TAILWIND_VERSION}/tailwind.min.css"
curl -L "$TAILWIND_URL" -o "$CSS_DIR/tailwind.min.css"

cat <<MSG
Librerías descargadas en:
  - $JS_DIR/jquery.min.js
  - $JS_DIR/bootstrap.bundle.min.js
  - $CSS_DIR/bootstrap.min.css
  - $CSS_DIR/tailwind.min.css
MSG
