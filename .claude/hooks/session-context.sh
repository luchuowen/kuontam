#!/usr/bin/env bash
# SessionStart(compact|resume): restore the open change artifact, changed files and governing rules.
set -uo pipefail
root="$(git rev-parse --show-toplevel 2>/dev/null)" || exit 0
cd "$root"
echo "## Factory context (re-injected)"
open=$(grep -L "Status: shipped" .factory/changes/2*.md 2>/dev/null || true)
if [ -n "$open" ]; then
  for f in $open; do echo "- open artifact: $f ($(wc -l < "$f") lines) — read it before continuing"; done
else echo "- no open change artifact"; fi
base=$(git merge-base HEAD origin/main 2>/dev/null || git rev-list --max-parents=0 HEAD 2>/dev/null | tail -1)
changed=$( (git diff --name-only "$base" 2>/dev/null; git diff --name-only; git ls-files -o --exclude-standard) | sort -u)
[ -n "$changed" ] && echo "- changed files:" && echo "$changed" | sed 's/^/  - /'
python3 - "$changed" <<'PY'
import sys, re, fnmatch, glob
changed = [c for c in sys.argv[1].split("\n") if c]
for rf in sorted(glob.glob(".claude/rules/*.md")):
    head = open(rf).read().split("---")
    pats = re.findall(r"^\s*-\s*['\"]?([^'\"\n]+)", head[1], re.M) if len(head) > 2 else []
    if any(fnmatch.fnmatch(c, p) for c in changed for p in pats):
        print(f"- rule governing changed files: {rf} — re-read it")
PY
echo "- invariants: zero monthly cost; only the kuontam CNAME on navac.co.ke; approved sections stay (DECISIONS.md)"
