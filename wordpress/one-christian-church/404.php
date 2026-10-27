<?php
/**
 * Page not found
 *
 * Generated from src/pages/404.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker">404</p>
    <h1>This page <span class="grad-text">wandered off.</span></h1>
    <p class="lead">The page you're looking for isn't here. Try the home page or plan a visit.</p>
  </div>
  
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<section class="section"><div class="wrap center"><a class="btn btn-sun" href="<?php echo esc_url( occ_url( 'home', '' ) ); ?>">Back to home <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div></section>

</main>
<?php
get_footer();
