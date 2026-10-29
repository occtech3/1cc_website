#!/usr/bin/env python3
"""Build for the One Christian Church design preview.

src/parts/*.html  -> shared header and footer
src/pages/*.html  -> one file per page; front matter, then '---', then body

Outputs two things from the same sources:
  docs/                          static preview (open docs/index.html; GitHub Pages serves /docs)
  wordpress/one-christian-church/  classic WordPress theme (zipped to dist/ by package.sh)

Placeholders used in the sources:
  {{url:slug}} {{url:slug#id}}   link to a page        {{asset:path}}     file under assets/
  {{video:slot}} {{video:slot:eager}}  background video  {{icon:name}}      inline SVG icon
  {{logo}} church logo (falls back to {{mark}})  {{mark}} {{aurora}} {{wave}} {{wave:top}}  decorations  {{cur:slug}}  current-page class in the nav
  {{logo3d:hero|page|cta|badge}}  animated 3D logo   {{logo_defs}}  shared SVG symbol and gradients (once, in the header)
  {{search_attrs}}  page links for the header search (its index, assets/search-index.json, is built from every page)
  Every <section class="section dark"> also gets a layer of 3D logos drifting up through its background.

Run:  python3 build.py
"""
import html
import json
import re
import shutil
from html.parser import HTMLParser
from pathlib import Path

ROOT = Path(__file__).parent
SRC = ROOT / "src"
SITE = ROOT / "docs"  # GitHub Pages serves this folder (Settings > Pages > main, /docs)
THEME = ROOT / "wordpress" / "one-christian-church"
SITE_NAME = "One Christian Church"
VERSION = "0.3.0"

# Placeholder footage: Mixkit clips under the Mixkit Stock Video Free License (each clip page checked).
# Mixkit's terms don't allow linking to their files, so the site never streams from Mixkit:
# download each clip by hand from its page and save it as assets/video/<slot>.mp4 (see CREDITS.md).
# A missing file just shows the animated colour background instead.
# In WordPress, upload the clips to Media and paste their URLs in Appearance > Customize > Background videos.
VIDEOS = {
    "hero": (486, "guitarist-playing-on-stage-486", "Guitarist on stage (home hero)"),
    "love-god": (478, "a-long-haired-woman-singing-on-stage-478", "Singer on stage (story: Love God)"),
    "love-people": (4537, "a-couple-of-friends-sitting-laughing-with-their-cell-phones-4537", "Friends laughing (story: Love People)"),
    "walk": (4119, "stunning-sunset-seen-from-the-sea-4119", "Sunset over the sea (story: Walk with Jesus, About, GriefShare)"),
    "disciples": (4269, "audience-at-a-concert-4269", "Crowd at a concert (story: Make Disciples, Events, Youth band)"),
    "kids": (48264, "three-kids-running-in-a-wooden-pier-48264", "Kids running on a pier (Kids)"),
    # Same clip as love-people for now; replace youth.mp4 with the church's own youth footage when there is some.
    "youth": (4537, "a-couple-of-friends-sitting-laughing-with-their-cell-phones-4537", "Friends laughing (OCC Youth card)"),
    "adults": (41813, "date-between-two-girl-friends-in-a-coffee-shop-41813", "Friends at a coffee shop (First Sunday, Contact)"),
    "students": (4794, "students-in-a-cafeteria-discussing-an-assignment-4794", "Friends talking around a table (Adults card, Plan a Visit, House Church)"),
    "mic": (4632, "microphone-in-a-party-hall-4632", "Microphone (Watch & Listen)"),
    "guitar": (483, "playing-guitar-close-up-483", "Guitar close-up (Give)"),
}
PAGE_SLUGS = ["new-here", "about", "ministries", "events", "listen", "give", "contact"]

