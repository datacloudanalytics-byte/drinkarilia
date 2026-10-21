#!/usr/bin/env bash
# Quick checks: PHP syntax, JSON validity, CSS brace balance.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
for f in $(find "$ROOT/arilia" -name '*.php'); do php -l "$f" | grep -v 'No syntax errors' || true; done
for f in $(find "$ROOT/arilia" -name '*.json'); do python3 -c "import json,sys; json.load(open(sys.argv[1]))" "$f" && echo "ok  $f"; done
python3 - "$ROOT/arilia/assets/css/arilia.css" <<'PY'
import sys; s=open(sys.argv[1]).read(); assert s.count('{')==s.count('}'), 'unbalanced braces'; print('ok ', sys.argv[1], s.count('{'), 'rules')
PY
