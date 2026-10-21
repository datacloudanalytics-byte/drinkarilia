#!/usr/bin/env bash
# Builds the installable theme ZIP: dist/Arilia-WordPress-Theme.zip
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION="$(grep -m1 '^Version:' "$ROOT/arilia/style.css" | awk '{print $2}')"
mkdir -p "$ROOT/dist"
OUT="$ROOT/dist/Arilia-WordPress-Theme.zip"
rm -f "$OUT"
for f in $(find "$ROOT/arilia" -name '*.php'); do php -l "$f" >/dev/null || { echo "PHP syntax error in $f"; exit 1; }; done
( cd "$ROOT" && zip -qr "$OUT" arilia -x 'arilia/.DS_Store' 'arilia/**/.DS_Store' '*/node_modules/*' )
echo "Built $OUT (theme version $VERSION)"
unzip -l "$OUT" | tail -1
