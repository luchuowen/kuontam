---
paths:
  - public/**
---
# public/ reference

- One file does the page: `public/index.html` (CSS in `<style>`, JS at the end). Edit in place with
  small Edits; the guard blocks whole-file rewrites.
- Tokens (`:root`): --paper #F4F2EE · --paper2 #ECE8E2 · --ink #231F20 · --red #BF1E2E · --red2 #9E1724 ·
  --mute #6B6662 · --line #D3CDC5 · --line2 #C4BDB4 · --ok #1F7A45 · --ease cubic-bezier(.2,.75,.15,1).
- Type: Archivo (display) + IBM Plex Mono (labels), from `fonts.css` → `fonts/*.woff2`.
- Scroll reveals: add `.rv` (optional `data-d` stagger); an IntersectionObserver adds `.in`.
- Mobile: ≤700px is the phone layout (centred headings, collapsible footer columns); check at 390px.
- Respect `prefers-reduced-motion` for any new animation.
- Hero diagram (`figure#dwg`): SVG viewBox 560×450; `.cp` pulses use pathLength=100 + dasharray 7 93;
  `[data-on]/[data-off]` text swaps and `[data-kon]/[data-koff]` kW readouts flip in `setOut()`.
  `.lk[data-k]` cards open Systems row k.
- `og.jpg` must stay 1200×630 (rebuild from `source/og-image.html`); `<head>` OG URLs use
  https://kuontam.navac.co.ke/.
