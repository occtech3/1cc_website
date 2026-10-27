<?php
/**
 * Site header: promo strip and navigation.
 *
 * @package One_Christian_Church
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#120B2B">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<div class="promo-strip">New here? We saved you a seat this Sunday at 10:00. <a href="<?php echo esc_url( occ_url( 'new-here', '' ) ); ?>">Plan your visit &rarr;</a></div>
<header class="site-header" data-header>
  <div class="wrap header-row">
    <a class="brand" href="<?php echo esc_url( occ_url( 'home', '' ) ); ?>" aria-label="One Christian Church home">
      <?php occ_logo(); ?>
      <span class="brand-text"><span class="brand-name">One Christian</span><span class="brand-sub">Church &middot; Greenwood</span></span>
    </a>
    <nav class="primary-nav" id="primary-nav" aria-label="Main">
      <ul class="nav-list">
        <li><a class="nav-link <?php occ_current( 'about' ); ?>" href="<?php echo esc_url( occ_url( 'about', '' ) ); ?>">About</a></li>
        <li><a class="nav-link <?php occ_current( 'ministries' ); ?>" href="<?php echo esc_url( occ_url( 'ministries', '' ) ); ?>">Ministries</a></li>
        <li><a class="nav-link <?php occ_current( 'events' ); ?>" href="<?php echo esc_url( occ_url( 'events', '' ) ); ?>">Events</a></li>
        <li><a class="nav-link <?php occ_current( 'listen' ); ?>" href="<?php echo esc_url( occ_url( 'listen', '' ) ); ?>">Watch &amp; Listen</a></li>
        <li><a class="nav-link <?php occ_current( 'give' ); ?>" href="<?php echo esc_url( occ_url( 'give', '' ) ); ?>">Give</a></li>
        <li><a class="nav-link <?php occ_current( 'contact' ); ?>" href="<?php echo esc_url( occ_url( 'contact', '' ) ); ?>">Contact</a></li>
      </ul>
      <a class="btn btn-sun nav-cta" href="<?php echo esc_url( occ_url( 'new-here', '' ) ); ?>">Plan a Visit</a>
    </nav>
    <button class="menu-toggle" type="button" aria-label="Open menu" aria-controls="primary-nav" aria-expanded="false" data-menu-toggle><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
  </div>
</header>
