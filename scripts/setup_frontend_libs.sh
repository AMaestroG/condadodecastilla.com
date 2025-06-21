#!/bin/bash
set -euo pipefail

JS_DIR="assets/vendor/js"
CSS_DIR="assets/vendor/css"

mkdir -p "$JS_DIR" "$CSS_DIR"

npm install bootstrap jquery tailwindcss

cp node_modules/jquery/dist/jquery.min.js "$JS_DIR/"
cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js "$JS_DIR/"
cp node_modules/bootstrap/dist/css/bootstrap.min.css "$CSS_DIR/"

cat <<MSG
Bibliotecas instaladas vía npm y copiadas a:
  - $JS_DIR/jquery.min.js
  - $JS_DIR/bootstrap.bundle.min.js
  - $CSS_DIR/bootstrap.min.css
Ejecuta 'npm run build' para generar tailwind.min.css
MSG
