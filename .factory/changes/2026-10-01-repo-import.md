# 2026-10-01 — repo-import

Size: standard   ·   Status: shipped

## Intent
- Ask: put everything that makes up the live website into github.com/luchuowen/kuontam, and make the
  repository conform to the Software Factory Playbook v2.1.
- User-visible outcome: repo contains the deployable site (`public/`, `firebase.json`) plus the factory.
- Out of scope: content/design changes; automatic deploy from CI (would need a token; owner decision).
- Constraints: zero monthly cost; never touch navac.co.ke records other than the kuontam CNAME.

## Spec
- `public/` is byte-identical to the version deployed on 2026-10-01 (B1 diagram, NAVAC GLOBAL credit).
- Factory installed per playbook §2 (Mode B on an existing static site; no typecheck, so `quick` runs
  gates + a site integrity check instead).

## Plan
1. Assemble site files from the deploy package — `site-check.py` finds no missing references.
2. Install factory files; wire hooks in `.claude/settings.json` — each hook fed sample JSON.
3. `factory-check full` green — output below.

## Verification
- `bash scripts/factory-check.sh full` → green (gates 5/5, caps ok, refs ok, images within cap).
- Hooks: guard blocks `.firebaserc` edit (exit 2) and passes `public/index.html` Edit (exit 0);
  stop-gate exits 0 on a clean tree; session-context prints the open artifact list.

## Learned
- No typecheck exists for this site; `quick` = gates + `scripts/site-check.py`.
