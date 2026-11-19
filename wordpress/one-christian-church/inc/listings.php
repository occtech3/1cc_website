<?php
/**
 * Dashboard menus for the cards on the site: Events, Ministries, Home Highlights and Leaders.
 *
 * Each menu has "Add new", edit and Trash, with a plain form (no page builder). Every card row below is printed from
 * those items, in exactly the markup and style of the design, through a [occ_list row="..."] code that sits inside
 * the row on the page (a "Design element" block in the editor):
 *
 *   events-weekly      Events page, "Every week"           Events with "Shows in: Every week"
 *   events-calendar    Events page, "Mark your calendar"   Events with "Shows in: Mark your calendar"
 *   home-highlights    Home page, "What's coming up"       Home Highlights, plus events ticked "Also show on Home"
 *   ministries-kids    Ministries page, Kids & youth       Ministries in the "Kids & youth" group
 *   ministries-adults  Ministries page, Adult ministries   Ministries in the "Adult ministries" group
 *   about-leaders      About page, "Meet our leaders"      Leaders
 *   home-leaders       Home page, "Meet our leaders"       Leaders
 *
 * Moving the cards in: the first time the theme runs (on activation, or after updating the theme on a site that
 * already uses it), each of those rows still holds its cards as page blocks. They are read one by one into items
 * of the matching menu, and the row's blocks are replaced by its [occ_list] code, so the page looks exactly as it
 * did. The page's previous version stays in its revisions. A row that has been changed beyond recognition is left
 * as it is. Each row is moved once; deleting every item of a menu later does not bring the old cards back.
 *
 * Dated events (a date instead of a "when" label) hide themselves after their last day.
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

/* ----------------------------------------------------------------------------------------------------------------
 * Options shown on the forms
 * ------------------------------------------------------------------------------------------------------------- */

/**
 * Choices for the select fields.
 *
 * @param string $set Which list.
 * @return array value => label
 */
function occ_list_choices( $set ) {
	switch ( $set ) {
		case 'promo':
			return array(
				'p1' => __( 'Violet to blue', 'one-christian-church' ),
				'p2' => __( 'Sunset (pink to orange)', 'one-christian-church' ),
				'p3' => __( 'Fresh (green to teal)', 'one-christian-church' ),
				'p4' => __( 'Gold to orange', 'one-christian-church' ),
				'p5' => __( 'Night blue', 'one-christian-church' ),
				'p6' => __( 'Pink to violet', 'one-christian-church' ),
				'p7' => __( 'Teal to violet', 'one-christian-church' ),
			);
		case 'tile':
			return array(
				't-yellow' => __( 'Yellow', 'one-christian-church' ),
				't-pink'   => __( 'Pink', 'one-christian-church' ),
				't-violet' => __( 'Violet', 'one-christian-church' ),
				't-orange' => __( 'Orange', 'one-christian-church' ),
				't-aqua'   => __( 'Aqua', 'one-christian-church' ),
				't-sky'    => __( 'Sky blue', 'one-christian-church' ),
				't-lime'   => __( 'Lime', 'one-christian-church' ),
				't-indigo' => __( 'Indigo', 'one-christian-church' ),
			);
		case 'icon':
			$names = array(
				'calendar' => __( 'Calendar', 'one-christian-church' ),
				'clock'    => __( 'Clock', 'one-christian-church' ),
				'pin'      => __( 'Map pin', 'one-christian-church' ),
				'sparkle'  => __( 'Sparkle', 'one-christian-church' ),
				'gift'     => __( 'Gift', 'one-christian-church' ),
				'heart'    => __( 'Heart', 'one-christian-church' ),
				'hands'    => __( 'Hands', 'one-christian-church' ),
				'users'    => __( 'People', 'one-christian-church' ),
				'fire'     => __( 'Fire', 'one-christian-church' ),
				'book'     => __( 'Bible / book', 'one-christian-church' ),
				'music'    => __( 'Music', 'one-christian-church' ),
				'coffee'   => __( 'Coffee', 'one-christian-church' ),
				'lunch'    => __( 'Meal', 'one-christian-church' ),
				'smile'    => __( 'Smile', 'one-christian-church' ),
				'home'     => __( 'House', 'one-christian-church' ),
				'radio'    => __( 'Radio', 'one-christian-church' ),
				'play'     => __( 'Video', 'one-christian-church' ),
				'mail'     => __( 'Envelope', 'one-christian-church' ),
				'phone'    => __( 'Phone', 'one-christian-church' ),
				'text'     => __( 'Speech bubble', 'one-christian-church' ),
				'card'     => __( 'Card', 'one-christian-church' ),
			);
			// Any other icon the design has (a card moved in from the page may use one) is offered too.
			foreach ( array_keys( occ_fragments()['icons'] ) as $icon ) {
				if ( ! isset( $names[ $icon ] ) && ! in_array( $icon, array( 'menu', 'search', 'arrow' ), true ) ) {
					$names[ $icon ] = ucfirst( $icon );
				}
			}
			return $names;
		case 'day':
			return array(
				'Sun' => __( 'Sunday', 'one-christian-church' ),
				'Mon' => __( 'Monday', 'one-christian-church' ),
				'Tue' => __( 'Tuesday', 'one-christian-church' ),
				'Wed' => __( 'Wednesday', 'one-christian-church' ),
				'Thu' => __( 'Thursday', 'one-christian-church' ),
				'Fri' => __( 'Friday', 'one-christian-church' ),
				'Sat' => __( 'Saturday', 'one-christian-church' ),
			);
		case 'event_section':
			return array(
				'calendar' => __( 'Events page: "Mark your calendar" (special, monthly and seasonal events)', 'one-christian-church' ),
				'weekly'   => __( 'Events page: "Every week" (the weekly schedule)', 'one-christian-church' ),
			);
		case 'ministry_section':
			return array(
				'adults' => __( 'Ministries page: Adult ministries', 'one-christian-church' ),
				'kids'   => __( 'Ministries page: Kids & youth', 'one-christian-church' ),
			);
	}
	return array();
}

/**
 * The form fields of each menu: key => [label, type, help, extra]. Fields with 'only' in extra show only for that
 * value of the item's "section" field.
 *
 * @param string $type Post type.
 * @return array
 */
