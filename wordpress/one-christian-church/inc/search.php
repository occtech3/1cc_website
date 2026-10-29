<?php
/**
 * The header search's index, built from the live pages so it stays current as they're edited.
 *
 * GET /wp-json/occ/v1/search-index returns every heading on the site's pages with the text under it, linked to
 * the nearest element with an id. It is cached, and the cache is cleared whenever a page or the footer is saved.
 * (The same rules as PageText in build.py, which indexes the static preview.)
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'occ/v1',
			'/search-index',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function () {
					$index = get_transient( 'occ_search_index' );
					if ( false === $index ) {
						$index = occ_build_search_index();
						set_transient( 'occ_search_index', $index, DAY_IN_SECONDS );
					}
					return rest_ensure_response( $index );
				},
			)
		);
	}
);

foreach ( array( 'save_post_page', 'save_post_wp_block', 'deleted_post', 'trashed_post', 'switch_theme' ) as $occ_hook ) {
	add_action(
		$occ_hook,
		function () {
			delete_transient( 'occ_search_index' );
		}
	);
}

/**
 * Index every published page: the front page first, then the theme's pages in menu order, then any others.
 * Headings on the home page that also appear on another page link to that page only.
 *
 * @return array
 */
function occ_build_search_index() {
	$order = array_merge( array( 'home' ), wp_list_pluck( occ_data()['pages'], 'slug' ) );
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
	$front = (int) get_option( 'page_on_front' );
	$rank  = function ( $page ) use ( $order, $front ) {
		if ( $page->ID === $front ) {
			return -1;
		}
		$at = array_search( $page->post_name, $order, true );
		return false === $at ? 100 : $at;
	};
	usort(
		$pages,
		function ( $a, $b ) use ( $rank ) {
			return $rank( $a ) <=> $rank( $b );
		}
	);

	global $post;
	$saved   = $post;
	$entries = array();
	foreach ( $pages as $page ) {
		$post = $page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- shortcodes read the current page.
		setup_postdata( $page );
		$html = apply_filters( 'the_content', $page->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- core filter.
		if ( ! occ_has_hero( $page ) ) {
			$html = '<h1>' . esc_html( get_the_title( $page ) ) . '</h1>' . $html;
		}
		$is_home = $page->ID === $front;
		$label   = $is_home ? __( 'Home', 'one-christian-church' ) : wp_strip_all_tags( get_the_title( $page ) );
		$slug    = $is_home ? 'home' : $page->post_name;
		foreach ( occ_page_text( $html ) as $entry ) {
			if ( '' !== $entry['h'] ) {
				$entries[] = array(
					'p' => html_entity_decode( $label, ENT_QUOTES, 'UTF-8' ),
					's' => $slug,
					'u' => $is_home ? home_url( '/' ) : get_permalink( $page ),
					'a' => $entry['a'],
					'h' => $entry['h'],
					't' => $entry['t'],
				);
			}
		}
	}
	$post = $saved; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();

	$elsewhere = array();
	foreach ( $entries as $entry ) {
		if ( 'home' !== $entry['s'] ) {
			$elsewhere[ $entry['h'] ] = true;
		}
	}
	return array_values(
		array_filter(
			$entries,
			function ( $entry ) use ( $elsewhere ) {
				return 'home' !== $entry['s'] || empty( $elsewhere[ $entry['h'] ] );
			}
		)
	);
}

/**
 * Split rendered page HTML into entries: each heading and the text under it. In a card or list item, the text
 * before the heading (an event's day and time, say) belongs to that card's entry.
 *
 * @param string $html Rendered page content.
 * @return array List of array( 'a' => anchor id, 'h' => heading, 't' => text ).
 */
function occ_page_text( $html ) {
	$doc      = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><html><body>' . $html . '</body></html>' );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	$void    = array( 'area', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr' );
	$skip    = array( 'script', 'style', 'svg', 'video', 'button', 'form', 'noscript', 'iframe' );
	$heads   = array( 'h1', 'h2', 'h3', 'summary' );
	$items   = array( 'article', 'li', 'details' );
	$classes = array( 'card', 'big-card', 'tile', 'age', 'vcard', 'event', 'leader', 'promo', 'story-panel' );

	$state = (object) array( 'stack' => array(), 'entries' => array(), 'heading' => null, 'item' => null );
	$hidden = function () use ( $state ) {
		foreach ( $state->stack as $level ) {
			if ( $level[2] ) {
				return true;
			}
		}
		return false;
	};
	$data = function ( $text ) use ( $state, $hidden ) {
		if ( $hidden() ) {
			return;
		}
		$last = count( $state->entries ) - 1;
		if ( $state->item && null === $state->item->entry ) {
			$state->item->pre[] = $text;
		} elseif ( $last >= 0 ) {
			$state->entries[ $last ]->{ $state->heading ? 'h' : 't' }[] = $text;
		}
	};
	$walk = function ( $node ) use ( &$walk, $state, $hidden, $data, $void, $skip, $heads, $items, $classes ) {
		foreach ( $node->childNodes as $child ) {
			if ( $child instanceof DOMText ) {
				$data( $child->nodeValue );
				continue;
			}
			if ( ! $child instanceof DOMElement ) {
				continue;
			}
			$tag = strtolower( $child->tagName );
			$data( ' ' );
			if ( in_array( $tag, $void, true ) ) {
				continue;
			}
			$state->stack[] = array( $tag, $child->getAttribute( 'id' ), in_array( $tag, $skip, true ) || 'true' === $child->getAttribute( 'aria-hidden' ) );
			$depth          = count( $state->stack );
			$own            = preg_split( '/\s+/', $child->getAttribute( 'class' ), -1, PREG_SPLIT_NO_EMPTY );
			if ( ! $state->item && ( in_array( $tag, $items, true ) || array_intersect( $classes, $own ) ) ) {
				$state->item = (object) array( 'depth' => $depth, 'pre' => array(), 'entry' => null );
			}
			if ( in_array( $tag, $heads, true ) && ! $state->heading && ! $hidden() ) {
				$anchor = '';
				foreach ( array_reverse( $state->stack ) as $level ) {
					if ( $level[1] ) {
						$anchor = $level[1];
						break;
					}
				}
				$entry = (object) array( 'a' => $anchor, 'h' => array(), 't' => array() );
				if ( $state->item && null === $state->item->entry ) {
					$entry->t           = $state->item->pre;
					$state->item->entry = $entry;
				}
				$state->entries[] = $entry;
				$state->heading   = $tag;
			}
			$walk( $child );
			$data( ' ' );
			array_pop( $state->stack );
			if ( $tag === $state->heading ) {
				$state->heading = null;
			}
			if ( $state->item && count( $state->stack ) < $state->item->depth ) {
				if ( null === $state->item->entry && $state->entries ) {
					$last                         = count( $state->entries ) - 1;
					$state->entries[ $last ]->t = array_merge( $state->entries[ $last ]->t, $state->item->pre );
				}
				$state->item = null;
			}
		}
	};
	$body = $doc->getElementsByTagName( 'body' )->item( 0 );
	if ( $body ) {
		$walk( $body );
	}
	$squash = function ( $parts ) {
		return trim( preg_replace( '/[\s\x{00A0}]+/u', ' ', implode( '', $parts ) ) );
	};
	return array_map(
		function ( $entry ) use ( $squash ) {
			return array( 'a' => $entry->a, 'h' => $squash( $entry->h ), 't' => $squash( $entry->t ) );
		},
		$state->entries
	);
}
