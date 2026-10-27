#!/usr/bin/env python3
"""Static build for the One Christian Church design preview.

src/parts/*.html  -> shared template parts (WordPress: parts/header.html, parts/footer.html)
src/pages/*.html  -> one file per page; front matter, then '---', then body
Outputs <name>.html at the repo root.

Run:  python3 build.py
"""
import html
import re
from pathlib import Path

ROOT = Path(__file__).parent
SRC = ROOT / "src"
SITE_NAME = "One Christian Church"

# Photos are served from the live site until they are moved into the new WordPress media library.
IMG = "https://i0.wp.com/1christianchurch.com/wp-content/uploads/"

SHELL = """<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title}</title>
<meta name="description" content="{description}">
<meta property="og:title" content="{title}">
<meta property="og:description" content="{description}">
<meta name="theme-color" content="#1B2430">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

PAGE_HERO = """<section class="page-hero{photo_class}"{style}>
  <div class="wrap">
    <p class="eyebrow">{eyebrow}</p>
    <h1>{heading}</h1>
    <p class="lead">{lead}</p>
  </div>
</section>
"""

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
    "menu": '<path d="M4 7h16M4 12h16M4 17h16"/>',
    "close": '<path d="M6 6l12 12M18 6 6 18"/>',
    "facebook": '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
    "instagram": '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8"/>',
    "youtube": '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9 5 3-5 3z"/>',
}

MARK = """<svg class="mark" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="currentColor"/><path d="M24 11v26M16 19h16" stroke="var(--mark-ink)" stroke-width="3.2" stroke-linecap="round"/></svg>"""


def icon(name: str) -> str:
    return (
        '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
        f'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{ICONS[name]}</svg>'
    )


def parse(path: Path):
    raw = path.read_text()
    head, body = raw.split("\n---\n", 1)
    meta = {}
    for line in head.strip().splitlines():
        k, v = line.split(":", 1)
        meta[k.strip()] = v.strip()
    return meta, body


def render(text: str, nav: str = "") -> str:
    text = text.replace("{{mark}}", MARK)
    text = text.replace("{{img}}", IMG)
    text = re.sub(r"\{\{icon:(\w+)\}\}", lambda m: icon(m.group(1)), text)
    text = re.sub(r"\{\{cur:(\w+)\}\}", lambda m: "is-current" if m.group(1) == nav else "", text)
    return text


def main():
    header = (SRC / "parts/header.html").read_text()
    footer = (SRC / "parts/footer.html").read_text()
    pages = sorted((SRC / "pages").glob("*.html"))
    for p in pages:
        slug = p.stem
        meta, body = parse(p)
        hero = ""
        if meta.get("heading"):
            photo = meta.get("photo", "")
            hero = PAGE_HERO.format(
                heading=meta["heading"],
                lead=meta.get("lead", ""),
                eyebrow=meta.get("eyebrow", SITE_NAME),
                photo_class=" has-photo" if photo else "",
                style=f' style="--hero-img:url(\'{{{{img}}}}{photo}\')"' if photo else "",
            )
        title = meta["title"] if slug == "index" else f"{meta['title']} | {SITE_NAME}"
        out = SHELL.format(
            title=html.escape(title),
            description=html.escape(meta.get("description", "")),
            slug=slug,
            header=header,
            hero=hero,
            body=body,
            footer=footer,
        )
        (ROOT / f"{slug}.html").write_text(render(out, meta.get("nav", "")))
    print(f"built {len(pages)} pages")


if __name__ == "__main__":
    main()