ICONS = {
    "arrow": '<path d="M5 12h14M13 6l6 6-6 6"/>',
    "clock": '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    "pin": '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
    "phone": '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
    "mail": '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    "play": '<circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l6-3.5z"/>',
    "radio": '<rect x="3" y="8" width="18" height="12" rx="2"/><path d="M7 8 17 3"/><circle cx="15" cy="14" r="3"/><path d="M6 12h3M6 16h3"/>',
    "heart": '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
    "coffee": '<path d="M4 9h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 11h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v3M12 3v3"/>',
    "users": '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14.2a5 5 0 0 1 5.5 4.8"/>',
    "gift": '<rect x="3" y="9" width="18" height="12" rx="1.5"/><path d="M3 13h18M12 9v12"/><path d="M12 9c-2-4-6-4-6-1.5S9 9 12 9c3 0 6-.5 6-1.5S14 5 12 9"/>',
    "book": '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 21V5M9 8h6"/>',
    "card": '<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/>',
    "text": '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 9h8M8 12h5"/>',
    "home": '<path d="M3 11 12 4l9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-5h4v5"/>',
    "hands": '<path d="M12 21c-3-2-8-5-8-10V5l4 2 4-4 4 4 4-2v6c0 5-5 8-8 10z"/>',
    "calendar": '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    "fire": '<path d="M12 22c4 0 7-3 7-7 0-5-4-7-5-11-2 2-3 4-3 6-1-1-2-2-2-4-2 2-4 5-4 9 0 4 3 7 7 7z"/>',
    "smile": '<circle cx="12" cy="12" r="9"/><path d="M8.5 14.5a4.5 4.5 0 0 0 7 0"/><path d="M9 9.5h.01M15 9.5h.01"/>',
    "sparkle": '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/>',
    "music": '<path d="M9 18V5l11-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="17" cy="16" r="3"/>',
    "menu": '<path d="M4 7h16M4 12h16M4 17h16"/>',
    "search": '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
    "facebook": '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
    "instagram": '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8"/>',
    "youtube": '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9 5 3-5 3z"/>',
}

MARK = (ROOT / "assets" / "img" / "logo-mark.svg").read_text().strip()  # the church logo, redrawn as a vector
AURORA = '<div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div>'
# The 3D logo is a stack of copies of the mark, each pushed a little further back in Z, so it turns like a solid
# object. Every copy is a <use> of one shared symbol, so the paths are only in the page once.
LOGO_VIEWBOX = re.search(r'viewBox="([^"]+)"', MARK).group(1)
# A <use> places the symbol at 0,0 of the layer, so the layers' own viewBox starts at 0,0 (with the mark's viewBox it sat off-centre)
LAYER_VIEWBOX = "0 0 " + " ".join(LOGO_VIEWBOX.split()[2:])
HOLO_STOPS = ('<stop offset="0" stop-color="#BEDCEB"/><stop offset=".17" stop-color="#18D1C0"/><stop offset=".33" stop-color="#B6F03C"/>'
              '<stop offset=".5" stop-color="#FFC93C"/><stop offset=".67" stop-color="#FF2D87"/><stop offset=".83" stop-color="#7B3FF2"/>'
              '<stop offset="1" stop-color="#4F5BFF"/>')
LOGO_DEFS = (
    '<svg class="occ-defs" width="0" height="0" aria-hidden="true" focusable="false"><defs>'
    f'<symbol id="occ-logo" viewBox="{LOGO_VIEWBOX}">{"".join(re.findall(r"<path [^>]*/>", MARK))}</symbol>'
    f'<linearGradient id="occ-holo" gradientUnits="userSpaceOnUse" x1="50" y1="25" x2="190" y2="170">{HOLO_STOPS}</linearGradient>'
    f'<linearGradient id="occ-holo-live" gradientUnits="userSpaceOnUse" x1="66" y1="42" x2="176" y2="155" spreadMethod="reflect">{HOLO_STOPS}'
    '<animateTransform attributeName="gradientTransform" type="rotate" values="0 121 98.5;360 121 98.5" dur="32s" repeatCount="indefinite"/></linearGradient>'
    '<linearGradient id="occ-sheen" gradientUnits="userSpaceOnUse" x1="34" y1="11.5" x2="208" y2="185.5">'
    '<stop offset=".4" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".7"/><stop offset=".6" stop-color="#fff" stop-opacity="0"/>'
    '<animateTransform attributeName="gradientTransform" type="translate" values="-180 -180;180 180;180 180" keyTimes="0;.3;1" dur="12s" repeatCount="indefinite"/></linearGradient>'
    '</defs></svg>')
