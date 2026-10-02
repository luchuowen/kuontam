# Kuontam Systems website — agent rules (factory v2.1)

Static one-page site for Kuontam Systems Limited (Nairobi building-systems contractor).
Everything ships from `public/`; `public/index.html` holds the HTML, CSS and JS inline.

## Commands
- verify quick: `bash scripts/factory-check.sh quick` · full: `bash scripts/factory-check.sh full`
- preview: `npx serve public` (or `python3 -m http.server -d public 8080`)
- deploy (owner-run, critical): `firebase deploy --only hosting --project kuontam-website`
- live health: `curl -sI https://kuontam-website.web.app/` · `https://kuontamsystems.co.ke/`

## Loop
1. `/change <slug>` sizes the work and opens `.factory/changes/…` (trivial = no artifact).
2. Critical paths (`.factory/manifest.json`: hosting config, CI) → plan mode,
   `/effort high`, `deploy-review` agent.
3. Build. The Stop hook runs the quick gate; a red gate is a blocker, not a note.
4. `/verify` before claiming done; `/ship` to learn, commit, push, open the PR. Agents never merge.

## Working style
- Scope = the ask: pre-existing bugs and nearby cleanups go in the summary as follow-ups, not the diff.
- Tests sized like their neighbours; scratch checks are not new test files.
- Batch every read/search/command that does not depend on another's result into one response.
- Own the whole mission: the owner is usually not watching; never ask permission for work already
  requested; end the turn only when done or blocked on input only the owner has.
- Design changes: show options in a separate preview first; the owner picks, then edit `public/`.

## Invariants without a mechanical check
- Zero monthly cost: Firebase Spark plan, no billing account, no paid services or APIs.
- DNS lives in the owner's navac.co.ke DirectAdmin zone, which also serves bms.navac.co.ke
  (production). Only the `kuontam` CNAME belongs to this project; never change any other record.
- No invented facts about Kuontam (clients, certifications, numbers). Diagram kW figures are illustrative.
- Brand: paper/ink/red tokens in `:root`; Archivo + IBM Plex Mono, self-hosted. No new fonts or colours.
- Never revert a section the owner has approved (see `.factory/DECISIONS.md`).

## Memory
- `.factory/DECISIONS.md` is current truth; read it before assuming something is unbuilt.
- `/ship` appends what was learned; `factory-check` fails when it exceeds its cap → compact.
- After `/compact`/resume the SessionStart hook names the rule files to re-read; skills re-load by name.
