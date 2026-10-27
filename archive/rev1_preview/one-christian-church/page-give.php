<?php
/**
 * Page template for the "Give" page (slug: give)
 *
 * Generated from src/pages/give.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'guitar', true ); ?></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker">Give</p>
    <h1>Generosity <span class="grad-text">builds the church.</span></h1>
    <p class="lead">This ministry exists because of people like you. Your generosity keeps building up the church.</p>
  </div>
  <button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<section class="section">
  <div class="wrap big-cards">
    <div class="big-card reveal" style="--bg:var(--grad-sun);--fg:var(--night)"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/></svg><h3>Give online</h3><p>Give once or set up recurring giving securely through Realm.</p><a class="btn btn-night" href="https://onrealm.org/OneChristianChu/give/now" target="_blank" rel="noopener">Give now <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div>
    <div class="big-card reveal" style="--bg:var(--grad-electric)"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v11H9l-5 4z"/><path d="M8 9h8M8 12h5"/></svg><h3>Text to give</h3><p>Text <strong>oneccgive</strong> to <strong>73256</strong> and follow the link to give from your phone.</p><a class="btn btn-glass" href="sms:73256?&amp;body=oneccgive">Open messages</a></div>
    <div class="big-card reveal" style="--bg:var(--grad-ocean);--fg:var(--night)"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><h3>In person or mail</h3><p>Cash and checks are collected at Sunday service, or mail to:<br><strong>One Christian Church, 2578 Lawrence Dr., Greenwood, IN 46143</strong></p></div>
  </div>
</section>
<section class="section dark">
  <div class="wrap center reveal zoom">
    <blockquote class="verse">&ldquo;Each of you should give what you have decided in your heart to give, not reluctantly or under compulsion, for God loves a cheerful giver.&rdquo;<cite>2 Corinthians 9:7</cite></blockquote>
  </div>
</section>

</main>
<?php
get_footer();