DEPTH_COLORS = ("#C04BFF", "#5B2FD6", "#150D36")  # side colours, front to back
LOGO3D = {"hero": 9, "page": 8, "cta": 9, "badge": 5}  # depth layers per size
# Drifting logos per dark section: (left %, size px, seconds to rise, start offset 0-1, opacity, seconds per turn)
DRIFT_LAYOUTS = [
    [(6, 160, 90, .20, .42, 60), (74, 220, 110, .65, .34, 75)],
    [(10, 120, 95, .70, .40, 65), (68, 190, 105, .25, .36, 70)],
    [(4, 200, 100, .50, .34, 72), (80, 140, 90, .05, .40, 58)],
]
WAVE = ('<svg class="wave{cls}" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true">'
        '<path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>')

PAGE_HERO = """<section class="hero hero-page">
  <div class="media">{aurora}{video}</div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="hero-logo">{{{{logo3d:page}}}}</div>
  <div class="wrap hero-copy">
    <p class="kicker">{kicker}</p>
    <h1>{heading}</h1>
    <p class="lead">{lead}</p>
  </div>
  {toggle}
  <svg class="wave" style="--wave:var(--{wave})" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
"""
TOGGLE = '<button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>'

STATIC_SHELL = """<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title}</title>
<meta name="description" content="{description}">
<meta property="og:title" content="{title}">
<meta property="og:description" content="{description}">
<meta name="theme-color" content="#120B2B">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="{fonts}" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-page="{slug}">
{header}
<main id="main">
{hero}{body}
</main>
{footer}
<script src="assets/js/main.js" defer></script>
</body>
</html>
"""
FONTS = "https://fonts.googleapis.com/css2?family=Anton&family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
PREVIEW_PILL = ('<div class="preview-pill" data-preview-pill role="note"><span><b>Design preview.</b> '
                'Videos are placeholders and forms are mock-ups.</span><button type="button" aria-label="Hide this note">&times;</button></div>')


def icon(name: str) -> str:
    return ('<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
            f'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{ICONS[name]}</svg>')


def _mix(a: str, b: str, t: float) -> str:
    pa, pb = (int(a[i:i + 2], 16) for i in (1, 3, 5)), (int(b[i:i + 2], 16) for i in (1, 3, 5))
    return "#" + "".join(f"{round(x + (y - x) * t):02X}" for x, y in zip(pa, pb))


def logo_stack(depth: int, sheen: bool) -> str:
    def layer(cls, i, color=""):
        style = f"--i:{i}" + (f";--c:{color}" if color else "")
        return f'<svg class="l3 {cls}" viewBox="{LAYER_VIEWBOX}" style="{style}"><use href="#occ-logo"/></svg>'
    a, b, c = DEPTH_COLORS
    sides = []
    for i in range(depth, 0, -1):
        t = (i - 1) / max(1, depth - 1)
        sides.append(layer("l3-side", i, _mix(a, b, t * 2) if t < .5 else _mix(b, c, t * 2 - 1)))
    return layer("l3-back", depth + 1) + "".join(sides) + layer("l3-front", 0) + (layer("l3-sheen", -0.3) if sheen else "")


def logo3d(kind: str) -> str:
    big = kind != "badge"
    tilt = " data-tilt" if kind in ("hero", "page") else ""  # follows the pointer
    glow = '<span class="l3-glow"></span>' if big else ""
    return (f'<div class="logo3d logo3d-{kind}" aria-hidden="true"{tilt}>{glow}'
            f'<div class="l3-tilt"><div class="l3-spin">{logo_stack(LOGO3D[kind], big)}</div></div></div>')


def drift(n: int) -> str:
    out = []
    for x, size, rise, start, opacity, turn in DRIFT_LAYOUTS[n % len(DRIFT_LAYOUTS)]:
        out.append(f'<span class="dl" style="--x:{x}%;--s:{size}px;--t:{rise}s;--d:-{round(rise * start, 1)}s;--o:{opacity};--y:{round(85 - start * 70)}%">'
                   f'<span class="dl-spin" style="--r:{turn}s">{logo_stack(4, False)}</span></span>')
    return f'<div class="drift" aria-hidden="true">{"".join(out)}</div>'