function occ_list_fields( $type ) {
	$text_help = __( 'One or two short sentences. A link can be added with the usual <a href="...">words</a>.', 'one-christian-church' );
	switch ( $type ) {
		case 'occ_event':
			return array(
				'section'   => array( __( 'Shows in', 'one-christian-church' ), 'select:event_section', '' ),
				'day'       => array( __( 'Day', 'one-christian-church' ), 'select:day', '', array( 'only' => 'weekly' ) ),
				'time'      => array( __( 'Time', 'one-christian-church' ), 'text', __( 'For example: 9:00, or 6:30pm', 'one-christian-church' ), array( 'only' => 'weekly' ) ),
				'place'     => array( __( 'Place', 'one-christian-church' ), 'text', __( 'For example: Prayer Room', 'one-christian-church' ), array( 'only' => 'weekly' ) ),
				'date'      => array( __( 'Date', 'one-christian-church' ), 'date', __( 'For a one-time event. The card shows the date and hides itself after the day. Leave empty for a regular event and fill in "When" instead.', 'one-christian-church' ), array( 'only' => 'calendar' ) ),
				'end_date'  => array( __( 'Last day (optional)', 'one-christian-church' ), 'date', __( 'For an event over several days.', 'one-christian-church' ), array( 'only' => 'calendar' ) ),
				'label'     => array( __( 'When', 'one-christian-church' ), 'text', __( 'The small line above the title. With a date: the time, for example 6–8pm. Without one: for example 3rd Thursday · 6:30pm', 'one-christian-church' ), array( 'only' => 'calendar' ) ),
				'text'      => array( __( 'Description', 'one-christian-church' ), 'textarea', $text_help ),
				'link'      => array( __( 'Card link (optional)', 'one-christian-church' ), 'url', __( 'Makes the whole card a link, for example to a sign-up page.', 'one-christian-church' ), array( 'only' => 'calendar' ) ),
				'colour'    => array( __( 'Card colour', 'one-christian-church' ), 'select:promo', '', array( 'only' => 'calendar' ) ),
				'icon'      => array( __( 'Icon', 'one-christian-church' ), 'select:icon', '', array( 'only' => 'calendar' ) ),
				'show_home' => array( __( 'Also show this card on the Home page ("What\'s coming up"), before the highlights', 'one-christian-church' ), 'check', '', array( 'only' => 'calendar' ) ),
				'show_from' => array( __( 'Start showing on (optional)', 'one-christian-church' ), 'date', __( 'Leave empty to show it as soon as it is published.', 'one-christian-church' ) ),
			);
		case 'occ_ministry':
			return array(
				'section'   => array( __( 'Shows in', 'one-christian-church' ), 'select:ministry_section', '' ),
				'chip'      => array( __( 'Tag', 'one-christian-church' ), 'text', __( 'The small rounded label, for example: 1st – 5th grade. Optional for adult ministries.', 'one-christian-church' ) ),
				'text'      => array( __( 'Description', 'one-christian-church' ), 'textarea', $text_help ),
				'when'      => array( __( 'When / where', 'one-christian-church' ), 'text', __( 'The line at the bottom of the card, for example: Tuesdays · 6:30pm', 'one-christian-church' ) ),
				'when_icon' => array( __( 'Icon for that line', 'one-christian-church' ), 'select:icon', '' ),
				'icon'      => array( __( 'Card icon', 'one-christian-church' ), 'select:icon', '', array( 'only' => 'adults' ) ),
				'colour'    => array( __( 'Card colour', 'one-christian-church' ), 'select:tile', '', array( 'only' => 'kids' ) ),
				'featured'  => array( __( 'Large card (spans two columns, like Young Adults)', 'one-christian-church' ), 'check', '', array( 'only' => 'adults' ) ),
				'anchor'    => array( __( 'Link name (optional)', 'one-christian-church' ), 'text', __( 'Lets other pages link straight to this card, for example "youth" for /ministries/#youth. Letters, numbers and dashes.', 'one-christian-church' ) ),
			);
		case 'occ_highlight':
			return array(
				'label'      => array( __( 'Small line above the title', 'one-christian-church' ), 'text', __( 'For example: Sundays · 6:30pm, or New series', 'one-christian-church' ) ),
				'text'       => array( __( 'Description', 'one-christian-church' ), 'textarea', __( 'One or two short sentences.', 'one-christian-church' ) ),
				'link'       => array( __( 'Card link (optional)', 'one-christian-church' ), 'url', __( 'Makes the whole card a link: a page on this site or another site.', 'one-christian-church' ) ),
				'colour'     => array( __( 'Card colour', 'one-christian-church' ), 'select:promo', '' ),
				'icon'       => array( __( 'Icon', 'one-christian-church' ), 'select:icon', '' ),
				'show_from'  => array( __( 'Start showing on (optional)', 'one-christian-church' ), 'date', '' ),
				'hide_after' => array( __( 'Hide after (optional)', 'one-christian-church' ), 'date', __( 'Leave empty to keep it up until you remove it.', 'one-christian-church' ) ),
			);
		case 'occ_leader':
			return array(
				'role'  => array( __( 'Role', 'one-christian-church' ), 'text', __( 'For example: Senior Pastor', 'one-christian-church' ) ),
				'photo' => array( __( 'Photo', 'one-christian-church' ), 'image', __( 'A square photo works best.', 'one-christian-church' ) ),
			);
	}
	return array();
}

/**
 * The menus.
 *
 * @return array post type => [plural, singular, icon, position, title placeholder, description]
 */
function occ_list_types() {
	return array(
		'occ_event'     => array( __( 'Events', 'one-christian-church' ), __( 'Event', 'one-christian-church' ), 'dashicons-calendar-alt', 21, __( 'Event name, for example: Christmas Eve Candlelight Service', 'one-christian-church' ), __( 'The weekly schedule and the "Mark your calendar" cards on the Events page.', 'one-christian-church' ) ),
		'occ_ministry'  => array( __( 'Ministries', 'one-christian-church' ), __( 'Ministry', 'one-christian-church' ), 'dashicons-groups', 22, __( 'Ministry name, for example: Women\'s Fire', 'one-christian-church' ), __( 'The Kids & youth and Adult ministries cards on the Ministries page.', 'one-christian-church' ) ),
		'occ_highlight' => array( __( 'Home Highlights', 'one-christian-church' ), __( 'Home Highlight', 'one-christian-church' ), 'dashicons-megaphone', 23, __( 'Card title, for example: New sermon series', 'one-christian-church' ), __( 'The "What\'s coming up" cards on the Home page.', 'one-christian-church' ) ),
		'occ_leader'    => array( __( 'Leaders', 'one-christian-church' ), __( 'Leader', 'one-christian-church' ), 'dashicons-id', 24, __( 'Name, for example: Jerry Shomali', 'one-christian-church' ), __( 'The "Meet our leaders" photos on the About and Home pages.', 'one-christian-church' ) ),
	);
}

/**
 * The card rows: row => [page key, CSS class of the row group, post type, section or ''].
 *
 * @return array
 */
function occ_list_rows() {
	return array(
		'events-weekly'     => array( 'events', 'event-list', 'occ_event', 'weekly' ),
		'events-calendar'   => array( 'events', 'carousel-track', 'occ_event', 'calendar' ),
		'home-highlights'   => array( 'home', 'carousel-track', 'occ_highlight', '' ),
		'ministries-kids'   => array( 'ministries', 'age-grid', 'occ_ministry', 'kids' ),
		'ministries-adults' => array( 'ministries', 'cards', 'occ_ministry', 'adults' ),
		'about-leaders'     => array( 'about', 'leaders', 'occ_leader', '' ),
		'home-leaders'      => array( 'home', 'leaders', 'occ_leader', '' ),
	);
}

