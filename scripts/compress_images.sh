#!/bin/bash
set -euo pipefail

# Usage: ./scripts/compress_images.sh [source_dir] [output_dir] [max_width]
# Requires ImageMagick's `convert` command.
# Compresses images and generates thumbnails preserving aspect ratio.

SRC_DIR="${1:-assets/img}"
OUT_DIR="${2:-${SRC_DIR}/thumbnails}"
MAX_WIDTH="${3:-800}"

mkdir -p "$OUT_DIR"

find "$SRC_DIR" -maxdepth 1 -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' -o -iname '*.webp' \) | while read -r img; do
    fname="$(basename "$img")"
    convert "$img" -auto-orient -strip -resize "${MAX_WIDTH}x${MAX_WIDTH}>" -quality 85 "$OUT_DIR/$fname"
    echo "[+] $fname -> $OUT_DIR/$fname"
done
