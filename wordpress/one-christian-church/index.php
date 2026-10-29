<?php
/**
 * Fallback template: blog index, archives and search.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div></div>
  <div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker"><?php bloginfo( 'name' ); ?></p>
    <h1><?php
    if ( is_search() ) {
      /* translators: %s: what the visitor searched for */
      echo esc_html( sprintf( __( 'Search: %s', 'one-christian-church' ), get_search_query( false ) ) );
    } else {
      echo esc_html( is_home() ? __( 'News', 'one-christian-church' ) : wp_strip_all_tags( get_the_archive_title() ) );
    }
    ?></h1>
  </div>
</section>
<section class="section">
  <div class="wrap cards">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'card' ); ?>>
          <h3><a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none"><?php the_title(); ?></a></h3>
          <p><?php echo esc_html( get_the_excerpt() ); ?></p>
          <span class="when"><?php echo esc_html( get_the_date() ); ?></span>
        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p><?php esc_html_e( 'Nothing here yet.', 'one-christian-church' ); ?></p>
    <?php endif; ?>
  </div>
  <div class="wrap"><?php the_posts_pagination(); ?></div>
</section>
</main>
<?php
get_footer();
