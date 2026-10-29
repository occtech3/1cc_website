<?php
/**
 * Putting the design into the site's pages, and taking it out again.
 *
 * Activating the theme:
 *   - remembers the front page settings, then for each design page (Home, Plan a Visit, About, ...) finds the page
 *     (for Home, the current front page if there is one; otherwise by slug) or creates it. An existing page keeps its
 *     title, and its current content, status, page template and Elementor setting are saved with it (_occ_previous)
 *   - fills each page with the design's blocks, or with the content it had when this theme was last active
 *   - makes Home the front page, creates the "Site footer" pattern and the two menus if they don't exist yet,
 *     and copies the leader photos into the Media Library
 * Switching to another theme:
 *   - keeps each page's content for next time (_occ_design_content), puts back what was saved, sets the pages
 *     the theme created to draft and restores the front page settings
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

/**
 * The design pages, key => slug and title. "home" becomes the front page.
 *
 * @return array
 */
function occ_design_pages() {
	$pages = array( 'home' => array( 'slug' => 'home', 'title' => __( 'Home', 'one-christian-church' ) ) );
	foreach ( occ_data()['pages'] as $page ) {
		$pages[ $page['slug'] ] = array( 'slug' => $page['slug'], 'title' => $page['title'] );
	}
	return $pages;
}

/**
 * The design's block content for a page (or 'footer'), with this site's links and image addresses filled in.
 *
 * @param string $key Page key.
 * @return string
 */
function occ_design_content( $key ) {
	$file = get_theme_file_path( 'inc/content/' . sanitize_key( $key ) . '.html' );
	if ( ! file_exists( $file ) ) {
		return '';
	}
	$content = file_get_contents( $file );
	// An image copied into the Media Library is linked to it, so WordPress adds its sizes and lazy loading.
	$content = preg_replace_callback(
		'/<!-- wp:image (\{.*?\}) -->(\s*<figure[^>]*>)<img src="\{\{asset:([\w.\/-]+)\}\}" alt="([^"]*)"/',
		function ( $m ) {
			$media = (array) get_option( 'occ_media', array() );
			$id    = (int) ( $media[ $m[3] ] ?? 0 );
			$url   = $id ? wp_get_attachment_url( $id ) : '';
			if ( ! $url ) {
				return $m[0];
			}
			$attrs = array( 'id' => $id ) + (array) json_decode( $m[1], true );
			return '<!-- wp:image ' . wp_json_encode( $attrs ) . ' -->' . $m[2] . '<img src="' . esc_url( $url ) . '" alt="' . $m[4] . '" class="wp-image-' . $id . '"';
		},
		$content
	);
	$content = preg_replace_callback(
		'/\{\{url:([\w-]+)(?:#([\w-]+))?\}\}/',
		function ( $m ) {
			return esc_url( occ_url( $m[1], $m[2] ?? '' ) );
		},
		$content
	);
	return preg_replace_callback(
		'/\{\{asset:([\w.\/-]+)\}\}/',
		function ( $m ) {
			return esc_url( occ_asset_url( $m[1] ) );
		},
		$content
	);
}

/**
 * Address of a theme image: its Media Library copy if it was imported, otherwise the file in the theme.
 *
 * @param string $path Path under assets/.
 * @return string
 */
function occ_asset_url( $path ) {
	$media = (array) get_option( 'occ_media', array() );
	$url   = empty( $media[ $path ] ) ? '' : wp_get_attachment_url( $media[ $path ] );
	return $url ? $url : get_theme_file_uri( 'assets/' . $path );
}

/**
 * Copy the images the design content uses (the leader photos) into the Media Library, once, so they can be
 * swapped with "Replace" in the editor and don't depend on the theme folder. Failures leave the theme's copy in use.
 */
function occ_import_media() {
	$media = (array) get_option( 'occ_media', array() );
	$paths = array();
	foreach ( glob( get_theme_file_path( 'inc/content/*.html' ) ) as $file ) {
		preg_match_all( '/\{\{asset:(img\/[\w.\/-]+\.(?:jpe?g|png|webp))\}\}/', file_get_contents( $file ), $m );
		$paths = array_merge( $paths, $m[1] );
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	foreach ( array_unique( $paths ) as $path ) {
		if ( ! empty( $media[ $path ] ) && get_post( $media[ $path ] ) ) {
			continue;
		}
		$tmp = wp_tempnam( basename( $path ) );
		if ( ! $tmp || ! copy( get_theme_file_path( 'assets/' . $path ), $tmp ) ) {
			continue;
		}
		$id = media_handle_sideload( array( 'name' => basename( $path ), 'tmp_name' => $tmp ), 0 );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			continue;
		}
		$media[ $path ] = $id;
	}
	update_option( 'occ_media', $media, false );
}