def video_tag(slot: str, eager: bool) -> str:
    attr = "src" if eager else "data-src"
    loaded = ' data-loaded="1"' if eager else ""
    return (f'<video class="bg-video" data-slot="{slot}" muted loop playsinline preload="{"auto" if eager else "metadata"}"{loaded} aria-hidden="true">'
            f'<source {attr}="assets/video/{slot}.mp4" type="video/mp4"></video>')


def video_readme() -> str:
    lines = ["Background videos", "=================", "",
             "Save each clip in this folder under the file name shown. Download them by hand from the Mixkit page",
             "(choose the 720p download); Mixkit's terms don't allow linking to their files or scripted downloads.",
             "A missing file just shows the animated colour background. Replace any clip with the church's own footage",
             "by saving it under the same name (short, muted MP4, 10 to 30 seconds, 720p).", "",
             "Shortcut, from the project folder:",
             "  python3 videos.py --open        opens the 11 clip pages; click Download (720p) on each",
             "  python3 videos.py ~/Downloads   renames the downloads and copies them here", ""]
    for slot, (_, page, label) in VIDEOS.items():
        lines.append(f"{slot}.mp4  <-  https://mixkit.co/free-stock-video/{page}/   ({label})")
    return "\n".join(lines) + "\n"


def parse(path: Path):
    raw = path.read_text()
    head, body = raw.split("\n---\n", 1)
    meta = {}
    for line in head.strip().splitlines():
        k, v = line.split(":", 1)
        meta[k.strip()] = v.strip()
    return meta, body


class PageText(HTMLParser):
    """Splits a rendered page into search entries: each heading and the text under it, linked to the nearest element with an id.
    In a card or list item, the text before the heading (an event's day and time, say) belongs to that card's entry."""
    VOID = {"area", "br", "col", "embed", "hr", "img", "input", "link", "meta", "source", "track", "wbr"}
    SKIP = {"script", "style", "svg", "video", "button", "form", "noscript"}
    HEADS = {"h1", "h2", "h3", "summary"}
    ITEM_TAGS = {"article", "li", "details"}
    ITEM_CLASSES = {"card", "big-card", "tile", "age", "vcard", "event", "leader", "promo", "story-panel"}

    def __init__(self):
        super().__init__()
        self.stack, self.entries, self.heading, self.item = [], [], None, None

    def hidden(self) -> bool:
        return any(hide for _, _, hide in self.stack)

    def handle_starttag(self, tag, attrs):
        self.handle_data(" ")
        if tag in self.VOID:
            return
        a = dict(attrs)
        self.stack.append((tag, a.get("id"), tag in self.SKIP or a.get("aria-hidden") == "true"))
        if self.item is None and (tag in self.ITEM_TAGS or self.ITEM_CLASSES & set((a.get("class") or "").split())):
            self.item = {"depth": len(self.stack), "pre": [], "entry": None}
        if tag in self.HEADS and not self.heading and not self.hidden():
            entry = {"a": next((i for _, i, _ in reversed(self.stack) if i), ""), "h": [], "t": []}
            if self.item and not self.item["entry"]:
                entry["t"], self.item["entry"] = self.item["pre"], entry
            self.entries.append(entry)
            self.heading = tag

    def handle_startendtag(self, tag, attrs):
        pass  # <use/>, <path/> and friends never hold text

    def handle_endtag(self, tag):
        self.handle_data(" ")
        for i in range(len(self.stack) - 1, -1, -1):
            if self.stack[i][0] == tag:
                del self.stack[i:]
                break
        if tag == self.heading:
            self.heading = None
        if self.item and len(self.stack) < self.item["depth"]:
            if not self.item["entry"] and self.entries:  # a card without a heading: its text joins the entry above
                self.entries[-1]["t"] += self.item["pre"]
            self.item = None

    def handle_data(self, data):
        if self.hidden():
            return
        if self.item and not self.item["entry"]:
            self.item["pre"].append(data)
        elif self.entries:
            self.entries[-1]["h" if self.heading else "t"].append(data)


