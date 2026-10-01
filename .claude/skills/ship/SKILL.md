---
name: ship
description: Learn, commit, push and open the PR for a verified change ("/ship").
disable-model-invocation: true
---
1. Artifact must say Status: verified. Add 0–3 bullets to `## Learned`; copy any still-true,
   expensive-to-rediscover fact into `.factory/DECISIONS.md` (keep under its cap; compact old entries into
   `.factory/history/`).
2. Set Status: shipped. Commit diff + artifact together on a branch; push `-u origin`; open a PR with the
   artifact's Intent and Verification. Never merge, never force-push.
3. Deploy is separate and owner-approved: `firebase deploy --only hosting --project kuontam-website`,
   then `curl -sI` both live URLs and record the result.