/**
 * Find the page for a design key: one this theme filled before, then (for Home) the current front page, then by slug.
 *
 * @param string $key  Page key.
 * @param array  $page Slug and title.
 * @return WP_Post|null
 */
function occ_find_page( $key, $page ) {
	$found = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'meta_key'    => '_occ_key', // phpcs:ignore WordPress.DB.SlowDBQuery -- a handful of pages, on activation only.
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
		)
	);
	if ( $found ) {
		return $found[0];
	}
	if ( 'home' === $key && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
		$front = get_post( get_option( 'page_on_front' ) );
		if ( $front && 'page' === $front->post_type && 'trash' !== $front->post_status ) {
			return $front;
		}
	}
	$by_slug = get_page_by_path( $page['slug'] );
	return ( $by_slug && 'trash' !== $by_slug->post_status ) ? $by_slug : null;
}

/**
 * Activation: fill the pages with the design (see the top of this file).
 */
function occ_install() {
	kses_remove_filters(); // the theme's own content; an admin without unfiltered_html would otherwise lose the decorations
	if ( false === get_option( 'occ_previous_front', false ) ) {
		update_option(
			'occ_previous_front',
			array(
				'show_on_front'  => get_option( 'show_on_front' ),
				'page_on_front'  => (int) get_option( 'page_on_front' ),
				'page_for_posts' => (int) get_option( 'page_for_posts' ),
			),
			false
		);
	}
	occ_import_media();

	// First find or create every page, so the links written into the content can find them.
	$ids = array();
	foreach ( occ_design_pages() as $key => $page ) {
		$post = occ_find_page( $key, $page );
		if ( $post ) {
			if ( ! get_post_meta( $post->ID, '_occ_previous', true ) && ! get_post_meta( $post->ID, '_occ_created', true ) ) {
				$previous = array(
					'content'   => $post->post_content,
					'status'    => $post->post_status,
					'template'  => get_post_meta( $post->ID, '_wp_page_template', true ),
					'elementor' => get_post_meta( $post->ID, '_elementor_edit_mode', true ),
				);
				update_post_meta( $post->ID, '_occ_previous', wp_slash( $previous ) );
			}
			$ids[ $key ] = $post->ID;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'draft',
					'post_title'  => wp_slash( $page['title'] ),
					'post_name'   => $page['slug'],
				)
			);
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, '_occ_created', 1 );
			$ids[ $key ] = $id;
		}
		update_post_meta( $ids[ $key ], '_occ_key', $key );
	}
	// Publish them before writing the content: a draft's address is ?page_id=N, and that is what the links would get.
	foreach ( $ids as $id ) {
		if ( 'publish' !== get_post_status( $id ) ) {
			wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
		}
	}
	foreach ( $ids as $key => $id ) {
		$saved   = get_post_meta( $id, '_occ_design_content', true );
		$content = $saved ? $saved : occ_design_content( $key );
		// Elementor would otherwise keep showing its own copy of the page, and its page templates skip the theme.
		delete_post_meta( $id, '_elementor_edit_mode' );
		update_post_meta( $id, '_wp_page_template', 'default' );
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => wp_slash( $content ),
				'post_status'  => 'publish',
			)
		);
	}
	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		if ( in_array( (int) get_option( 'page_for_posts' ), array_map( 'intval', $ids ), true ) ) {
			update_option( 'page_for_posts', 0 );
		}
	}

	if ( ! occ_footer_block_id() ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'wp_block',
				'post_status'  => 'publish',
				'post_title'   => __( 'Site footer', 'one-christian-church' ),
				'post_content' => wp_slash( occ_design_content( 'footer' ) ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_option( 'occ_footer_block', $id, false );
		}
	}
	occ_install_menus();
	kses_init();
	delete_transient( 'occ_search_index' );
	set_transient( 'occ_just_installed', 1, DAY_IN_SECONDS );
}
add_action( 'after_switch_theme', 'occ_install' );