/* ----------------------------------------------------------------------------------------------------------------
 * Menus and forms
 * ------------------------------------------------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		foreach ( occ_list_types() as $type => $t ) {
			register_post_type(
				$type,
				array(
					'labels'              => array(
						'name'               => $t[0],
						'singular_name'      => $t[1],
						'menu_name'          => $t[0],
						'add_new'            => __( 'Add new', 'one-christian-church' ),
						/* translators: %s: Event, Ministry... */
						'add_new_item'       => sprintf( __( 'Add new %s', 'one-christian-church' ), strtolower( $t[1] ) ),
						/* translators: %s: Event, Ministry... */
						'edit_item'          => sprintf( __( 'Edit %s', 'one-christian-church' ), strtolower( $t[1] ) ),
						/* translators: %s: Events, Ministries... */
						'all_items'          => sprintf( __( 'All %s', 'one-christian-church' ), strtolower( $t[0] ) ),
						/* translators: %s: Events, Ministries... */
						'search_items'       => sprintf( __( 'Search %s', 'one-christian-church' ), strtolower( $t[0] ) ),
						'not_found'          => __( 'Nothing here yet. Click "Add new" to add one.', 'one-christian-church' ),
						'not_found_in_trash' => __( 'Nothing in the trash.', 'one-christian-church' ),
						'item_published'     => __( 'Published. It is on the site now.', 'one-christian-church' ),
						'item_updated'       => __( 'Saved. The site shows the change now.', 'one-christian-church' ),
					),
					'description'         => $t[5],
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_rest'        => false, // a plain form, not the block editor
					'exclude_from_search' => true,
					'menu_position'       => $t[3],
					'menu_icon'           => $t[2],
					'supports'            => array( 'title', 'page-attributes' ),
					'map_meta_cap'        => true,
					'capability_type'     => 'page', // the same people who edit the pages
				)
			);
		}
	}
);

add_filter(
	'enter_title_here',
	function ( $text, $post ) {
		$types = occ_list_types();
		return isset( $types[ $post->post_type ] ) ? $types[ $post->post_type ][4] : $text;
	},
	10,
	2
);

add_action(
	'add_meta_boxes',
	function () {
		foreach ( array_keys( occ_list_types() ) as $type ) {
			add_meta_box( 'occ_card', __( 'Card details', 'one-christian-church' ), 'occ_list_form', $type, 'normal', 'high' );
		}
	}
);

// The photo picker on the Leaders form.
add_action(
	'admin_enqueue_scripts',
	function () {
		$screen = get_current_screen();
		if ( $screen && 'post' === $screen->base && 'occ_leader' === $screen->post_type ) {
			wp_enqueue_media();
		}
	}
);

/**
 * Saved value of a field, with the defaults a new item starts with.
 *
 * @param int    $id  Item ID.
 * @param string $key Field key.
 * @return string
 */
function occ_list_value( $id, $key ) {
	if ( metadata_exists( 'post', $id, '_occ_' . $key ) ) {
		return (string) get_post_meta( $id, '_occ_' . $key, true );
	}
	$type     = get_post_type( $id );
	$defaults = array(
		'section'   => 'occ_event' === $type ? 'calendar' : 'adults',
		'colour'    => 'occ_ministry' === $type ? 't-yellow' : 'p2',
		'icon'      => 'occ_event' === $type ? 'calendar' : 'sparkle',
		'when_icon' => 'clock',
		'day'       => 'Sun',
	);
	return $defaults[ $key ] ?? '';
}

/**
 * The form.
 *
 * @param WP_Post $post Item.
 */
