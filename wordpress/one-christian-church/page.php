<?php
/**
 * Pages. The theme's own pages start with the design's hero block; any other page gets a simple title header.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<?php
while ( have_posts() ) :
	the_post();
	if ( occ_has_hero() ) :
		the_content();
	else :
		?>
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div></div>
  <div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker"><?php bloginfo( 'name' ); ?></p>
    <h1><?php the_title(); ?></h1>
  </div>
</section>
<section class="section">
  <div class="wrap entry-content" style="max-width:820px"><?php the_content(); ?></div>
</section>
		<?php
	endif;
endwhile;
?>
</main>
<?php
get_footer();
