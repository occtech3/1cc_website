<?php
/**
 * The parts around the page content: header menu, promo strip, brand text, header button, search shortcuts and footer.
 *
 *   Appearance > Menus          "Main menu (header)" and "Search shortcuts" (created on activation)
 *   Appearance > Customize      Header (promo strip, brand text, button), Forms, Background videos
 *   Appearance > Patterns       "Site footer", a synced pattern edited like a page
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

/**
 * A header setting, falling back to the theme's default.
 *
 * @param string $key Key in theme-data.json "header".
 * @return string
 */
function occ_header_mod( $key ) {
	return (string) get_theme_mod( 'occ_' . $key, occ_data()['header'][ $key ] ?? '' );
}

/**
 * Print the main menu. Without one assigned, the theme's default links show.
 */
function occ_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav-list',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="nav-list">';
	foreach ( occ_data()['menus']['primary'] as $item ) {
		printf(
			'<li><a class="nav-link%1$s" href="%2$s">%3$s</a></li>',
			is_page( $item['slug'] ) ? ' is-current' : '',
			esc_url( occ_url( $item['slug'], $item['anchor'] ) ),
			esc_html( $item['title'] )
		);
	}
	echo '</ul>';
}

add_filter(
	'nav_menu_link_attributes',
	function ( $atts, $item, $args ) {
		if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$atts['class'] = trim( 'nav-link ' . ( $item->current ? 'is-current' : '' ) );
		}
		return $atts;
	},
	10,
	3
);

/**
 * Print the quick links shown when search opens.
 */
function occ_search_quick() {
	$locations = get_nav_menu_locations();
	$items     = empty( $locations['search'] ) ? false : wp_get_nav_menu_items( $locations['search'] );
	if ( $items ) {
		foreach ( $items as $item ) {
			if ( ! $item->menu_item_parent ) {
				printf( '<a href="%s">%s</a>', esc_url( $item->url ), esc_html( $item->title ) );
			}
		}
		return;
	}
	foreach ( occ_data()['menus']['search'] as $item ) {
		printf( '<a href="%s">%s</a>', esc_url( occ_url( $item['slug'], $item['anchor'] ) ), esc_html( $item['title'] ) );
	}
}

/**
 * Print the coloured strip above the header. Empty text hides it.
 */
function occ_promo_strip() {
	$text = occ_header_mod( 'promo_text' );
	if ( '' === trim( $text ) ) {
		return;
	}
	$link = occ_header_mod( 'promo_link' );
	printf(
		'<div class="promo-strip">%1$s%2$s</div>',
		esc_html( $text ),
		$link ? ' <a href="' . esc_url( occ_link( occ_header_mod( 'promo_url' ) ) ) . '">' . esc_html( $link ) . '</a>' : ''
	);
}

/**
 * Print the two lines of text next to the logo.
 */
function occ_brand_text() {
	printf(
		'<span class="brand-text"><span class="brand-name">%1$s</span><span class="brand-sub">%2$s</span></span>',
		esc_html( occ_header_mod( 'brand_name' ) ),
		esc_html( occ_header_mod( 'brand_sub' ) )
	);
}

/**
 * Print the button at the end of the menu. An empty label hides it.
 */
function occ_header_cta() {
	$label = occ_header_mod( 'cta_label' );
	if ( '' === trim( $label ) ) {
		return;
	}
	printf( '<a class="btn btn-sun nav-cta" href="%1$s">%2$s</a>', esc_url( occ_link( occ_header_mod( 'cta_url' ) ) ), esc_html( $label ) );
}

/**
 * ID of the "Site footer" synced pattern, or 0.
 *
 * @return int
 */
function occ_footer_block_id() {
	$id   = (int) get_option( 'occ_footer_block' );
	$post = $id ? get_post( $id ) : null;
	return ( $post && 'wp_block' === $post->post_type && 'publish' === $post->post_status ) ? $id : 0;
}

/**
 * Print the footer: the "Site footer" pattern, or the theme's design if the pattern was deleted.
 */
function occ_footer() {
	$id      = occ_footer_block_id();
	$content = $id ? get_post_field( 'post_content', $id ) : occ_design_content( 'footer' );
	echo do_shortcode( do_blocks( $content ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- block content, as the_content() prints it.
}

/**
 * Customizer: header text and button, form recipient, background videos.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'occ_header',
			array(
				'title'       => __( 'Header', 'one-christian-church' ),
				'description' => __( 'The menu itself is in Appearance > Menus. Links can be a page slug (new-here, about#leaders) or a full address.', 'one-christian-church' ),
				'priority'    => 25,
			)
		);
		$fields = array(
			'promo_text' => __( 'Promo strip text (leave empty to hide the strip)', 'one-christian-church' ),
			'promo_link' => __( 'Promo strip link text', 'one-christian-church' ),
			'promo_url'  => __( 'Promo strip link', 'one-christian-church' ),
			'brand_name' => __( 'Name next to the logo', 'one-christian-church' ),
			'brand_sub'  => __( 'Small line under the name', 'one-christian-church' ),
			'cta_label'  => __( 'Button text (leave empty to hide the button)', 'one-christian-church' ),
			'cta_url'    => __( 'Button link', 'one-christian-church' ),
		);
		foreach ( $fields as $key => $label ) {
			$wp_customize->add_setting(
				'occ_' . $key,
				array(
					'default'           => occ_data()['header'][ $key ],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control( 'occ_' . $key, array( 'label' => $label, 'section' => 'occ_header', 'type' => 'text' ) );
		}

		$wp_customize->add_section(
			'occ_forms',
			array(
				'title'       => __( 'Forms', 'one-christian-church' ),
				'description' => __( 'The Contact and Plan a Visit forms email this address. Every message is also kept under Messages in the dashboard.', 'one-christian-church' ),
				'priority'    => 26,
			)
		);
		$wp_customize->add_setting( 'occ_form_email', array( 'default' => occ_data()['form_email'], 'sanitize_callback' => 'sanitize_email' ) );
		$wp_customize->add_control( 'occ_form_email', array( 'label' => __( 'Send form messages to', 'one-christian-church' ), 'section' => 'occ_forms', 'type' => 'email' ) );

		$wp_customize->add_section(
			'occ_videos',
			array(
				'title'       => __( 'Background videos', 'one-christian-church' ),
				'description' => __( 'Upload each MP4 in Media > Add New, then paste its URL here. A slot left blank shows the animated colour background. Short, muted clips of 10 to 30 seconds at 720p work best. Every video spot cycles through all the clips.', 'one-christian-church' ),
				'priority'    => 30,
			)
		);
		foreach ( occ_data()['videos'] as $slot => $video ) {
			$wp_customize->add_setting( 'occ_video_' . $slot, array( 'sanitize_callback' => 'esc_url_raw' ) );
			$wp_customize->add_control(
				'occ_video_' . $slot,
				array(
					'label'   => $video['label'],
					'section' => 'occ_videos',
					'type'    => 'url',
				)
			);
		}
		$wp_customize->add_setting( 'occ_preview_badge', array( 'default' => false, 'sanitize_callback' => 'wp_validate_boolean' ) );
		$wp_customize->add_control(
			'occ_preview_badge',
			array(
				'label'   => __( 'Show the "Design preview" note (for a staging site)', 'one-christian-church' ),
				'section' => 'occ_videos',
				'type'    => 'checkbox',
			)
		);
	}
);