def search_index(parsed) -> list:
    """Every page's headings and text. The home page repeats a lot from the other pages, so those repeats link to the other pages only."""
    entries = []
    order = ["index", *PAGE_SLUGS]  # visitors' pages first, so an equal match lists Ministries before Events
    for slug, meta, body in sorted((p for p in parsed if p[0] in order), key=lambda p: order.index(p[0])):
        page = PageText()
        page.feed(render_static(hero_for(meta) + body, ""))
        for e in page.entries:
            h, t = (" ".join("".join(e[k]).split()) for k in ("h", "t"))
            if h:
                entries.append({"p": "Home" if slug == "index" else html.unescape(meta.get("menu", meta["title"])),
                                "s": "home" if slug == "index" else slug, "a": e["a"], "h": h, "t": t})
    elsewhere = {e["h"] for e in entries if e["s"] != "home"}
    return [e for e in entries if e["s"] != "home" or e["h"] not in elsewhere]


def static_logo() -> str:
    """Drop the church's logo at assets/img/logo.png (or .svg) and the header and footer use it."""
    for name in ("logo.svg", "logo.png"):
        if (ROOT / "assets" / "img" / name).exists():
            return f'<img class="logo-img" src="assets/img/{name}" alt="">'
    return MARK


def common(text: str) -> str:
    text = text.replace("{{mark}}", MARK).replace("{{aurora}}", AURORA).replace("{{logo_defs}}", LOGO_DEFS)
    text = re.sub(r"\{\{logo3d:(\w+)\}\}", lambda m: logo3d(m.group(1)), text)
    sections = iter(range(100))
    text = re.sub(r'(<section class="section dark"[^>]*>)', lambda m: m.group(1) + drift(next(sections)), text)
    text = text.replace("{{wave:top}}", WAVE.format(cls=" top")).replace("{{wave}}", WAVE.format(cls=""))
    return re.sub(r"\{\{icon:(\w+)\}\}", lambda m: icon(m.group(1)), text)


def render_static(text: str, nav: str) -> str:
    def url(m):
        slug, _, anchor = m.group(1).partition("#")
        page = "index.html" if slug == "home" else f"{slug}.html"
        return page + (f"#{anchor}" if anchor else "")
    text = re.sub(r"\{\{url:([\w#-]+)\}\}", url, text)
    text = re.sub(r"\{\{asset:([\w./-]+)\}\}", r"assets/\1", text)
    text = re.sub(r"\{\{video:([\w-]+)(:eager)?\}\}", lambda m: video_tag(m.group(1), bool(m.group(2))), text)
    text = re.sub(r"\{\{cur:([\w-]+)\}\}", lambda m: "is-current" if m.group(1) == nav else "", text)
    text = text.replace("{{preview_pill}}", PREVIEW_PILL).replace("{{logo}}", static_logo())
    pages = {s: "index.html" if s == "home" else f"{s}.html" for s in ["home", *PAGE_SLUGS]}
    text = text.replace("{{search_attrs}}", f'data-search-pages="{html.escape(json.dumps(pages))}"')
    return common(text)


def render_wp(text: str) -> str:
    def url(m):
        slug, _, anchor = m.group(1).partition("#")
        return f"<?php echo esc_url( occ_url( '{slug}', '{anchor}' ) ); ?>"
    text = re.sub(r"\{\{url:([\w#-]+)\}\}", url, text)
    text = re.sub(r"\{\{asset:([\w./-]+)\}\}", r"<?php echo esc_url( get_theme_file_uri( 'assets/\1' ) ); ?>", text)
    text = re.sub(r"\{\{video:([\w-]+)(:eager)?\}\}",
                  lambda m: f"<?php occ_video( '{m.group(1)}', {'true' if m.group(2) else 'false'} ); ?>", text)
    text = re.sub(r"\{\{cur:([\w-]+)\}\}", lambda m: f"<?php occ_current( '{m.group(1)}' ); ?>", text)
    text = text.replace("{{logo}}", "<?php occ_logo(); ?>")
    text = text.replace("{{search_attrs}}", 'data-search-pages="<?php echo esc_attr( wp_json_encode( occ_search_pages() ) ); ?>" data-native')
    text = text.replace("{{preview_pill}}", "<?php if ( get_theme_mod( 'occ_preview_badge', true ) ) : ?>" + PREVIEW_PILL + "<?php endif; ?>")
    return common(text)


