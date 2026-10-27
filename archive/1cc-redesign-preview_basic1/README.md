# One Christian Church — website redesign preview

Static HTML/CSS/JS preview of a redesigned [1christianchurch.com](https://1christianchurch.com/), for client feedback.
It is the design pass for the WordPress rebuild; nothing here needs a server. All text, times, people and links come from the current site.

## Edit and rebuild

Pages live in `src/pages/` and the shared header/footer in `src/parts/`. After editing, run:

```
python3 build.py
```

That regenerates every `*.html` file at the repo root. Publish with GitHub Pages (Settings → Pages → deploy from branch `main`, folder `/`).

## Photos

Photos are loaded straight from the current site's media library (`IMG` in `build.py`) so the preview shows the church's real pictures.
For the WordPress build they move into the new media library.

## Open items for the church

- House Church: the current site has no description, meeting days or locations.
- GriefShare: the listed dates are from last winter; the next session's start date is needed.
- Staff bios (the current staff social links are placeholders).
- The Events page should pull from a calendar so dates stay current.

## How this maps to WordPress

| Preview | WordPress block theme |
|---|---|
| `assets/css/style.css` `:root` tokens | `theme.json` colour palette, fonts, spacing |
| `src/parts/header.html`, `footer.html` | `parts/header.html`, `parts/footer.html` template parts |
| Events page | The Events Calendar plugin (or the church's Realm calendar) |
| Give page | Existing Realm giving link and text-to-give, unchanged |
| Plan a Visit and Contact forms | Fluent Forms, WPForms or Gravity Forms |
| Watch & Listen | Facebook Live / YouTube embeds, or a sermon plugin such as Sermon Manager |

## Brand

Ink `#1B2430`, warm cream `#FBF7F1`, terracotta accent `#B5592F` and gold `#E3B26B`. Headings in Fraunces, body in Inter.
