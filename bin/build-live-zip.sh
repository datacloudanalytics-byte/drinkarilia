#!/usr/bin/env bash
# Builds dist/Arilia-WordPress-Theme-1.12.zip from arilia-live/ (the 1.x theme line that runs on drinkarilia.com).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"; TMP="$(mktemp -d)"; cp -r "$ROOT/arilia-live" "$TMP/arilia"
V="$(grep -m1 '^Version:' "$ROOT/arilia-live/style.css" | awk '{print $2}')"
( cd "$TMP" && zip -qr "$ROOT/dist/Arilia-WordPress-Theme-${V%.*}.zip" arilia ); rm -rf "$TMP"; echo "Built dist/Arilia-WordPress-Theme-${V%.*}.zip ($V)"
