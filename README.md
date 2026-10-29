# One Christian Church: website redesign preview

A bold, colourful redesign of [1christianchurch.com](https://1christianchurch.com/) for client feedback, inspired by
eclife.org, thecreek.org and itownchurch.com: full-screen background video, a scroll-driven video story,
video ministry cards, marquees, wave dividers and big condensed type.

One set of sources builds two deliverables:

- `docs/`: static preview pages (open `docs/index.html`). GitHub Pages publishes this folder.
- `wordpress/one-christian-church/`: an installable classic WordPress theme. Activating it writes the design into
  the pages (Home, Plan a Visit, About, Ministries, Events, Watch & Listen, Give, Contact) as ordinary block-editor
  content, so staff edit the text, photos and FAQs in WordPress. It also sets up the menus, the footer pattern,
  the forms and the search. Switching to another theme puts the site's previous pages back. Details for the
  people who run the site are in the theme's `readme.txt` and, once installed, Appearance > Site guide.

## Edit and rebuild

Page sources are in `src/pages/`, the shared header and footer in `src/parts/`. Then:

```
./package.sh      # runs build.py, then zips both deliverables into dist/
```

Do not edit the generated `docs/*.html`, or in the theme `header.php`, `footer.php`, `404.php`, `inc/content/`,
`inc/forms/`, `inc/theme-data.json` and `inc/fragments.json`; `build.py` overwrites them.

## How the pages become WordPress blocks

`blocks.py` turns each page source into two things from one normalised copy: the static HTML in `docs/` and the
block markup the theme writes into the WordPress page (`inc/content/<page>.html`). Paragraphs, headings, lists,
quotes, FAQs (`<details>`) and photos become their own blocks; sections and cards become Group blocks that keep
their CSS classes. Decorative parts (SVG art, video frames, marquees) become Custom HTML blocks, which the editor
labels "Design element". Placeholders in the sources (`{{icon:x}}`, `{{video:slot}}`, `{{form:contact}}`,
`{{wave}}`, `{{map}}`...) become `[occ_...]` shortcodes in WordPress, so the markup they print stays in the theme.
`build.py` stops with an error if a source uses markup the converter cannot map, rather than producing blocks the
editor would mark as invalid.

The theme side lives in `wordpress/one-christian-church/inc/`: `content.php` (install, switch back, reset a page),
`site-parts.php` (menu, header settings, footer pattern), `forms.php`, `search.php`, `shortcodes.php` and
`admin.php` (the Site guide screen).

## Forms

The Contact and Plan a Visit forms are defined in `FORMS` in `build.py`. On the static preview they are
demonstrations. In WordPress they email the address in Customize > Forms, keep a copy under Messages, and have
spam protection (hidden field, minimum time, rate limit).

## Videos

Background video files live in `docs/assets/video/` (`build.py` leaves that folder alone). The file names, the
Mixkit page for each placeholder clip and the licence notes are in [CREDITS.md](CREDITS.md). Download the clips by
hand; the site never streams from Mixkit. A missing file shows the animated colour background.

```
python3 videos.py --open        # opens the 11 clip pages: click Download (720p) on each
python3 videos.py ~/Downloads   # finds the downloads by clip number, saves them as docs/assets/video/<slot>.mp4
```

Then commit `docs/assets/video/` and push. In WordPress,
upload the clips to Media and paste their URLs in Appearance > Customize > Background videos. Videos play muted,
only while on screen, and respect "reduce motion"; every video hero has a Pause button.

## Publish on GitHub Pages

Push this folder as a repository, then in the repository's Settings > Pages choose "Deploy from a branch",
branch `main`, folder `/docs`.

## Logo

`assets/img/logo-mark.svg` is a vector redraw of the church's circle-and-cross logo. In WordPress, setting
a logo under Appearance > Customize > Site Identity replaces it in the header and footer.

The animated 3D logo (home and page heroes, the "Come as you are" band, and the logos drifting up through every
dark section) is built from the same file: `build.py` stacks copies of it in CSS 3D, with a moving colour gradient
on the front. It leans toward the mouse, pauses off screen, and holds still for visitors who prefer reduced motion.

## Search

The magnifying glass in the header (or `/`, or Ctrl+K) opens a search over every page. For the static preview
`build.py` indexes each page's headings and text into `docs/assets/search-index.json`. In WordPress the theme
builds the same index from the published pages (`/wp-json/occ/v1/search-index`) and rebuilds it whenever a page
is saved. Pressing Enter when nothing matches falls through to the normal WordPress search, which also finds posts.

## Open items for the church

- Leader bios (the photos of Jerry, Dena, and Devon & Jordyn are in `assets/img/leaders/`).
- House Church details; next GriefShare start date.
- Their own video footage to replace the placeholders.
- An events calendar plugin, if they want one.

## Brand

Night `#120B2B`, cream `#FFF7EE`, pink `#FF2D87`, orange `#FF7A1A`, yellow `#FFC93C`, violet `#7B3FF2`,
aqua `#18D1C0`, sky `#2E9BFF`, lime `#B6F03C`, indigo `#4F5BFF`; logo blue `#BEDCEB`. Display type Anton, script Caveat, body Plus Jakarta Sans.
