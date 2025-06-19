#!/bin/bash
set -euo pipefail

CSS_DIR="assets/css"
JS_DIR="assets/js"

echo "Minifying CSS files..."
find "$CSS_DIR" -type f -name '*.css' ! -name '*.min.css' | while read -r css; do
  out="${css%.css}.min.css"
  npx cleancss -o "$out" "$css"
  echo "  Created $out"
done

echo "Minifying JS files..."
find "$JS_DIR" -type f -name '*.js' ! -name '*.min.js' | while read -r js; do
  out="${js%.js}.min.js"
  npx terser "$js" -c -m -o "$out"
  echo "  Created $out"
done

echo "Minification complete."
