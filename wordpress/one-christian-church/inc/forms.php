<?php
/**
 * The Contact and Plan a Visit forms: [occ_form type="contact"] and [occ_form type="visit"].
 *
 * A form posts back to its own page. The answers are emailed to the address in Appearance > Customize > Forms
 * (the reply goes straight to the person who wrote) and kept under Messages in the dashboard, so nothing is lost
 * if an email goes astray. Spam protection: a hidden field people never fill in, a minimum time on the page,
 * and at most five messages per visitor every ten minutes.
 *
 * To use a form plugin instead (Ninja Forms, WPForms...), replace the Shortcode block on the page with the
 * plugin's own block or shortcode.
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'occ_message',
			array(
				'labels'       => array(
					'name'          => __( 'Messages', 'one-christian-church' ),
					'singular_name' => __( 'Message', 'one-christian-church' ),
					'edit_item'     => __( 'Message', 'one-christian-church' ),
					'search_items'  => __( 'Search messages', 'one-christian-church' ),
					'not_found'     => __( 'No messages yet.', 'one-christian-church' ),
				),
				'description'  => __( 'Messages sent with the website forms.', 'one-christian-church' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'menu_icon'    => 'dashicons-email-alt',
				'supports'     => array( 'title' ),
				'map_meta_cap' => true,
				'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			)
		);
		occ_handle_form();
	}
);

/**
 * Form spec from theme-data.json.
 *
 * @param string $type Form type.
 * @return array|null
 */
function occ_form_spec( $type ) {
	return occ_data()['forms'][ $type ] ?? null;
}

add_shortcode(
	'occ_form',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'type' => 'contact' ), $atts );
		$type = sanitize_key( $atts['type'] );
		$file = get_theme_file_path( 'inc/forms/' . $type . '.html' );
		if ( ! occ_form_spec( $type ) || ! file_exists( $file ) ) {
			return '';
		}
		$here   = get_permalink() ? get_permalink() : home_url( '/' );
		$hidden = sprintf(
			'<input type="hidden" name="occ_form" value="%1$s"><input type="hidden" name="occ_back" value="%2$s"><input type="hidden" name="occ_t" value="%3$d">'
			. '<div class="occ-hp" aria-hidden="true"><label>%4$s <input type="text" name="occ_website" tabindex="-1" autocomplete="off"></label></div>',
			esc_attr( $type ),
			esc_url( $here ),
			time(),
			esc_html__( 'Leave this empty', 'one-christian-church' )
		);
		$status = '';
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- only picks which thank-you or error line to show.
		if ( isset( $_GET['occ_sent'] ) && $type === $_GET['occ_sent'] ) {
			$status = '<p class="form-ok" role="status">' . esc_html( occ_form_spec( $type )['sent'] ) . '</p>';
		} elseif ( isset( $_GET['occ_error'] ) && $type === $_GET['occ_error'] ) {
			$reason = sanitize_key( wp_unslash( $_GET['occ_reason'] ?? '' ) );
			$text   = 'busy' === $reason
				? __( 'That was a lot of messages in a short time. Please try again in a few minutes, or call the office.', 'one-christian-church' )
				: __( 'Please fill in your name, a valid email address and the other required fields, then send again.', 'one-christian-church' );
			$status = '<p class="form-error" role="alert">' . esc_html( $text ) . '</p>';
		}
		// phpcs:enable
		return strtr(
			file_get_contents( $file ),
			array(
				'{{form_action}}' => esc_url( $here . '#occ-form-' . $type ),
				'{{form_hidden}}' => $hidden,
				'{{form_status}}' => $status,
			)
		);
	}
);

/**
 * Handle a form post, then send the visitor back to the page with a thank-you (or what to fix).
 */
