#!/bin/bash
set -euo pipefail

# Build the forum app using the package.json script
npm --prefix frontend/forum-app run build

# Copy build output to assets directory if a dist folder exists
if [ -d frontend/forum-app/dist ]; then
  cp -r frontend/forum-app/dist/* assets/forum-app/
fi
