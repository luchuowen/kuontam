#!/usr/bin/env bash
# factory-check: gates | quick | full. CI runs `full`, so local and CI agree on "green".
set -euo pipefail
cd "$(dirname "$0")/.."
mode="${1:-quick}"
case "$mode" in
  gates) python3 scripts/site-check.py --gates-only ;;
  quick) python3 scripts/site-check.py ;;
  full)  python3 scripts/site-check.py --full ;;
  *) echo "usage: factory-check.sh gates|quick|full" >&2; exit 64 ;;
esac
