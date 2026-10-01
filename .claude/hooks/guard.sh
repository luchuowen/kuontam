#!/usr/bin/env bash
# PreToolUse(Edit|Write|MultiEdit): protected paths, append-only dirs, wasteful whole-file Writes.
set -uo pipefail
root="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
export FACTORY_EVENT="$(cat)"
python3 - "$root" <<'PY'
import json, sys, fnmatch, subprocess, os
root = sys.argv[1]
try: ev = json.loads(os.environ.get("FACTORY_EVENT") or "{}")
except ValueError: ev = {}
ti = ev.get("tool_input", {}) or {}
tool = ev.get("tool_name", "")
path = ti.get("file_path") or ti.get("path") or ""
if not path: sys.exit(0)
rel = os.path.relpath(os.path.abspath(path), root)
m = json.load(open(os.path.join(root, ".factory/manifest.json")))
def block(msg): print(f"factory guard: {msg}", file=sys.stderr); sys.exit(2)
for pat in m.get("protected", []):
    if fnmatch.fnmatch(rel, pat):
        block(f"{rel} is protected (manifest.protected). Ask the owner to name this exact file first.")
for d in m.get("append_only_dirs", []):
    if rel.startswith(d.rstrip("/") + "/"):
        r = subprocess.run(["git", "-C", root, "cat-file", "-e", f"HEAD:{rel}"], capture_output=True)
        if r.returncode == 0: block(f"{rel} is in append-only {d}; add a new file instead.")
if tool == "Write":
    full = os.path.join(root, rel)
    if os.path.exists(full):
        old = open(full, errors="ignore").read().splitlines()
        new = (ti.get("content") or "").splitlines()
        if len(old) >= 150:
            changed = len(set(new) ^ set(old)) / 2
            if changed < 0.25 * len(old):
                block(f"Write re-emits {len(old)} lines to change ~{int(changed)}; use Edit.")
sys.exit(0)
PY
