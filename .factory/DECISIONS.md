# Decisions and current facts

## Hosting and domain
- Firebase Hosting, project `kuontam-website`, Spark (no-cost) plan; owner account luchuowen@gmail.com.
  No billing account is linked; keep it that way.
- Default URL https://kuontam-website.web.app. Custom domain kuontam.navac.co.ke via one CNAME
  `kuontam → kuontam-website.web.app.` (TTL 3600) in DirectAdmin (da10.host-ww.net).
- navac.co.ke is delegated to dan1/dan2.host-ww.net, but the zone's own NS records name
  ns1/ns2.server-102-218-215-52.da.direct, which REFUSE queries. Firebase domain verification stalled on
  this (2026-10-01). Fix belongs to the owner/host; do not edit the zone from this project.
- Deploys so far ran from Google Cloud Shell (`~/kuontam`), firebase-tools 15.x. CI only verifies.

## Page (public/index.html), approved by the owner
- Direction "D1 Schematic": drafting-sheet look, 40px grid, red wire progress bar.
- Hero diagram: "B1 Circuit · Live readouts" (rounded cards, pulse current, kW chips, segmented
  battery, breathing LEDs, outage cycle every 10s after 5.5s). Load cards link to the Systems rows.
  Note line centred under the graphic; footer readouts centred.
- Red band with four icon items: Smart lighting, Gate automation, Biometric access, IP CCTV.
- Systems: accordion rows with "wired components" nodes + sticky image wipe. No section lead paragraph.
- Sectors: four expanding panels, auto-cycle 4.5s on desktop until hovered. Owner: keep as is, never revert.
- FAQ: one open at a time. Survey: three-step stepper form, vertically centred with its copy.
- Mobile menu (≤960px): "M2 Thumb sheet" — floating Menu pill at the bottom opens a bottom card with
  four icon tiles + full-width survey button; page dims/blurs; pill hides while #survey is on screen.
  Replaced the old burger + full-screen dark drawer. Nav logo centred on phones.
- Footer: mobile collapsible Systems/Company; bar "© 2026 Kuontam Systems | Designed by NAVAC GLOBAL"
  — the whole "NAVAC GLOBAL" is the link (https://navac.co.ke), colour #ff6170, no underline.
- Removed on request: cursor X/Y readout, side page rail, "Sheet NN ·" labels, "Hover a part…" hint,
  contact Email/Post block, "They share power, cable routes and controls…" line.

## Assets
- Photos: Gemini-generated, upscaled with Real-ESRGAN, served as `img/A-0N_4k.jpg` web-sized.
- og.jpg 1200×630 built from `source/og-image.html`; favicon = red mark, 192px.

## Known gaps (follow-ups, not yet requested)
- Survey form only shows a success message; it does not send the lead anywhere.
