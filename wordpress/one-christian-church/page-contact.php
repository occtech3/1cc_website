<?php
/**
 * Page template for the "Contact" page (slug: contact)
 *
 * Generated from src/pages/contact.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'adults', true ); ?></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="hero-logo"><div class="logo3d logo3d-page" aria-hidden="true" data-tilt><span class="l3-glow"></span><div class="l3-tilt"><div class="l3-spin"><svg class="l3 l3-back" viewBox="34 11.5 174 174" style="--i:9"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:8;--c:#150D36"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:7;--c:#291764"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:6;--c:#3D2091"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:5;--c:#512ABF"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:4;--c:#6933DC"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:3;--c:#863BE8"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:2;--c:#A343F3"><use href="#occ-logo"/></svg><svg class="l3 l3-side" viewBox="34 11.5 174 174" style="--i:1;--c:#C04BFF"><use href="#occ-logo"/></svg><svg class="l3 l3-front" viewBox="34 11.5 174 174" style="--i:0"><use href="#occ-logo"/></svg><svg class="l3 l3-sheen" viewBox="34 11.5 174 174" style="--i:-0.3"><use href="#occ-logo"/></svg></div></div></div></div>
  <div class="wrap hero-copy">
    <p class="kicker">Contact</p>
    <h1>We'd love to <span class="grad-text">hear from you.</span></h1>
    <p class="lead">Reach out to plan a visit, ask a question or share a prayer request.</p>
  </div>
  <button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<section class="section">
  <div class="wrap split" style="align-items:start">
    <div class="reveal from-left">
      <ul class="contact-list">
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg><div><h3>Visit</h3><p>2578 Lawrence Dr.<br>Greenwood, IN 46143</p></div></li>
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg><div><h3>Call</h3><p><a href="tel:+13177878236">317-787-8236</a></p></div></li>
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><div><h3>Email</h3><p><a href="mailto:onecc.office@gmail.com">onecc.office@gmail.com</a></p></div></li>
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg><div><h3>Office hours</h3><p>Monday &ndash; Friday, 9am &ndash; 5pm</p></div></li>
      </ul>
      <div class="map-frame" style="aspect-ratio:16/10"><iframe title="Map to One Christian Church" loading="lazy" src="https://www.google.com/maps?q=2578+Lawrence+Dr,+Greenwood,+IN+46143&amp;output=embed"></iframe></div>
    </div>
    <form class="form-card reveal from-right" data-mock-form>
      <h3>Send us a message</h3>
      <div class="form-row">
        <div class="field"><label for="c-name">Name</label><input id="c-name" required></div>
        <div class="field"><label for="c-email">Email</label><input id="c-email" type="email" required></div>
      </div>
      <div class="field"><label for="c-topic">I'd like to&hellip;</label><select id="c-topic"><option>Plan a visit</option><option>Ask a question</option><option>Share a prayer request</option><option>Get connected to a ministry</option><option>Something else</option></select></div>
      <div class="field"><label for="c-msg">Message</label><textarea id="c-msg" rows="5"></textarea></div>
      <button class="btn btn-sun" type="submit">Send message <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      <p class="form-ok" hidden>Thanks! This is a preview, so nothing was sent.</p>
    </form>
  </div>
</section>

</main>
<?php
get_footer();
