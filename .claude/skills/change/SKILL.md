---
name: change
description: Open and size a change. Use at the start of any non-trivial request ("/change <slug>").
---
1. Size it from the paths it touches (`.factory/manifest.json`): trivial (one-sentence diff, no critical
   path) → no artifact, just build; critical (touches `critical_paths`) → plan mode and `/effort high`;
   else standard.
2. Copy `.factory/changes/TEMPLATE.md` to `.factory/changes/<YYYY-MM-DD>-<slug>.md`.
3. Fill Intent with the ask, the visible outcome, out-of-scope, and every known constraint (cost, DNS,
   brand, approved sections from DECISIONS.md). Ask the owner only at a genuine fork; otherwise state the
   assumption on the first line and continue.
4. Fill Spec (acceptance checks include 390px + 1300px) and Plan (each step with its verification).
5. Design changes: build options in a separate preview first; edit `public/` only after the owner picks.