def hero_for(meta) -> str:
    if not meta.get("heading"):
        return ""
    slot = meta.get("video", "")
    return PAGE_HERO.format(
        aurora=AURORA,
        video=f"{{{{video:{slot}:eager}}}}" if slot else "",
        toggle=TOGGLE if slot else "",
        kicker=meta.get("kicker", SITE_NAME),
        heading=meta["heading"],
        lead=meta.get("lead", ""),
        wave=meta.get("wave", "cream"),
    )


def build_static(parsed, header, footer):
    # Everything in docs/ is regenerated except docs/assets/video/ (the downloaded clips) and a custom-domain CNAME.
    if SITE.exists():
        for p in SITE.iterdir():
            if p.name not in ("assets", "CNAME"):
                shutil.rmtree(p) if p.is_dir() else p.unlink()
        for p in (SITE / "assets").iterdir() if (SITE / "assets").exists() else []:
            if p.name != "video":
                shutil.rmtree(p) if p.is_dir() else p.unlink()
    shutil.copytree(ROOT / "assets", SITE / "assets", dirs_exist_ok=True)
    (SITE / "assets" / "video").mkdir(exist_ok=True)
    (SITE / "assets" / "video" / "README.txt").write_text(video_readme())
    (SITE / ".nojekyll").write_text("")
    for slug, meta, body in parsed:
        title = meta["title"] if slug == "index" else f"{meta['title']} | {SITE_NAME}"
        out = STATIC_SHELL.format(
            title=html.escape(title), description=html.escape(meta.get("description", "")),
            fonts=FONTS, slug=slug, header=header, hero=hero_for(meta), body=body, footer=footer,
        )
        (SITE / f"{slug}.html").write_text(render_static(out, meta.get("nav", "")))


WP_HEADER = """<?php
/**
 * Site header: promo strip and navigation.
 *
 * @package One_Christian_Church
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#120B2B">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
"""
WP_FOOTER_TAIL = """<?php wp_footer(); ?>
</body>
</html>
"""
WP_PAGE = """<?php
/**
 * {label}
 *
 * Generated from src/pages/{src}.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
{content}
</main>
<?php
get_footer();
"""


def build_wp(parsed, header, footer):
    for p in THEME.glob("*.php"):
        if p.name.startswith(("page-", "front-page")) or p.name in ("header.php", "footer.php", "404.php"):
            p.unlink()
    if (THEME / "assets").exists():
        shutil.rmtree(THEME / "assets")
    shutil.copytree(ROOT / "assets", THEME / "assets")
    (THEME / "header.php").write_text(WP_HEADER + render_wp(header))
    (THEME / "footer.php").write_text(render_wp(footer) + "\n" + WP_FOOTER_TAIL)
    descriptions, titles = {}, {}
    for slug, meta, body in parsed:
        content = render_wp(hero_for(meta) + body)
        if slug == "index":
            name, label = "front-page.php", "Front page (Home)"
        elif slug == "404":
            name, label = "404.php", "Page not found"
        else:
            name, label = f"page-{slug}.php", f"Page template for the \"{meta['title']}\" page (slug: {slug})"
            titles[slug] = meta.get("menu", meta["title"])
        descriptions["home" if slug == "index" else slug] = meta.get("description", "")
        (THEME / name).write_text(WP_PAGE.format(label=label, src=slug, content=content))
    data = {
        "version": VERSION,
        "pages": [{"slug": s, "title": titles[s]} for s in PAGE_SLUGS],
        "descriptions": descriptions,
        "videos": {k: {"label": v[2]}
                   for k, v in VIDEOS.items()},
        "fonts": FONTS,
    }
    (THEME / "inc").mkdir(exist_ok=True)
    (THEME / "inc" / "theme-data.json").write_text(json.dumps(data, indent=1))


def main():
    header = (SRC / "parts/header.html").read_text()
    footer = (SRC / "parts/footer.html").read_text()
    parsed = [(p.stem, *parse(p)) for p in sorted((SRC / "pages").glob("*.html"))]
    build_static(parsed, header, footer)
    build_wp(parsed, header, footer)
    index = json.dumps(search_index(parsed), ensure_ascii=False, separators=(",", ":"))
    for folder in (SITE, THEME):
        (folder / "assets" / "search-index.json").write_text(index)
    print(f"built {len(parsed)} pages -> docs/ and wordpress/one-christian-church/")


if __name__ == "__main__":
    main()
