<?php
/**
 * Shortcodes for the design's decorations, used inside the page content.
 *
 *   [occ_icon name="arrow"] [occ_icon name="users" size="lg"]   line icons (names: see inc/fragments.json)
 *   [occ_video slot="kids"] [occ_video slot="hero" eager="1"]  background video (Appearance > Customize > Background videos)
 *   [occ_logo3d kind="hero|page|cta|badge"]                    the turning 3D logo
 *   [occ_ring text="Love God • Love People •"]                 words running round a circle
 *   [occ_wave] [occ_wave top="1"] [occ_wave color="night"]     the wavy edge at the bottom of a section
 *   [occ_aurora] [occ_mark] [occ_logo] [occ_map q="address"]
 *   [occ_form type="contact|visit"]                            see inc/forms.php
 *
 * Every Group block with the classes "section dark" also gets the drifting logos in its background.
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

add_shortcode(
	'occ_icon',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'name' => 'arrow', 'size' => '' ), $atts );
		$set  = 'lg' === $atts['size'] ? 'icons_lg' : 'icons';
		return occ_fragments()[ $set ][ $atts['name'] ] ?? '';
	}
);

add_shortcode(
	'occ_video',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'slot' => '', 'eager' => '' ), $atts );
		return occ_video_html( $atts['slot'], (bool) $atts['eager'] );
	}
);

add_shortcode(
	'occ_logo3d',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'kind' => 'page' ), $atts );
		return occ_fragments()['logo3d'][ $atts['kind'] ] ?? '';
	}
);

add_shortcode(
	'occ_ring',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'text' => '' ), $atts );
		return str_replace( '{text}', esc_html( $atts['text'] ), occ_fragments()['ring'] );
	}
);

add_shortcode(
	'occ_wave',
	function ( $atts ) {
		$atts  = shortcode_atts( array( 'top' => '', 'color' => '' ), $atts );
		$color = sanitize_key( $atts['color'] );
		return strtr(
			occ_fragments()['wave'],
			array(
				'{cls}'   => $atts['top'] ? ' top' : '',
				'{style}' => $color ? ' style="--wave:var(--' . $color . ')"' : '',
			)
		);
	}
);

add_shortcode(
	'occ_aurora',
	function () {
		return occ_fragments()['aurora'];
	}
);

add_shortcode(
	'occ_mark',
	function () {
		return occ_fragments()['mark'];
	}
);

add_shortcode( 'occ_logo', 'occ_logo_html' );

add_shortcode(
	'occ_map',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'q' => occ_fragments()['map_query'] ), $atts );
		return str_replace( '{q}', esc_attr( str_replace( ' ', '+', $atts['q'] ) ), occ_fragments()['map'] );
	}
);

/**
 * Drifting 3D logos behind each dark section, as in the design. They're decoration, so they aren't in the content.
 */
add_filter(
	'render_block_core/group',
	function ( $html, $block ) {
		static $count = 0;
		$classes = ' ' . ( $block['attrs']['className'] ?? '' ) . ' ';
		if ( is_admin() || false === strpos( $classes, ' section ' ) || false === strpos( $classes, ' dark ' ) ) {
			return $html;
		}
		$layouts = occ_fragments()['drift'];
		$drift   = $layouts[ $count++ % count( $layouts ) ];
		return preg_replace( '/^(\s*<[a-z]+\b[^>]*>)/', '$1' . str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), $drift ), $html, 1 );
	},
	10,
	2
);
