<?php
/**
 * Page template for the "Watch & Listen" page (slug: listen)
 *
 * Generated from src/pages/listen.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'mic', true ); ?></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="hero-logo"><div class="logo3d logo3d-page" aria-hidden="true" data-tilt><span class="l3-glow"></span><div class="l3-tilt"><div class="l3-spin"><svg class="l3 l3-back" viewBox="0 0 174 174" style="--i:9"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:8;--c:#150D36"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:7;--c:#291764"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:6;--c:#3D2091"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:5;--c:#512ABF"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:4;--c:#6933DC"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:3;--c:#863BE8"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:2;--c:#A343F3"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="0 0 174 174" style="--i:1;--c:#C04BFF"><use href="#occ-logo"/></svg><svg class="l3 l3-front" viewBox="0 0 174 174" style="--i:0"><use href="#occ-logo"/></svg><svg class="l3 l3-sheen" viewBox="0 0 174 174" style="--i:-0.3"><use href="#occ-logo"/></svg></div></div></div></div>
  <div class="wrap hero-copy">
    <p class="kicker">Watch &amp; listen</p>
    <h1>Join us from <span class="grad-text">anywhere.</span></h1>
    <p class="lead">Worship with us live on Facebook, catch up on YouTube, or hear Pastor Jerry on Freedom 95 Radio.</p>
  </div>
  <button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<section class="section">
  <div class="wrap big-cards">
    <a class="big-card reveal" style="--bg:var(--grad-electric)" href="https://www.facebook.com/OneChristianministry" target="_blank" rel="noopener"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l6-3.5z"/></svg><h3>Watch live</h3><p>Sunday services stream live on our Facebook page at 10:00am.</p><span class="btn btn-glass">Open Facebook <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
    <a class="big-card reveal" style="--bg:var(--grad-sun);--fg:var(--night)" href="https://www.freedom95.us/" target="_blank" rel="noopener"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M7 8 17 3"/><circle cx="15" cy="14" r="3"/><path d="M6 12h3M6 16h3"/></svg><h3>Freedom 95</h3><p>Listen to Pastor Jerry on Freedom 95.9, Sundays at 12:00.</p><span class="btn btn-night">Listen now <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
    <a class="big-card reveal" style="--bg:var(--grad-ocean);--fg:var(--night)" href="https://www.youtube.com/channel/UCYVLSh8gjUrTDR09XfRN5Dg" target="_blank" rel="noopener"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10 9 5 3-5 3z"/></svg><h3>YouTube</h3><p>Subscribe to our channel for messages and worship.</p><span class="btn btn-night">Open YouTube <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
  </div>
  <div class="wrap"><p class="placeholder-note reveal">Preview note: once sermons are posted regularly, this page can show the latest message embedded right here, plus a searchable archive.</p></div>
</section>

</main>
<?php
get_footer();
