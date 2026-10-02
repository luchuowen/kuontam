#!/usr/bin/env python3
"""Factory checks for a static site. Reads .factory/manifest.json.
--gates-only : text gates + context caps
(default)    : + JSON validity, HTML well-formedness, local reference integrity
--full       : + asset budgets, required SEO/OG meta, OG image size
"""
import json, re, sys, struct
from pathlib import Path
from html.parser import HTMLParser

ROOT = Path(__file__).resolve().parent.parent
M = json.loads((ROOT / ".factory/manifest.json").read_text())
fails, notes = [], []

def files(globs, exclude=()):
    out = set()
    for g in globs:
        out |= {p for p in ROOT.glob(g) if p.is_file()}
    ex = set()
    for g in exclude:
        ex |= set(ROOT.glob(g))
    return sorted(out - ex)

def gates():
    for g in M["gates"]:
        rx = re.compile(g["pattern"])
        hits = []
        for f in files(g["paths"], g.get("exclude", [])):
            for n, line in enumerate(f.read_text(errors="ignore").splitlines(), 1):
                if rx.search(line):
                    hits.append(f"{f.relative_to(ROOT)}:{n}")
        if hits:
            fails.append(f"gate {g['name']}: " + ", ".join(hits[:5]))
    notes.append(f"gates {len(M['gates'])} checked")
    for name, cap in M["caps"].items():
        p = ROOT / name
        if p.suffix == ".md" and p.exists():
            n = len(p.read_text().splitlines())
            if n > cap:
                fails.append(f"cap {name}: {n} lines > {cap} (compact into .factory/history/)")

VOID = {"area","base","br","col","embed","hr","img","input","link","meta","source","track","wbr"}
class Tags(HTMLParser):
    def __init__(s):
        super().__init__(); s.stack=[]; s.errs=[]; s.refs=[]; s.ids=set(); s.meta={}; s.title=False
    def handle_starttag(s, t, a):
        a = dict(a)
        if "id" in a:
            if a["id"] in s.ids: s.errs.append(f"duplicate id #{a['id']}")
            s.ids.add(a["id"])
        for k in ("src", "href"):
            v = a.get(k)
            if v and not re.match(r"^(https?:|mailto:|tel:|#|data:|javascript:)", v): s.refs.append(v)
        if t == "meta":
            key = a.get("property") or a.get("name")
            if key: s.meta[key] = a.get("content", "")
        if t == "title": s.title = True
        if t not in VOID: s.stack.append((t, s.getpos()[0]))
    def handle_startendtag(s, t, a): s.handle_starttag(t, a); (t not in VOID) and s.stack.pop()
    def handle_endtag(s, t):
        if t in VOID: return
        for i in range(len(s.stack) - 1, -1, -1):
            if s.stack[i][0] == t:
                for u, ln in s.stack[i+1:]:
                    if u not in ("p", "li", "option", "path", "g"): s.errs.append(f"<{u}> line {ln} not closed before </{t}>")
                del s.stack[i:]; return
        s.errs.append(f"stray </{t}> line {s.getpos()[0]}")

def site():
    for j in files(["*.json", ".firebaserc", ".factory/*.json", ".claude/*.json"]):
        try: json.loads(j.read_text())
        except Exception as e: fails.append(f"json {j.relative_to(ROOT)}: {e}")
    pages = files(["public/**/*.html"])
    parsed = {}
    for p in pages:
        t = Tags(); t.feed(p.read_text()); t.close(); parsed[p] = t
        fails.extend(f"html {p.relative_to(ROOT)}: {e}" for e in t.errs[:5])
        for r in t.refs:
            ref = r.split("#")[0].split("?")[0]
            target = (ROOT / "public" / ref.lstrip("/")) if ref.startswith("/") else (p.parent / ref)
            if r.split("#")[0] and not target.exists():
                fails.append(f"missing ref in {p.relative_to(ROOT)}: {r}")
        for a in re.findall(r'href="#([\w-]+)"', p.read_text()):
            if a not in t.ids and a != "top": fails.append(f"anchor #{a} has no target")
    for c in files(["public/**/*.css"]):
        for u in re.findall(r"url\(['\"]?([^'\")]+)", c.read_text()):
            if not u.startswith(("data:", "http")) and not (c.parent / u).exists():
                fails.append(f"missing url() in {c.relative_to(ROOT)}: {u}")
    notes.append(f"{len(pages)} page(s) parsed, references checked")
    return parsed

def jpeg_size(p):
    b = p.read_bytes(); i = 2
    while i < len(b):
        if b[i] != 0xFF: i += 1; continue
        m = b[i+1]
        if m in (0xC0, 0xC1, 0xC2): h, w = struct.unpack(">HH", b[i+5:i+9]); return w, h
        i += 2 + struct.unpack(">H", b[i+2:i+4])[0]
    return None

def full(parsed):
    caps = M["caps"]
    total = sum(f.stat().st_size for f in files(["public/**/*"])) / 1e6
    if total > caps["public_total_mb"]: fails.append(f"public/ is {total:.1f} MB > {caps['public_total_mb']} MB")
    for img in files(["public/**/*.jpg", "public/**/*.png", "public/**/*.webp"]):
        kb = img.stat().st_size / 1024
        if kb > caps["image_max_kb"]: fails.append(f"{img.relative_to(ROOT)} {kb:.0f} KB > {caps['image_max_kb']} KB")
    idx = ROOT / "public/index.html"
    t = parsed.get(idx)
    need = ["description", "viewport", "og:title", "og:description", "og:image", "og:url", "twitter:card", "theme-color"]
    if t:
        miss = [k for k in need if not t.meta.get(k)]
        if miss: fails.append("index.html missing meta: " + ", ".join(miss))
        if not t.title: fails.append("index.html has no <title>")
    og = ROOT / "public/og.jpg"
    if og.exists() and jpeg_size(og) != (1200, 630): fails.append(f"og.jpg is {jpeg_size(og)}, expected (1200, 630)")
    notes.append(f"budgets ok check: public {total:.1f} MB")

args = sys.argv[1:]
gates()
if "--gates-only" not in args:
    parsed = site()
    if "--full" in args: full(parsed)
for n in notes: print("·", n)
if fails:
    print("RED"); [print("✗", f) for f in fails]; sys.exit(1)
print("GREEN")
