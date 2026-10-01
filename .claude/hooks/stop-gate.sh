#!/usr/bin/env bash
# Stop: run the quick gate when tracked source changed since the last green run; block on red.
set -uo pipefail
input="$(cat || true)"
case "$input" in *'"stop_hook_active": true'*|*'"stop_hook_active":true'*) exit 0;; esac
root="$(git rev-parse --show-toplevel 2>/dev/null)" || exit 0
cd "$root"
stamp=".factory/.last-green"
sig="$( (git diff HEAD -- public scripts firebase.json .claude .factory/manifest.json CLAUDE.md; git ls-files -o --exclude-standard -- public scripts) 2>/dev/null | sha1sum | cut -d' ' -f1)"
[ -f "$stamp" ] && [ "$(cat "$stamp")" = "$sig" ] && exit 0
if out="$(bash scripts/factory-check.sh quick 2>&1)"; then
  echo "$sig" > "$stamp"; exit 0
fi
echo "factory quick gate is RED — fix before ending the turn:" >&2
echo "$out" >&2
exit 2
