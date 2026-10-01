---
name: factory
description: Factory status, init and migrate for this repo (playbook docs/Software_Factory_Playbook.md). Use for "/factory status", "/factory migrate", or "update the environment using the new playbook".
disable-model-invocation: true
---
- `status`: print `playbook_version` and `applied_migrations` from `.factory/manifest.json`, line counts of
  CLAUDE.md and .factory/DECISIONS.md against `caps`, open artifacts, and `bash scripts/factory-check.sh gates`.
- `migrate`: read `docs/Software_Factory_Playbook.md` §9; apply only `## Migration from` entries newer than
  `playbook_version`, in order. Replace factory-owned files (hooks, skills, reviewer agent, factory-check.sh,
  changes/TEMPLATE.md); merge into project-owned files (CLAUDE.md, DECISIONS.md, manifest, rules,
  site-check.py, deploy-review agent). Prove each hook by piping sample tool JSON into it. Run
  `factory-check full`, bump `playbook_version`, append to `applied_migrations`, write a change artifact.
