<?php
/**
 * Page template for the "Plan a Visit" page (slug: new-here)
 *
 * Generated from src/pages/new-here.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'youth', true ); ?></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker">New here?</p>
    <h1>We saved you <span class="grad-text">a seat.</span></h1>
    <p class="lead">Welcome to One Christian Church! Here's everything you need for your first Sunday.</p>
  </div>
  <button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<section class="section">
  <div class="wrap visit-grid">
    <a class="big-card reveal" style="--bg:var(--grad-electric)" href="#expect"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><h3>When</h3><p>Sunday Morning Fellowship at 9:00am.<br>Worship service at 10:00am.</p></a>
    <a class="big-card reveal" style="--bg:var(--grad-sun);--fg:var(--night)" href="https://maps.google.com/?q=2578+Lawrence+Dr,+Greenwood,+IN+46143" target="_blank" rel="noopener"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg><h3>Where</h3><p>2578 Lawrence Dr.<br>Greenwood, IN 46143</p><span class="text-link" style="color:var(--night)">Get directions <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
    <a class="big-card reveal" style="--bg:var(--grad-ocean);--fg:var(--night)" href="#plan"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="9" width="18" height="12" rx="1.5"/><path d="M3 13h18M12 9v12"/><path d="M12 9c-2-4-6-4-6-1.5S9 9 12 9c3 0 6-.5 6-1.5S14 5 12 9"/></svg><h3>First time?</h3><p>Stop by the Welcome Center across from the main entrance. We have a gift bag for you.</p></a>
  </div>
</section>

<section class="section paper" id="expect">
  <div class="wrap split" style="align-items:start">
    <div class="reveal">
      <p class="kicker">What to expect</p>
      <h2>Come<br><span class="grad-electric-text">as you are.</span></h2>
      <p style="font-size:1.1rem">We're ordinary people who love Jesus and love having guests. You don't need to know the songs, dress up or have it all together.</p>
    </div>
    <div class="faq reveal">
      <details open><summary>What happens during the service?</summary><p>Our service starts at 10:00 with worship led by our worship team, followed by Bible-centered teaching from Pastor Jerry.</p></details>
      <details><summary>What about my kids?</summary><p>Infants through Kindergarten (First Steps) and 1st&ndash;5th grade (OCC Kids) have their own classes during the 10:00 service. Students in 6th&ndash;12th grade meet on Wednesday nights at 6:30.</p></details>
      <details><summary>Where do I go when I arrive?</summary><p>Head to the Welcome Center across from the main entrance. It's staffed before and after service, and our team can show you around.</p></details>
      <details><summary>Can I watch online first?</summary><p>Absolutely. We stream live on <a href="https://www.facebook.com/OneChristianministry" target="_blank" rel="noopener">Facebook</a>, and you can hear Pastor Jerry on Freedom 95 Radio on Sundays at 12:00.</p></details>
      <details><summary>Who do I ask if I have more questions?</summary><p>Call the office at <a href="tel:+13177878236">317-787-8236</a> (M&ndash;F, 9am&ndash;5pm), email <a href="mailto:onecc.office@gmail.com">onecc.office@gmail.com</a>, or use our <a href="<?php echo esc_url( occ_url( 'contact', '' ) ); ?>">contact form</a>.</p></details>
    </div>
  </div>
</section>

<section class="section" id="plan">
  <div class="wrap split" style="align-items:stretch">
    <div class="map-frame reveal from-left"><iframe title="Map to One Christian Church" loading="lazy" src="https://www.google.com/maps?q=2578+Lawrence+Dr,+Greenwood,+IN+46143&amp;output=embed"></iframe></div>
    <form class="form-card reveal from-right" data-mock-form>
      <h3>Let us know you're coming</h3>
      <p class="muted">We'll have someone ready to greet you and help with kids' check-in.</p>
      <div class="form-row">
        <div class="field"><label for="v-name">Name</label><input id="v-name" required></div>
        <div class="field"><label for="v-email">Email</label><input id="v-email" type="email" required></div>
      </div>
      <div class="form-row">
        <div class="field"><label for="v-date">Which Sunday?</label><input id="v-date" type="date"></div>
        <div class="field"><label for="v-kids">Bringing kids?</label><select id="v-kids"><option>No</option><option>Yes, infants&ndash;K</option><option>Yes, 1st&ndash;5th grade</option><option>Yes, both</option></select></div>
      </div>
      <button class="btn btn-sun" type="submit">Plan my visit <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
      <p class="form-ok" hidden>Thanks! This is a preview, so nothing was sent.</p>
    </form>
  </div>
</section>

</main>
<?php
get_footer();