/**
 * Create the "Main menu" and "Search shortcuts" menus and assign them, unless the theme already has menus assigned.
 */
function occ_install_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$names     = array(
		'primary' => __( 'Main menu', 'one-christian-church' ),
		'search'  => __( 'Search shortcuts', 'one-christian-church' ),
	);
	$made      = (array) get_option( 'occ_menus', array() );
	foreach ( $names as $location => $name ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue;
		}
		if ( ! empty( $made[ $location ] ) && wp_get_nav_menu_object( $made[ $location ] ) ) {
			$locations[ $location ] = $made[ $location ];
			continue;
		}
		$label = $name;
		for ( $n = 2; wp_get_nav_menu_object( $label ); $n++ ) {
			$label = $name . ' ' . $n; // the old site may already have a menu with this name
		}
		$menu_id = wp_create_nav_menu( $label );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		foreach ( occ_data()['menus'][ $location ] as $item ) {
			$page = get_page_by_path( $item['slug'] );
			$args = array(
				'menu-item-title'  => $item['title'],
				'menu-item-status' => 'publish',
			);
			if ( $page && ! $item['anchor'] ) {
				$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $page->ID );
			} else {
				$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => occ_url( $item['slug'], $item['anchor'] ) );
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		$locations[ $location ] = $menu_id;
		$made[ $location ]      = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( 'occ_menus', $made, false );
}

/**
 * Switching to another theme: put the site's previous pages and front page back (see the top of this file).
 */
function occ_uninstall() {
	kses_remove_filters();
	$pages = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'any',
			'meta_key'    => '_occ_key', // phpcs:ignore WordPress.DB.SlowDBQuery -- a handful of pages, on theme switch only.
			'numberposts' => -1,
		)
	);
	foreach ( $pages as $page ) {
		update_post_meta( $page->ID, '_occ_design_content', wp_slash( $page->post_content ) );
		$previous = get_post_meta( $page->ID, '_occ_previous', true );
		if ( is_array( $previous ) ) {
			wp_update_post(
				array(
					'ID'           => $page->ID,
					'post_content' => wp_slash( $previous['content'] ),
					'post_status'  => $previous['status'] ? $previous['status'] : 'publish',
				)
			);
			if ( $previous['template'] ) {
				update_post_meta( $page->ID, '_wp_page_template', $previous['template'] );
			} else {
				delete_post_meta( $page->ID, '_wp_page_template' );
			}
			if ( $previous['elementor'] ) {
				update_post_meta( $page->ID, '_elementor_edit_mode', $previous['elementor'] );
			}
			delete_post_meta( $page->ID, '_occ_previous' );
		} elseif ( get_post_meta( $page->ID, '_occ_created', true ) ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_status' => 'draft' ) );
		}
	}
	$front = get_option( 'occ_previous_front' );
	if ( is_array( $front ) ) {
		update_option( 'show_on_front', $front['show_on_front'] );
		update_option( 'page_on_front', $front['page_on_front'] );
		update_option( 'page_for_posts', $front['page_for_posts'] );
		delete_option( 'occ_previous_front' );
	}
	kses_init();
}
add_action( 'switch_theme', 'occ_uninstall' );

/**
 * Put a page (or the footer pattern) back to the theme's design. The current content stays in the page's revisions.
 *
 * @param int $id Page or pattern ID.
 * @return bool
 */
function occ_reset_to_design( $id ) {
	$key = $id === occ_footer_block_id() ? 'footer' : get_post_meta( $id, '_occ_key', true );
	if ( ! $key ) {
		return false;
	}
	kses_remove_filters();
	$result = wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( occ_design_content( $key ) ) ) );
	kses_init();
	delete_transient( 'occ_search_index' );
	return $result && ! is_wp_error( $result );
}

/**
 * True when a page starts with the design's own hero (the theme's pages); other pages get a simple title header.
 *
 * @param WP_Post|null $post Page.
 * @return bool
 */
function occ_has_hero( $post = null ) {
	$post = get_post( $post );
	return $post && preg_match( '/^\s*<!-- wp:group \{[^}]*"className":"hero[ "]/', $post->post_content );
}
