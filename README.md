# One Christian Church: website redesign preview

A bold, colourful redesign of [1christianchurch.com](https://1christianchurch.com/) for client feedback, inspired by
eclife.org, thecreek.org and itownchurch.com: full-screen background video, a scroll-driven video story,
video ministry cards, marquees, wave dividers and big condensed type.

One set of sources builds two deliverables:

- `docs/`: static preview pages (open `docs/index.html`). GitHub Pages publishes this folder.
- `wordpress/one-christian-church/`: an installable classic WordPress theme. Activating it creates the
  pages (Home, Plan a Visit, About, Ministries, Events, Watch & Listen, Give, Contact) and sets Home as
  the front page. Each page renders from its `page-{slug}.php` template.

## Edit and rebuild

Page sources are in `src/pages/`, the shared header and footer in `src/parts/`. Then:

```
./package.sh      # runs build.py, then zips both deliverables into dist/
```

Do not edit the generated `docs/*.html` or `wordpress/one-christian-church/page-*.php`, `front-page.php`,
`header.php`, `footer.php`, `404.php`; they are overwritten by `build.py`.

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

## Open items for the church

- Staff photos and bios (leaders show initials for now).
- House Church details; next GriefShare start date.
- Their own video footage to replace the placeholders.
- Real forms (WPForms, Fluent Forms or Gravity Forms) and an events calendar plugin.

## Brand

Night `#120B2B`, cream `#FFF7EE`, pink `#FF2D87`, orange `#FF7A1A`, yellow `#FFC93C`, violet `#7B3FF2`,
aqua `#18D1C0`, sky `#2E9BFF`, lime `#B6F03C`, indigo `#4F5BFF`; logo blue `#BEDCEB`. Display type Anton, script Caveat, body Plus Jakarta Sans.
