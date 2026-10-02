# Kuontam Systems website

Single-page website for Kuontam Systems Limited, a Nairobi building-systems contractor (solar & electrical, automation, access control & CCTV, fire detection, structured cabling).

- Live: https://kuontam-website.web.app (domain: https://kuontamsystems.co.ke; old: kuontam.navac.co.ke)
- Hosting: Firebase Hosting (Spark plan), project `kuontam-website`

## Structure

```
public/           Deployed site
  index.html      The whole page (HTML, CSS and JS inline)
  fonts.css       Archivo + IBM Plex Mono, self-hosted
  fonts/          woff2 font files
  img/            Photography, logos, favicon
  og.jpg          1200×630 social share image
source/           Source for the OG image
scripts/          factory-check.sh (gates | quick | full) and site-check.py
.factory/         Software Factory: manifest, DECISIONS.md, change artifacts, history
.claude/          Claude Code settings, hooks, rules, skills and agents
docs/             Software Factory Playbook v2.1
firebase.json     Hosting config (cache headers)
.firebaserc       Default Firebase project
```

## Verify

```
bash scripts/factory-check.sh full
```

CI runs the same command on every push and PR.

## Deploy

```
npm i -g firebase-tools
firebase login
firebase deploy --only hosting
```

## Local preview

```
npx serve public
```

Designed by [NAVAC GLOBAL](https://navac.co.ke).
