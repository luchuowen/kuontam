---
name: verify
description: Prove a change works before claiming done ("/verify").
---
1. `bash scripts/factory-check.sh full` — must be GREEN.
2. Serve `public/` and screenshot the touched sections at 1300px and 390px; look at them.
3. Spawn the `reviewer` agent; for critical changes also `deploy-review`. Fix every finding or record why not.
4. Write commands, results and findings into the artifact's `## Verification`; set Status: verified.