function occ_list_form( $post ) {
	wp_nonce_field( 'occ_list_save', 'occ_list_nonce' );
	$section = occ_list_value( $post->ID, 'section' );
	echo '<table class="form-table occ-list-form" role="presentation">';
	foreach ( occ_list_fields( $post->post_type ) as $key => $field ) {
		$label = $field[0];
		$kind  = $field[1];
		$help  = $field[2];
		$only  = $field[3]['only'] ?? '';
		$name  = 'occ_list[' . $key . ']';
		$id    = 'occ-list-' . $key;
		$value = occ_list_value( $post->ID, $key );
		$attrs = $only ? ' data-only="' . esc_attr( $only ) . '"' . ( $only !== $section ? ' hidden' : '' ) : '';
		echo '<tr' . $attrs . '><th scope="row">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		if ( 'check' !== $kind ) {
			echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		}
		echo '</th><td>';
		if ( 'textarea' === $kind ) {
			echo '<textarea class="large-text" rows="3" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( 'check' === $kind ) {
			echo '<label><input type="checkbox" value="1" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . checked( '1', $value, false ) . '> ' . esc_html( $label ) . '</label>';
		} elseif ( 0 === strpos( $kind, 'select:' ) ) {
			$choices = occ_list_choices( substr( $kind, 7 ) );
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . ( 'section' === $key ? ' data-section' : '' ) . '>';
			foreach ( $choices as $option => $option_label ) {
				echo '<option value="' . esc_attr( $option ) . '"' . selected( $option, $value, false ) . '>' . esc_html( $option_label ) . '</option>';
			}
			echo '</select>';
		} elseif ( 'image' === $kind ) {
			$img = $value ? wp_get_attachment_image( (int) $value, 'thumbnail', false, array( 'style' => 'border-radius:50%;width:96px;height:96px;object-fit:cover' ) ) : '';
			echo '<div data-photo>' . ( $img ? $img : '' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core markup.
			echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
			echo '<p><button type="button" class="button" data-photo-pick>' . esc_html__( 'Choose photo', 'one-christian-church' ) . '</button> ';
			echo '<button type="button" class="button-link" data-photo-clear>' . esc_html__( 'Remove photo', 'one-christian-church' ) . '</button></p>';
		} else {
			$input = 'date' === $kind ? 'date' : ( 'url' === $kind ? 'url' : 'text' );
			echo '<input type="' . esc_attr( $input ) . '"' . ( 'date' === $kind ? '' : ' class="regular-text"' ) . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
		}
		if ( $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
	echo '<p class="description">' . esc_html__( 'Order: cards are shown in the "Order" number set in the box on the right, lowest first (dated events by date, the weekly schedule by day and time). To take a card off the site, click "Move to Trash", or switch it to Draft.', 'one-christian-church' ) . '</p>';
	$rows  = occ_list_rows_for( $post->ID );
	$links = array();
	foreach ( $rows as $row ) {
		$page = occ_list_page( occ_list_rows()[ $row ][0] );
		if ( $page ) {
			$links[ $page ] = '<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank" rel="noopener">' . esc_html( get_the_title( $page ) ) . '</a>';
		}
	}
	if ( $links && 'publish' === $post->post_status ) {
		echo '<p>' . esc_html__( 'See it on:', 'one-christian-church' ) . ' ' . implode( ', ', $links ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	?>
	<script>
	( function () {
		var form = document.querySelector( '.occ-list-form' );
		var sel = form && form.querySelector( '[data-section]' );
		if ( sel ) {
			sel.addEventListener( 'change', function () {
				form.querySelectorAll( '[data-only]' ).forEach( function ( row ) { row.hidden = row.getAttribute( 'data-only' ) !== sel.value; } );
			} );
		}
		var pick = form && form.querySelector( '[data-photo-pick]' );
		if ( pick && window.wp && wp.media ) {
			var input = form.querySelector( 'input[name="occ_list[photo]"]' ), box = form.querySelector( '[data-photo]' ), frame;
			pick.addEventListener( 'click', function () {
				frame = frame || wp.media( { title: pick.textContent, library: { type: 'image' }, multiple: false } );
				frame.off( 'select' ).on( 'select', function () {
					var a = frame.state().get( 'selection' ).first().toJSON();
					input.value = a.id;
					var url = ( a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url );
					box.innerHTML = '<img src="' + url + '" alt="" style="border-radius:50%;width:96px;height:96px;object-fit:cover">';
				} );
				frame.open();
			} );
			form.querySelector( '[data-photo-clear]' ).addEventListener( 'click', function () { input.value = ''; box.innerHTML = ''; } );
		}
	} )();
	</script>
	<?php
}

/**
 * Description text: words with simple links and emphasis.
 *
 * @param string $html Input.
 * @return string
 */
function occ_list_kses( $html ) {
	return wp_kses(
		$html,
		array(
			'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
			'em'     => array(),
			'strong' => array(),
			'br'     => array(),
		)
	);
}

/**
 * Clean one field value for saving.
 *
 * @param string $type Post type.
 * @param string $key  Field key.
 * @param string $raw  Raw value.
 * @return string
 */
function occ_list_clean( $type, $key, $raw ) {
	$fields = occ_list_fields( $type );
	$kind   = $fields[ $key ][1] ?? 'text';
	$raw    = (string) $raw;
	if ( 'textarea' === $kind ) {
		return trim( occ_list_kses( $raw ) );
	}
	if ( 'url' === $kind ) {
		return esc_url_raw( trim( $raw ) );
	}
	if ( 'date' === $kind ) {
		return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
	}
	if ( 'check' === $kind ) {
		return '1' === $raw ? '1' : '0';
	}
	if ( 'image' === $kind ) {
		return (string) absint( $raw );
	}
	if ( 0 === strpos( $kind, 'select:' ) ) {
		$choices = occ_list_choices( substr( $kind, 7 ) );
		return isset( $choices[ $raw ] ) ? $raw : (string) array_key_first( $choices );
	}
	if ( 'anchor' === $key ) {
		return sanitize_title( $raw );
	}
	return sanitize_text_field( $raw );
}

add_action(
	'save_post',
	function ( $id, $post ) {
		if ( ! isset( occ_list_types()[ $post->post_type ] ) || wp_is_post_revision( $id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}
		if ( ! isset( $_POST['occ_list_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['occ_list_nonce'] ) ), 'occ_list_save' ) || ! current_user_can( 'edit_post', $id ) ) {
			return;
		}
		// A new card goes after the existing ones unless an Order number was typed.
		if ( 0 === (int) $post->menu_order && isset( $_POST['menu_order'] ) && '0' === trim( sanitize_text_field( wp_unslash( $_POST['menu_order'] ) ) ) && ! get_post_meta( $id, '_occ_saved', true ) ) {
			global $wpdb;
			$max = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = %s AND post_status <> 'trash'", $post->post_type ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $max + 10 ), array( 'ID' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			clean_post_cache( $id );
		}
		$input = isset( $_POST['occ_list'] ) && is_array( $_POST['occ_list'] ) ? wp_unslash( $_POST['occ_list'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cleaned per field.
		foreach ( array_keys( occ_list_fields( $post->post_type ) ) as $key ) {
			update_post_meta( $id, '_occ_' . $key, wp_slash( occ_list_clean( $post->post_type, $key, $input[ $key ] ?? '' ) ) );
		}
		update_post_meta( $id, '_occ_saved', 1 );
	},
	10,
	2
);

// Plain-words notices after saving.
add_filter(
	'post_updated_messages',
	function ( $messages ) {
		foreach ( array_keys( occ_list_types() ) as $type ) {
			$saved              = __( 'Saved. The site shows the change now.', 'one-christian-church' );
			$messages[ $type ] = array(
				1  => $saved,
				4  => $saved,
				6  => __( 'Published. It is on the site now.', 'one-christian-church' ),
				7  => $saved,
				8  => $saved,
				9  => __( 'Scheduled.', 'one-christian-church' ),
				10 => __( 'Draft saved. Drafts are not shown on the site.', 'one-christian-church' ),
			);
		}
		return $messages;
	}
);

// The site shows a change at once: empty the page caches and the search index whenever an item changes.
add_action(
	'transition_post_status',
	function ( $new, $old, $post ) {
		if ( isset( occ_list_types()[ $post->post_type ] ) && ( 'publish' === $new || 'publish' === $old ) ) {
			add_action( 'shutdown', 'occ_list_changed' );
		}
	},
	10,
	3
);
add_action(
	'save_post',
	function ( $id, $post ) {
		if ( isset( occ_list_types()[ $post->post_type ] ) && 'publish' === $post->post_status ) {
			add_action( 'shutdown', 'occ_list_changed' );
		}
	},
	20,
	2
);

/**
 * After a change: clear caches.
 */
function occ_list_changed() {
	delete_transient( 'occ_search_index' );
	if ( function_exists( 'occ_clear_page_caches' ) ) {
		occ_clear_page_caches();
	}
}

/* ----------------------------------------------------------------------------------------------------------------
 * Which items show where
 * ------------------------------------------------------------------------------------------------------------- */

/**
 * The design page for a key.
 *
 * @param string $key Page key.
 * @return int
 */
function occ_list_page( $key ) {
	$found = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => array( 'publish', 'draft', 'private' ),
			'meta_key'    => '_occ_key', // phpcs:ignore WordPress.DB.SlowDBQuery -- a handful of pages.
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * The rows an item shows in.
 *
 * @param int $id Item ID.
 * @return string[]
 */
function occ_list_rows_for( $id ) {
	$type = get_post_type( $id );
	$rows = array();
	foreach ( occ_list_rows() as $row => $def ) {
		if ( $def[2] === $type && ( '' === $def[3] || occ_list_value( $id, 'section' ) === $def[3] ) ) {
			$rows[] = $row;
		}
	}
	if ( 'occ_event' === $type && 'calendar' === occ_list_value( $id, 'section' ) && '1' === occ_list_value( $id, 'show_home' ) ) {
		$rows[] = 'home-highlights';
	}
	return $rows;
}

/**
 * Whether an item shows today.
 *
 * @param int $id Item ID.
 * @return string showing, scheduled or ended
 */
function occ_list_state( $id ) {
	$today = wp_date( 'Y-m-d' );
	$from  = occ_list_value( $id, 'show_from' );
	$last  = '';
	if ( 'occ_event' === get_post_type( $id ) && 'calendar' === occ_list_value( $id, 'section' ) ) {
		$last = occ_list_value( $id, 'end_date' );
		$last = $last ? $last : occ_list_value( $id, 'date' );
	} elseif ( 'occ_highlight' === get_post_type( $id ) ) {
		$last = occ_list_value( $id, 'hide_after' );
	}
	if ( $last && $last < $today ) {
		return 'ended';
	}
	if ( $from && $from > $today ) {
		return 'scheduled';
	}
	return 'showing';
}

/**
 * Minutes after midnight for a weekly time such as "6:30pm" or "9:00" (sorting only).
 *
 * @param string $time Time text.
 * @return int
 */
function occ_list_minutes( $time ) {
	if ( ! preg_match( '/(\d{1,2})(?::(\d{2}))?\s*(am|pm)?/i', $time, $m ) ) {
		return 9999;
	}
	$h = (int) $m[1] % 12;
	if ( isset( $m[3] ) && 'pm' === strtolower( $m[3] ) ) {
		$h += 12;
	} elseif ( empty( $m[3] ) && $h < 7 ) {
		$h += 12; // "6:30" with no am/pm is an evening gathering
	}
	return $h * 60 + (int) ( $m[2] ?? 0 );
}

/**
 * The items showing in a row today, in order.
 *
 * @param string $row Row.
 * @return int[]
 */
function occ_list_items( $row ) {
	$def = occ_list_rows()[ $row ] ?? null;
	if ( ! $def ) {
		return array();
	}
	$types = 'home-highlights' === $row ? array( 'occ_event', 'occ_highlight' ) : array( $def[2] );
	$ids   = get_posts(
		array(
			'post_type'   => $types,
			'post_status' => 'publish',
			'numberposts' => 200,
			'fields'      => 'ids',
			'orderby'     => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
		)
	);
	$ids = array_values( array_filter( $ids, fn( $id ) => in_array( $row, occ_list_rows_for( $id ), true ) && 'showing' === occ_list_state( $id ) ) );
	$pos = array_flip( $ids );
	$day = array_flip( array_keys( occ_list_choices( 'day' ) ) );
	usort(
		$ids,
		function ( $a, $b ) use ( $row, $pos, $day ) {
			if ( 'events-weekly' === $row ) {
				$ka = array( $day[ occ_list_value( $a, 'day' ) ] ?? 9, occ_list_minutes( occ_list_value( $a, 'time' ) ), $pos[ $a ] );
				$kb = array( $day[ occ_list_value( $b, 'day' ) ] ?? 9, occ_list_minutes( occ_list_value( $b, 'time' ) ), $pos[ $b ] );
				return $ka <=> $kb;
			}
			// Dated events first, soonest first; events before highlights on Home; then the Order number.
			$da = 'occ_event' === get_post_type( $a ) ? occ_list_value( $a, 'date' ) : '';
			$db = 'occ_event' === get_post_type( $b ) ? occ_list_value( $b, 'date' ) : '';
			$ka = array( $da ? 0 : 1, $da, 'occ_event' === get_post_type( $a ) ? 0 : 1, $pos[ $a ] );
			$kb = array( $db ? 0 : 1, $db, 'occ_event' === get_post_type( $b ) ? 0 : 1, $pos[ $b ] );
			return $ka <=> $kb;
		}
	);
	return $ids;
}

/* ----------------------------------------------------------------------------------------------------------------
 * The cards, in the same markup the page blocks print
 * ------------------------------------------------------------------------------------------------------------- */

/**
 * An icon from the design.
 *
 * @param string $name Icon.
 * @param bool   $lg   Large version.
 * @return string
 */
function occ_list_icon( $name, $lg = false ) {
	$set = occ_fragments()[ $lg ? 'icons_lg' : 'icons' ];
	return $set[ $name ] ?? ( $set['calendar'] ?? '' );
}

/**
 * The "when" line of a calendar event: its label, or the date followed by the label.
 *
 * @param int $id Event.
 * @return string Plain text.
 */
function occ_event_when( $id ) {
	$label = occ_list_value( $id, 'label' );
	$date  = occ_list_value( $id, 'date' );
	if ( ! $date ) {
		return $label;
	}
	$tz    = wp_timezone();
	$start = date_create_immutable( $date . ' 12:00', $tz );
	$end   = occ_list_value( $id, 'end_date' );
	$end   = $end ? date_create_immutable( $end . ' 12:00', $tz ) : null;
	if ( ! $start ) {
		return $label;
	}
	$when = wp_date( 'D, M j', $start->getTimestamp(), $tz );
	if ( $end && $end > $start ) {
		$when .= '–' . wp_date( $end->format( 'n' ) === $start->format( 'n' ) ? 'j' : 'M j', $end->getTimestamp(), $tz );
	}
	return $label ? $when . ' · ' . $label : $when;
}

/**
 * A card's attributes: id and classes.
 *
 * @param string $classes Classes.
 * @param string $anchor  Anchor.
 * @return string
 */
function occ_list_attrs( $classes, $anchor = '' ) {
	return ( $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '' ) . ' class="' . esc_attr( $classes ) . '"';
}

/**
 * One card.
 *
 * @param int    $id  Item.
 * @param string $row Row.
 * @return string
 */
function occ_list_card( $id, $row ) {
	$title = esc_html( get_the_title( $id ) );
	$text  = occ_list_kses( occ_list_value( $id, 'text' ) );
	$p     = $text ? '<p>' . $text . '</p>' : '';
	switch ( $row ) {
		case 'events-weekly':
			$time = esc_html( occ_list_value( $id, 'time' ) );
			$time = preg_replace( '/^(.*\d)\s*(am|pm)$/i', '$1<small>$2</small>', $time );
			$day  = occ_list_value( $id, 'day' );
			return '<article class="wp-block-group event reveal"><div class="wp-block-group ev-date"><p class="x-inline"><span>' . esc_html( $day ) . '</span><strong>' . $time . '</strong></p></div>'
				. '<div class="wp-block-group"><h3 class="wp-block-heading">' . $title . '</h3>' . $p . '</div>'
				. ( occ_list_value( $id, 'place' ) ? '<p class="x-inline"><span class="ev-where">' . esc_html( occ_list_value( $id, 'place' ) ) . '</span></p>' : '' )
				. '</article>';

		case 'events-calendar':
		case 'home-highlights':
			$lg     = 'home-highlights' === $row;
			$colour = occ_list_value( $id, 'colour' );
			$link   = 'occ_event' === get_post_type( $id ) && 'calendar' !== occ_list_value( $id, 'section' ) ? '' : occ_list_value( $id, 'link' );
			$when   = 'occ_event' === get_post_type( $id ) ? occ_event_when( $id ) : occ_list_value( $id, 'label' );
			if ( $link ) {
				$host  = wp_parse_url( $link, PHP_URL_HOST );
				$ext   = $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host;
				$title = '<a class="card-link" href="' . esc_url( $link ) . '"' . ( $ext ? ' target="_blank" rel="noopener"' : '' ) . '>' . $title . '</a>';
				// The whole card is the link, so links inside the description would be unclickable.
				$p = $text ? '<p>' . wp_strip_all_tags( $text ) . '</p>' : '';
			}
			return '<div' . occ_list_attrs( 'wp-block-group promo ' . $colour . ' reveal' . ( $link ? ' is-linked' : '' ) ) . '>' . occ_list_icon( occ_list_value( $id, 'icon' ), $lg )
				. '<div class="wp-block-group">' . ( '' !== $when ? '<p class="x-inline"><span class="when">' . esc_html( $when ) . '</span></p>' : '' )
				. '<h3 class="wp-block-heading">' . $title . '</h3>' . $p . '</div></div>';

		case 'ministries-kids':
			$chip = occ_list_value( $id, 'chip' );
			$when = occ_list_value( $id, 'when' );
			return '<div' . occ_list_attrs( 'wp-block-group age ' . occ_list_value( $id, 'colour' ) . ' reveal', occ_list_value( $id, 'anchor' ) ) . '>'
				. ( $chip ? '<p class="x-inline"><span class="chip">' . esc_html( $chip ) . '</span></p>' : '' )
				. '<h3 class="wp-block-heading">' . $title . '</h3>' . $p
				. ( $when ? '<p class="x-inline"><span class="when">' . occ_list_icon( occ_list_value( $id, 'when_icon' ) ) . ' ' . esc_html( $when ) . '</span></p>' : '' )
				. '</div>';

		case 'ministries-adults':
			$chip = occ_list_value( $id, 'chip' );
			$when = occ_list_value( $id, 'when' );
			return '<div' . occ_list_attrs( 'wp-block-group card' . ( '1' === occ_list_value( $id, 'featured' ) ? ' card-feature' : '' ) . ' reveal', occ_list_value( $id, 'anchor' ) ) . '>'
				. '<span class="ic">' . occ_list_icon( occ_list_value( $id, 'icon' ) ) . '</span>'
				. ( $chip ? '<p class="x-inline"><span class="chip">' . esc_html( $chip ) . '</span></p>' : '' )
				. '<h3 class="wp-block-heading">' . $title . '</h3>' . $p
				. ( $when ? '<p class="x-inline"><span class="when">' . occ_list_icon( occ_list_value( $id, 'when_icon' ) ) . ' ' . esc_html( $when ) . '</span></p>' : '' )
				. '</div>';

		case 'about-leaders':
		case 'home-leaders':
			$photo = (int) occ_list_value( $id, 'photo' );
			$img   = $photo ? wp_get_attachment_image( $photo, 'large', false, array( 'alt' => '', 'class' => 'wp-image-' . $photo, 'loading' => false, 'decoding' => 'async' ) ) : '';
			$role  = occ_list_value( $id, 'role' );
			return '<div class="wp-block-group leader reveal">'
				. ( $img ? '<figure class="wp-block-image avatar has-photo">' . $img . '</figure>' : '<figure class="wp-block-image avatar"></figure>' )
				. '<h3 class="wp-block-heading">' . $title . '</h3>' . ( $role ? '<p>' . esc_html( $role ) . '</p>' : '' ) . '</div>';
	}
	return '';
}

add_shortcode(
	'occ_list',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'row' => '' ), $atts );
		$row  = sanitize_key( $atts['row'] );
		// Curly quotes and dashes, as WordPress gives the rest of the page text.
		return wptexturize( implode( '', array_map( fn( $id ) => occ_list_card( $id, $row ), occ_list_items( $row ) ) ) );
	}
);

/* ----------------------------------------------------------------------------------------------------------------
 * Moving the cards already in the pages into the menus
 * ------------------------------------------------------------------------------------------------------------- */

/**
 * Read one card block into field values.
 *
 * @param array  $block Card block (parsed).
 * @param string $row   Row.
 * @return array|null [title, fields] or null when the card isn't recognised.
 */
function occ_list_read_card( $block, $row ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return null;
	}
	$raw     = serialize_blocks( $block['innerBlocks'] );
	$classes = ' ' . ( $block['attrs']['className'] ?? '' ) . ' ';
	$anchor  = $block['attrs']['anchor'] ?? '';
	$doc     = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="occ-root">' . $raw . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$xp    = new DOMXPath( $doc );
	$first = function ( $q ) use ( $xp ) {
		$n = $xp->query( $q );
		return $n && $n->length ? $n->item( 0 ) : null;
	};
	$inner = function ( $node ) use ( $doc ) {
		$html = '';
		foreach ( $node->childNodes as $child ) {
			$html .= $doc->saveHTML( $child );
		}
		return trim( $html );
	};
	$text = function ( $node ) {
		return $node ? trim( preg_replace( '/\s+/u', ' ', preg_replace( '/\[occ_icon[^\]]*\]/', '', $node->textContent ) ) ) : '';
	};
	$icon_in = function ( $html ) {
		return preg_match( '/\[occ_icon name="([\w-]+)"/', $html, $m ) ? $m[1] : '';
	};
	$cls  = fn( $c ) => "contains(concat(' ', normalize-space(@class), ' '), ' $c ')";
	$h3   = $first( '//h3' );
	$desc = $first( '//p[not(' . $cls( 'x-inline' ) . ')]' );
	if ( ! $h3 ) {
		return null;
	}
	$title  = $text( $h3 );
	$fields = array( 'text' => $desc ? occ_list_kses( $inner( $desc ) ) : '' );
	$when   = $first( '//span[' . $cls( 'when' ) . ']' );
	$chip   = $first( '//span[' . $cls( 'chip' ) . ']' );

	switch ( $row ) {
		case 'events-weekly':
			$day    = $first( '//*[' . $cls( 'ev-date' ) . ']//span' );
			$time   = $first( '//*[' . $cls( 'ev-date' ) . ']//strong' );
			$where  = $first( '//span[' . $cls( 'ev-where' ) . ']' );
			$fields += array(
				'section' => 'weekly',
				'day'     => $day ? substr( $text( $day ), 0, 3 ) : 'Sun',
				'time'    => $text( $time ),
				'place'   => $text( $where ),
			);
			if ( ! isset( occ_list_choices( 'day' )[ $fields['day'] ] ) ) {
				return null;
			}
			break;

		case 'events-calendar':
		case 'home-highlights':
			$link   = $first( '//a[' . $cls( 'card-link' ) . ']' );
			$colour = preg_match( '/ (p[1-7]) /', $classes, $m ) ? $m[1] : 'p1';
			$fields += array(
				'label'  => $text( $when ),
				'colour' => $colour,
				'icon'   => $icon_in( $raw ) ? $icon_in( $raw ) : 'calendar',
				'link'   => $link ? $link->getAttribute( 'href' ) : '',
			);
			if ( 'events-calendar' === $row ) {
				$fields += array( 'section' => 'calendar', 'show_home' => '0' );
			}
			break;

		case 'ministries-kids':
			$colour = preg_match( '/ (t-[a-z]+) /', $classes, $m ) ? $m[1] : 't-yellow';
			$fields += array(
				'section'   => 'kids',
				'chip'      => $text( $chip ),
				'when'      => $text( $when ),
				'when_icon' => $when && $icon_in( $inner( $when ) ) ? $icon_in( $inner( $when ) ) : 'clock',
				'colour'    => $colour,
				'anchor'    => $anchor,
			);
			break;

		case 'ministries-adults':
			$ic      = $first( '//span[' . $cls( 'ic' ) . ']' );
			$fields += array(
				'section'   => 'adults',
				'chip'      => $text( $chip ),
				'when'      => $text( $when ),
				'when_icon' => $when && $icon_in( $inner( $when ) ) ? $icon_in( $inner( $when ) ) : 'clock',
				'icon'      => $ic && $icon_in( $inner( $ic ) ) ? $icon_in( $inner( $ic ) ) : 'users',
				'featured'  => false !== strpos( $classes, ' card-feature ' ) ? '1' : '0',
				'anchor'    => $anchor,
			);
			break;

		case 'about-leaders':
		case 'home-leaders':
			$img  = $first( '//img' );
			$role = $first( '//p' );
			$src  = $img ? $img->getAttribute( 'src' ) : '';
			$pid  = 0;
			foreach ( $block['innerBlocks'] as $b ) {
				if ( 'core/image' === $b['blockName'] && ! empty( $b['attrs']['id'] ) ) {
					$pid = (int) $b['attrs']['id'];
				}
			}
			if ( ! $pid && $src ) {
				$pid = attachment_url_to_postid( preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', strtok( $src, '?' ) ) );
				if ( ! $pid && preg_match( '#/([\w.-]+\.(?:jpe?g|png|webp))#i', $src, $m ) ) {
					// A photo still served from the theme folder: use its Media Library copy if the theme made one.
					$media = (array) get_option( 'occ_media', array() );
					$pid   = (int) ( $media[ 'img/leaders/' . $m[1] ] ?? 0 );
				}
			}
			$fields = array(
				'role'  => $text( $role ),
				'photo' => (string) $pid,
			);
			break;

		default:
			return null;
	}
	return array( $title, $fields );
}

/**
 * Find the row group in a list of blocks (depth first).
 *
 * @param array  $blocks Blocks, by reference.
 * @param string $class  Row class.
 * @return array|null Reference path.
 */
function &occ_list_find_row( array &$blocks, $class ) {
	$none = null;
	foreach ( $blocks as &$block ) {
		$classes = ' ' . ( $block['attrs']['className'] ?? '' ) . ' ';
		if ( 'core/group' === $block['blockName'] && false !== strpos( $classes, ' ' . $class . ' ' ) ) {
			return $block;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = &occ_list_find_row( $block['innerBlocks'], $class );
			if ( null !== $found ) {
				return $found;
			}
		}
	}
	return $none;
}

/**
 * Whether a row already prints from its menu.
 *
 * @param array  $row_block Row group.
 * @param string $row       Row.
 * @return bool
 */
function occ_list_is_converted( $row_block, $row ) {
	return false !== strpos( serialize_blocks( $row_block['innerBlocks'] ), '[occ_list row="' . $row . '"]' );
}

/**
 * Move the cards of one page's rows into the menus and put the [occ_list] codes in their place.
 *
 * @param int  $page_id Page.
 * @param bool $seed    Create items from the cards (only for rows that have never been moved).
 * @return bool Whether the page changed.
 */
function occ_list_convert_page( $page_id, $seed = true ) {
	$key  = get_post_meta( $page_id, '_occ_key', true );
	$done = (array) get_option( 'occ_lists_moved', array() );
	$post = get_post( $page_id );
	if ( ! $key || ! $post ) {
		return false;
	}
	$blocks  = parse_blocks( $post->post_content );
	$changed = false;
	$created = array();
	foreach ( occ_list_rows() as $row => $def ) {
		if ( $def[0] !== $key ) {
			continue;
		}
		$row_block = &occ_list_find_row( $blocks, $def[1] );
		if ( null === $row_block ) {
			unset( $row_block );
			continue;
		}
		if ( occ_list_is_converted( $row_block, $row ) ) {
			$done[ $row ] = 1;
			unset( $row_block );
			continue;
		}
		$cards = array();
		$ok    = true;
		foreach ( $row_block['innerBlocks'] as $card ) {
			$read = occ_list_read_card( $card, $row );
			if ( null === $read ) {
				$ok = false;
				break;
			}
			$cards[] = $read;
		}
		if ( ! $ok || ! $cards ) {
			unset( $row_block );
			continue; // changed beyond recognition: leave the row as page content
		}
		// Seed the menu once per row. The Home leaders are the same people as the About leaders.
		$seed_row = $seed && empty( $done[ $row ] ) && 'home-leaders' !== $row;
		if ( 'about-leaders' === $row && occ_list_count( 'occ_leader' ) ) {
			$seed_row = false;
		}
		if ( $seed_row ) {
			foreach ( $cards as $i => $card ) {
				$id = wp_insert_post(
					array(
						'post_type'   => $def[2],
						'post_status' => 'publish',
						'post_title'  => wp_slash( $card[0] ),
						'menu_order'  => ( $i + 1 ) * 10,
					)
				);
				if ( $id && ! is_wp_error( $id ) ) {
					foreach ( $card[1] as $field => $value ) {
						update_post_meta( $id, '_occ_' . $field, wp_slash( $value ) );
					}
					$created[] = $id;
				}
			}
		}
		$html                      = "\n" . '[occ_list row="' . $row . '"]' . "\n";
		$open                      = is_string( $row_block['innerContent'][0] ?? null ) ? $row_block['innerContent'][0] : '';
		$close                     = is_string( end( $row_block['innerContent'] ) ) ? end( $row_block['innerContent'] ) : '';
		$row_block['innerBlocks']  = array(
			array(
				'blockName'    => 'core/html',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => $html,
				'innerContent' => array( $html ),
			),
		);
		$row_block['innerContent'] = array( $open, null, $close );
		$row_block['innerHTML']    = $open . $close;
		$done[ $row ]              = 1;
		$changed                   = true;
		unset( $row_block );
	}
	update_option( 'occ_lists_moved', $done, false );
	if ( $changed ) {
		kses_remove_filters();
		$result = wp_update_post( array( 'ID' => $page_id, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ), true );
		kses_init();
		if ( is_wp_error( $result ) ) {
			foreach ( $created as $id ) {
				wp_delete_post( $id, true );
			}
			return false;
		}
		occ_list_changed();
	}
	return $changed;
}

/**
 * Number of items of a menu, in any state but trash.
 *
 * @param string $type Post type.
 * @return int
 */
function occ_list_count( $type ) {
	$counts = wp_count_posts( $type );
	return (int) ( $counts->publish + $counts->draft + $counts->pending + $counts->private + $counts->future );
}

/**
 * Move the cards of every design page (once per theme version).
 */
function occ_list_convert_all() {
	occ_add_pastor_bio();
	foreach ( array( 'about', 'events', 'ministries', 'home' ) as $key ) {
		$id = occ_list_page( $key );
		if ( $id ) {
			occ_list_convert_page( $id );
		}
	}
	update_option( 'occ_lists_version', 1, false );
}

/**
 * Theme 0.5 adds Pastor Jerry's bio under the leaders on About. A site that already uses the theme gets the design's
 * bio section added after its leaders row, once, as ordinary page blocks (edited in Pages > About like any text).
 */
function occ_add_pastor_bio() {
	$id   = occ_list_page( 'about' );
	$post = $id ? get_post( $id ) : null;
	if ( ! $post || get_option( 'occ_bio_added' ) ) {
		return;
	}
	update_option( 'occ_bio_added', 1, false );
	if ( false !== strpos( $post->post_content, 'pastor-bio' ) ) {
		return;
	}
	$design = parse_blocks( occ_design_content( 'about' ) );
	$bio    = &occ_list_find_row( $design, 'pastor-bio' );
	if ( null === $bio ) {
		return;
	}
	$bio_block = $bio;
	unset( $bio );
	$blocks = parse_blocks( $post->post_content );
	$added  = occ_insert_after_class( $blocks, 'leaders', $bio_block );
	if ( $added ) {
		kses_remove_filters();
		wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
		kses_init();
		occ_list_changed();
	}
}

/**
 * Insert a block right after the first group with a class, at the same level.
 *
 * @param array  $blocks Blocks, by reference.
 * @param string $class  Class.
 * @param array  $insert Block to insert.
 * @return bool
 */
function occ_insert_after_class( array &$blocks, $class, $insert ) {
	foreach ( $blocks as &$block ) {
		foreach ( $block['innerBlocks'] as $i => $child ) {
			$classes = ' ' . ( $child['attrs']['className'] ?? '' ) . ' ';
			if ( 'core/group' === $child['blockName'] && false !== strpos( $classes, ' ' . $class . ' ' ) ) {
				array_splice( $block['innerBlocks'], $i + 1, 0, array( $insert ) );
				// innerContent holds one null per inner block: add one after the $i-th.
				$seen = -1;
				foreach ( $block['innerContent'] as $pos => $piece ) {
					if ( null === $piece && ++$seen === $i ) {
						array_splice( $block['innerContent'], $pos + 1, 0, array( "\n\n", null ) );
						break;
					}
				}
				return true;
			}
		}
		if ( $block['innerBlocks'] && occ_insert_after_class( $block['innerBlocks'], $class, $insert ) ) {
			return true;
		}
	}
	return false;
}

// After activation, and once on a site that already uses the theme when the updated theme arrives.
add_action( 'after_switch_theme', 'occ_list_convert_all', 20 );
add_action(
	'admin_init',
	function () {
		if ( (int) get_option( 'occ_lists_version' ) < 1 && current_user_can( 'edit_pages' ) && ! wp_doing_ajax() ) {
			occ_list_convert_all();
		}
	}
);
// "Reset to the design" brings back the design's own cards: print the menus' items there again instead.
add_action(
	'occ_after_reset',
	function ( $id ) {
		occ_list_convert_page( $id, false );
	}
);

/* ----------------------------------------------------------------------------------------------------------------
 * List screens
 * ------------------------------------------------------------------------------------------------------------- */

foreach ( array_keys( occ_list_types() ) as $occ_type ) {
	add_filter(
		'manage_' . $occ_type . '_posts_columns',
		function ( $cols ) use ( $occ_type ) {
			$new = array();
			foreach ( $cols as $k => $v ) {
				if ( 'date' === $k ) {
					$new['occ_info']  = 'occ_leader' === $occ_type ? __( 'Role', 'one-christian-church' ) : __( 'When', 'one-christian-church' );
					$new['occ_where'] = __( 'Shows on', 'one-christian-church' );
					$new['occ_order'] = __( 'Order', 'one-christian-church' );
					$new['occ_state'] = __( 'Status', 'one-christian-church' );
					continue;
				}
				$new[ $k ] = $v;
			}
			return $new;
		}
	);
	add_action(
		'manage_' . $occ_type . '_posts_custom_column',
		function ( $col, $id ) {
			$names = array(
				'events-weekly'     => __( 'Events: Every week', 'one-christian-church' ),
				'events-calendar'   => __( 'Events: Mark your calendar', 'one-christian-church' ),
				'home-highlights'   => __( 'Home: What\'s coming up', 'one-christian-church' ),
				'ministries-kids'   => __( 'Ministries: Kids & youth', 'one-christian-church' ),
				'ministries-adults' => __( 'Ministries: Adults', 'one-christian-church' ),
				'about-leaders'     => __( 'About', 'one-christian-church' ),
				'home-leaders'      => __( 'Home', 'one-christian-church' ),
			);
			switch ( $col ) {
				case 'occ_info':
					$type = get_post_type( $id );
					if ( 'occ_leader' === $type ) {
						echo esc_html( occ_list_value( $id, 'role' ) );
					} elseif ( 'occ_event' === $type && 'weekly' === occ_list_value( $id, 'section' ) ) {
						echo esc_html( occ_list_value( $id, 'day' ) . ' ' . occ_list_value( $id, 'time' ) );
					} elseif ( 'occ_event' === $type ) {
						echo esc_html( occ_event_when( $id ) );
					} elseif ( 'occ_ministry' === $type ) {
						echo esc_html( occ_list_value( $id, 'when' ) );
					} else {
						echo esc_html( occ_list_value( $id, 'label' ) );
					}
					break;
				case 'occ_where':
					echo esc_html( implode( ', ', array_map( fn( $r ) => $names[ $r ] ?? $r, occ_list_rows_for( $id ) ) ) );
					break;
				case 'occ_order':
					echo (int) get_post_field( 'menu_order', $id );
					break;
				case 'occ_state':
					if ( 'publish' !== get_post_status( $id ) ) {
						esc_html_e( 'Not on the site (draft)', 'one-christian-church' );
						break;
					}
					$labels = array(
						'showing'   => __( 'On the site', 'one-christian-church' ),
						'scheduled' => __( 'Scheduled', 'one-christian-church' ),
						'ended'     => __( 'Ended (hidden)', 'one-christian-church' ),
					);
					echo esc_html( $labels[ occ_list_state( $id ) ] );
					break;
			}
		},
		10,
		2
	);
}

// List screens sort by the Order number, as the site does.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() && $query->is_main_query() && isset( occ_list_types()[ $query->get( 'post_type' ) ] ) && ! $query->get( 'orderby' ) ) {
			if ( in_array( $query->get( 'post_type' ), array( 'occ_event', 'occ_ministry' ), true ) ) {
				// Grouped by where they show, as on the site.
				$query->set( 'meta_key', '_occ_section' );
				$query->set( 'orderby', array( 'meta_value' => 'ASC', 'menu_order' => 'ASC', 'ID' => 'ASC' ) );
			} else {
				$query->set( 'orderby', array( 'menu_order' => 'ASC', 'ID' => 'ASC' ) );
			}
		}
	}
);