function occ_handle_form() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- a public form; pages are often cached, which breaks nonces. See the spam checks.
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['occ_form'] ) ) {
		return;
	}
	$type = sanitize_key( wp_unslash( $_POST['occ_form'] ) );
	$spec = occ_form_spec( $type );
	if ( ! $spec ) {
		return;
	}
	$back = wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['occ_back'] ?? '' ) ), home_url( '/' ) );
	$done = function ( $args ) use ( $back, $type ) {
		wp_safe_redirect( add_query_arg( $args, remove_query_arg( array( 'occ_sent', 'occ_error', 'occ_reason' ), $back ) ) . '#occ-form-' . $type, 303 );
		exit;
	};

	// Bots fill in the hidden field or post within a moment of loading the page: pretend it worked.
	$started = (int) ( $_POST['occ_t'] ?? 0 );
	if ( ! empty( $_POST['occ_website'] ) || ( $started && time() - $started < 3 ) ) {
		$done( array( 'occ_sent' => $type ) );
	}

	$fields = array();
	$valid  = true;
	foreach ( $spec['fields'] as $field ) {
		$raw   = wp_unslash( $_POST[ $field['name'] ] ?? '' );
		$value = 'textarea' === $field['type'] ? str_replace( "\r\n", "\n", sanitize_textarea_field( $raw ) ) : sanitize_text_field( $raw );
		if ( 'email' === $field['type'] ) {
			$value = sanitize_email( $value );
			$valid = $valid && ( '' === $value || is_email( $value ) );
		}
		if ( 'select' === $field['type'] && '' !== $value && ! in_array( $value, $field['options'], true ) ) {
			$value = '';
		}
		$valid = $valid && ! ( $field['required'] && '' === $value );
		$fields[ $field['name'] ] = array( 'label' => $field['label'], 'value' => $value );
	}
	// phpcs:enable
	if ( ! $valid ) {
		$done( array( 'occ_error' => $type, 'occ_reason' => 'fields' ) );
	}

	// Behind a proxy or CDN every visitor can arrive from the same address, so the visitor's own address, as the proxy
	// passes it on, is counted too.
	// phpcs:disable WordPress.Security.ValidatedSanitizedInput -- only hashed.
	$forwarded = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? strtok( (string) ( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '' ), ',' );
	$visitor   = 'occ_form_' . md5( ( $_SERVER['REMOTE_ADDR'] ?? '' ) . '|' . trim( (string) $forwarded ) . wp_salt() );
	// phpcs:enable
	$count   = (int) get_transient( $visitor );
	if ( $count >= 5 ) {
		$done( array( 'occ_error' => $type, 'occ_reason' => 'busy' ) );
	}
	set_transient( $visitor, $count + 1, 10 * MINUTE_IN_SECONDS );

	$name  = $fields['name']['value'] ?? '';
	$email = $fields['email']['value'] ?? '';
	$lines = array();
	foreach ( $fields as $field ) {
		$lines[] = $field['label'] . ': ' . ( '' === $field['value'] ? '-' : $field['value'] );
	}
	$lines[] = '';
	/* translators: %s: page address */
	$lines[] = sprintf( __( 'Sent from %s', 'one-christian-church' ), $back );
	$body    = implode( "\n", $lines );

	$id = wp_insert_post(
		array(
			'post_type'    => 'occ_message',
			'post_status'  => 'private',
			/* translators: 1: form name, 2: sender's name */
			'post_title'   => wp_slash( sprintf( __( '%1$s: %2$s', 'one-christian-church' ), $spec['label'], $name ) ),
			'post_content' => wp_slash( $body ),
		)
	);

	$to      = get_theme_mod( 'occ_form_email', occ_data()['form_email'] );
	$to      = is_email( $to ) ? $to : get_option( 'admin_email' );
	$subject = sprintf( '[%1$s] %2$s: %3$s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $spec['subject'], $name );
	$headers = $email ? array( sprintf( 'Reply-To: %s <%s>', str_replace( array( '"', '<', '>', "\r", "\n" ), '', $name ), $email ) ) : array();
	$mailed  = wp_mail( $to, $subject, $body, $headers );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_occ_form', $type );
		update_post_meta( $id, '_occ_fields', wp_slash( $fields ) );
		update_post_meta( $id, '_occ_mailed', $mailed ? 1 : 0 );
	}
	do_action( 'occ_form_sent', $type, $fields, $mailed );
	$done( array( 'occ_sent' => $type ) );
}

/**
 * Messages screen: show what was sent, and whether the email went out.
 */
add_action(
	'add_meta_boxes_occ_message',
	function ( $post ) {
		add_meta_box(
			'occ_message_body',
			__( 'Message', 'one-christian-church' ),
			function ( $post ) {
				$fields = (array) get_post_meta( $post->ID, '_occ_fields', true );
				echo '<table class="form-table" role="presentation"><tbody>';
				foreach ( $fields as $field ) {
					printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', esc_html( $field['label'] ), nl2br( esc_html( $field['value'] ) ) );
				}
				echo '</tbody></table>';
				printf(
					'<p><strong>%1$s</strong> %2$s</p>',
					esc_html__( 'Email:', 'one-christian-church' ),
					get_post_meta( $post->ID, '_occ_mailed', true )
						? esc_html__( 'sent to the church address.', 'one-christian-church' )
						: esc_html__( 'WordPress could not send it. Check the address in Appearance > Customize > Forms, or ask the host about email (an SMTP plugin usually fixes this).', 'one-christian-church' )
				);
			},
			'occ_message',
			'normal',
			'high'
		);
	}
);

add_filter(
	'manage_occ_message_posts_columns',
	function ( $columns ) {
		return array(
			'cb'         => $columns['cb'],
			'title'      => __( 'Message', 'one-christian-church' ),
			'occ_email'  => __( 'Email', 'one-christian-church' ),
			'occ_mailed' => __( 'Emailed', 'one-christian-church' ),
			'date'       => $columns['date'],
		);
	}
);

add_action(
	'manage_occ_message_posts_custom_column',
	function ( $column, $id ) {
		$fields = (array) get_post_meta( $id, '_occ_fields', true );
		if ( 'occ_email' === $column ) {
			echo esc_html( $fields['email']['value'] ?? '' );
		} elseif ( 'occ_mailed' === $column ) {
			echo get_post_meta( $id, '_occ_mailed', true ) ? '&#10003;' : esc_html__( 'No', 'one-christian-church' );
		}
	},
	10,
	2
);
