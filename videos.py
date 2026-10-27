#!/usr/bin/env python3
"""Put the downloaded background clips where the site expects them.

  python3 videos.py --open          open the 11 Mixkit clip pages in your browser (click Download on each, 720p)
  python3 videos.py ~/Downloads     find those downloads by clip number and copy them to docs/assets/video/<slot>.mp4
  python3 videos.py                 list which clips are in place and which are still missing

Mixkit's terms don't allow scripted downloads, so the downloading itself stays a click on each page.
"""
import re
import shutil
import sys
import webbrowser
from pathlib import Path

from build import SITE, VIDEOS

VIDEO_DIR = SITE / "assets" / "video"
BIG = 40 * 1024 * 1024  # GitHub warns above 50 MB per file; the 720p downloads are far smaller


def page(slug: str) -> str:
    return f"https://mixkit.co/free-stock-video/{slug}/"


def status():
    missing = [(slot, slug) for slot, (_, slug, _) in VIDEOS.items() if not (VIDEO_DIR / f"{slot}.mp4").exists()]
    print(f"{len(VIDEOS) - len(missing)} of {len(VIDEOS)} clips in {VIDEO_DIR}")
    for slot, slug in missing:
        print(f"  missing {slot}.mp4  <-  {page(slug)}")
    return missing


def place(folder: Path):
    files = [f for f in folder.iterdir() if f.is_file() and f.suffix.lower() == ".mp4"]
    VIDEO_DIR.mkdir(parents=True, exist_ok=True)
    for slot, (clip, _, label) in VIDEOS.items():
        # Mixkit names its downloads after the clip number, e.g. mixkit-...-486-hd-ready.mp4 or 486-720.mp4
        hits = [f for f in files if re.search(rf"(?<!\d){clip}(?!\d)", f.name)]
        if not hits:
            continue
        src = max(hits, key=lambda f: f.stat().st_mtime)
        dest = VIDEO_DIR / f"{slot}.mp4"
        shutil.copy2(src, dest)
        note = "  (large: the 720p download is plenty)" if dest.stat().st_size > BIG else ""
        print(f"  {src.name}  ->  {dest.relative_to(SITE.parent)}   {label}{note}")


def main():
    args = sys.argv[1:]
    if args == ["--open"]:
        for _, slug, _ in VIDEOS.values():
            webbrowser.open_new_tab(page(slug))
        return
    if args:
        folder = Path(args[0]).expanduser()
        if not folder.is_dir():
            sys.exit(f"Not a folder: {folder}")
        place(folder)
    if not status():
        print("All set. Commit docs/assets/video/ and push, and every page plays its video.")


if __name__ == "__main__":
    main()
