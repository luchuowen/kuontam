# Decisions and current facts

## Hosting and domain
- Client domain kuontamsystems.co.ke (bought by the owner at HostPinnacle, 2026-10-02) is the primary URL;
  canonical/OG point there. Company email: info@kuontamsystems.co.ke (footer + profile p.8).
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
- Company profile: only entry point is the footer Company column link "Company profile (PDF)". It opens an
  in-page viewer (page images public/profile/p-N.jpg) with the Download PDF button in the viewer bar.
  PDF = digital brochure (8 pp). Images not iframe: mobile browsers do not render embedded PDFs reliably.
- Footer: mobile collapsible Systems/Company; bar "© 2026 Kuontam Systems | Designed by NAVAC GLOBAL"
  — the whole "NAVAC GLOBAL" is the link (https://navac.co.ke), colour #ff6170, no underline.
- Removed on request: cursor X/Y readout, side page rail, "Sheet NN ·" labels, "Hover a part…" hint,
  contact Email/Post block, "They share power, cable routes and controls…" line.

## Assets
- Photos: Gemini-generated, upscaled with Real-ESRGAN, served as `img/A-0N_4k.jpg` web-sized.
- og.jpg 1200×630 built from `source/og-image.html`; favicon = red mark, 192px.

## Survey form
- Submits to public/send.php on the cPanel host. PHP mail() is disabled there, so it delivers over SMTP to localhost:25 (info@kuontamsystems.co.ke, From website@).
- Email design: "Spec sheet" (picked 2026-10-02), HTML in public/email-template.php plus a plain-text part; ref KS-yymmdd-XXXX, Nairobi time, logo from /img/logo_red_w.png.
- cPanel uploads: files must be 0644 and folders 0755 (extracted zips came out 0600/0700 and broke images/fonts).
  Guards: honeypot `company_site`, min 3s fill time, 5 requests/IP/hour. CORS allows kuontamsystems.co.ke,
  www, kuontam.navac.co.ke and kuontam-website.web.app; the Firebase copy posts cross-origin to it.
  send.php and email-template.php are excluded from Firebase deploys (firebase.json ignore).

## Known gaps (follow-ups, not yet requested)
- "WhatsApp us" button in the survey section links to #survey; it needs the business WhatsApp number.

## SEO basics (2026-10-02)
- robots.txt + sitemap.xml at site root; JSON-LD (schema.org Electrician) in <head>.
- Google Search Console: URL-prefix property https://kuontamsystems.co.ke/ under luchuowen@gmail.com, verified by public/google3955a567f2ad3ef1.html. Never delete that file.
- Sitemap submitted and homepage indexing requested on 2026-10-02.
- SEO pass (2026-10-02): keyword title/description, twitter tags, apple-touch-icon, og:locale en_KE, JSON-LD @graph (business + 5 services + WebSite).
- Images: responsive WebP (700/900 and 1100/1600 wide) via srcset with JPEG fallback; originals *_4k.jpg kept only as masters (not referenced). Logos served at 96px.
- 404.html (noindex) + public/.htaccess (https/bare-domain redirect, ErrorDocument, 30-day image/font cache). .htaccess lives in the repo now; upload it with the site.
- Firebase (kuontam.navac.co.ke, web.app) 301-redirects every path to https://kuontamsystems.co.ke/ so Google sees one site.
- Lighthouse (local): Performance 95, Accessibility 100, Best Practices 100, SEO 100.
- Phone / WhatsApp: 0712 219 038 (+254712219038). Used in survey section (WhatsApp + Call), footer, mobile menu, form error fallback, JSON-LD telephone + contactPoint.
- WhatsApp: in-page chat window (W1, picked 2026-10-02) opened by any [data-wa] link; topic chips + name + message, then hands a prefilled message to wa.me/254712219038 in a new tab/app. wa.me href kept as no-JS fallback.
- Contact fields (2026-10-02): WhatsApp window requires name, phone, email (topic chips 3x2, full-width greeting); survey form now requires email too. send.php validates email, adds it to the email and sets Reply-To to the client so hitting Reply in the inbox answers them; email has Call / WhatsApp / Email buttons.
