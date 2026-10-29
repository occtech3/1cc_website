<?php
/**
 * Appearance > Site guide: where everything on the site is edited, plus "Reset to the design" for each page.
 * Also adds "Edit footer" to the toolbar and warns about Elementor templates that would cover the theme's header.
 *
 * @package One_Christian_Church
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Site guide', 'one-christian-church' ), __( 'Site guide', 'one-christian-church' ), 'edit_pages', 'occ-guide', 'occ_guide_page' );
	}
);

/**
 * Elementor header or footer templates (Elementor Pro's Theme Builder) can replace the theme's own.
 *
 * @return int Number of published header and footer templates.
 */
function occ_elementor_theme_templates() {
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return 0;
	}
	return count(
		get_posts(
			array(
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'numberposts' => 20,
				'fields'      => 'ids',
				'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- admin screen only.
					array(
						'key'     => '_elementor_template_type',
						'value'   => array( 'header', 'footer' ),
						'compare' => 'IN',
					),
				),
			)
		)
	);
}

/**
 * The guide.
 */
function occ_guide_page() {
	$pages     = occ_design_pages();
	$footer_id = occ_footer_block_id();
	$customize = function ( $section ) {
		return admin_url( 'customize.php?autofocus[section]=' . $section );
	};
	$messages  = wp_count_posts( 'occ_message' );
	?>
	<div class="wrap occ-guide">
		<h1><?php esc_html_e( 'Site guide', 'one-christian-church' ); ?></h1>
		<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
		if ( isset( $_GET['occ_reset'] ) ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Done. The earlier version is still in the page\'s revisions.', 'one-christian-church' ); ?></p></div>
		<?php endif; ?>
		<?php if ( occ_elementor_theme_templates() ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'This site has Elementor header or footer templates (Templates > Theme Builder). If the site shows the old header or footer, set those templates to draft or remove their display conditions.', 'one-christian-church' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Pages', 'one-christian-church' ); ?></h2>
		<p><?php esc_html_e( 'Every page is made of ordinary blocks: click any heading, paragraph, list or button text to change it, and use Save to publish. The coloured backgrounds, videos and icons are Custom HTML blocks (shown as small "Design element" labels while editing) and short [occ_...] codes (listed below), so they are easy to keep, move or remove. A page\'s Excerpt, in the editor\'s sidebar, is the description search engines show.', 'one-christian-church' ); ?></p>
		<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
			<p><strong><?php esc_html_e( 'Elementor is active:', 'one-christian-church' ); ?></strong> <?php esc_html_e( 'edit these pages in the WordPress editor, not with "Edit with Elementor", which would replace the design. If no other pages need Elementor, it can be deactivated.', 'one-christian-church' ); ?></p>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th><?php esc_html_e( 'Page', 'one-christian-church' ); ?></th><th></th><th><?php esc_html_e( 'Back to the theme\'s design', 'one-christian-church' ); ?></th></tr></thead>
			<tbody>
			<?php
			foreach ( $pages as $key => $page ) :
				$found = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'meta_key' => '_occ_key', 'meta_value' => $key, 'numberposts' => 1 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
				$id    = $found ? $found[0]->ID : 0;
				?>
				<tr>
					<td><strong><?php echo esc_html( $page['title'] ); ?></strong></td>
					<td>
						<?php if ( $id ) : ?>
							<a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php esc_html_e( 'Edit', 'one-christian-church' ); ?></a> &middot;
							<a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php esc_html_e( 'View', 'one-christian-church' ); ?></a>
						<?php else : ?>
							<?php esc_html_e( 'Not created yet (activate the theme again)', 'one-christian-church' ); ?>
						<?php endif; ?>
					</td>
					<td><?php $id && occ_reset_button( $id ); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><strong><?php esc_html_e( 'Footer', 'one-christian-church' ); ?></strong></td>
				<td><?php if ( $footer_id ) : ?><a href="<?php echo esc_url( get_edit_post_link( $footer_id ) ); ?>"><?php esc_html_e( 'Edit', 'one-christian-church' ); ?></a><?php endif; ?></td>
				<td><?php $footer_id && occ_reset_button( $footer_id ); ?></td>
			</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Header, menu and footer', 'one-christian-church' ); ?></h2>
		<ul class="ul-disc">
			<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Appearance > Menus', 'one-christian-church' ); ?></a>: <?php esc_html_e( '"Main menu" is the header menu; "Search shortcuts" are the links shown when search opens.', 'one-christian-church' ); ?></li>
			<li><a href="<?php echo esc_url( $customize( 'occ_header' ) ); ?>"><?php esc_html_e( 'Customize > Header', 'one-christian-church' ); ?></a>: <?php esc_html_e( 'the coloured strip at the top, the name next to the logo and the "Plan a Visit" button.', 'one-christian-church' ); ?></li>
			<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>"><?php esc_html_e( 'Customize > Site Identity', 'one-christian-church' ); ?></a>: <?php esc_html_e( 'logo, site title and browser icon.', 'one-christian-church' ); ?></li>
			<li><?php if ( $footer_id ) : ?><a href="<?php echo esc_url( get_edit_post_link( $footer_id ) ); ?>"><?php esc_html_e( 'Site footer', 'one-christian-church' ); ?></a><?php else : esc_html_e( 'Site footer', 'one-christian-church' ); endif; ?>: <?php esc_html_e( 'the "Come as you are" band, addresses, phone, email and links. It is a synced pattern, edited like a page.', 'one-christian-church' ); ?></li>
			<li><a href="<?php echo esc_url( $customize( 'occ_videos' ) ); ?>"><?php esc_html_e( 'Customize > Background videos', 'one-christian-church' ); ?></a>: <?php esc_html_e( 'swap any clip for the church\'s own footage.', 'one-christian-church' ); ?></li>
		</ul>

		<h2><?php esc_html_e( 'Forms', 'one-christian-church' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: 1: email address, 2: link to the Customizer */
				esc_html__( 'The Contact and Plan a Visit forms email %1$s (change it in %2$s). Every message is also kept under %3$s.', 'one-christian-church' ),
				'<strong>' . esc_html( get_theme_mod( 'occ_form_email', occ_data()['form_email'] ) ) . '</strong>',
				'<a href="' . esc_url( $customize( 'occ_forms' ) ) . '">' . esc_html__( 'Customize > Forms', 'one-christian-church' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=occ_message' ) ) . '">' . esc_html__( 'Messages', 'one-christian-church' ) . '</a> (' . (int) ( $messages->private ?? 0 ) . ')'
			);
			?>
		</p>
		<p><?php esc_html_e( 'If emails don\'t arrive, the messages are still saved; an SMTP plugin (for example WP Mail SMTP) usually fixes delivery. To use a form plugin such as Ninja Forms instead, replace the Shortcode block [occ_form type="contact"] on the Contact page (or type="visit" on Plan a Visit) with the plugin\'s form block.', 'one-christian-church' ); ?></p>

		<h2><?php esc_html_e( 'Search', 'one-christian-church' ); ?></h2>
		<p><?php esc_html_e( 'The magnifying glass in the header searches every published page. It updates by itself whenever a page is saved.', 'one-christian-church' ); ?></p>

		<h2><?php esc_html_e( 'Codes used in the page content', 'one-christian-church' ); ?></h2>
		<table class="widefat striped" style="max-width:900px"><tbody>
			<tr><td><code>[occ_icon name="arrow"]</code></td><td><?php echo esc_html( implode( ', ', array_keys( occ_fragments()['icons'] ) ) ); ?>. <?php esc_html_e( 'Add size="lg" for a big one.', 'one-christian-church' ); ?></td></tr>
			<tr><td><code>[occ_video slot="kids"]</code></td><td><?php echo esc_html( implode( ', ', array_keys( occ_data()['videos'] ) ) ); ?></td></tr>
			<tr><td><code>[occ_form type="contact"]</code></td><td><?php esc_html_e( 'contact or visit', 'one-christian-church' ); ?></td></tr>
			<tr><td><code>[occ_logo3d kind="badge"]</code></td><td><?php esc_html_e( 'The turning 3D logo: hero, page, cta or badge', 'one-christian-church' ); ?></td></tr>
			<tr><td><code>[occ_ring text="..."]</code></td><td><?php esc_html_e( 'Words running round the circle on the home page', 'one-christian-church' ); ?></td></tr>
			<tr><td><code>[occ_map]</code> <code>[occ_wave]</code> <code>[occ_aurora]</code> <code>[occ_logo]</code></td><td><?php esc_html_e( 'Map, wavy section edge, colour glow, church logo', 'one-christian-church' ); ?></td></tr>
		</tbody></table>

		<h2><?php esc_html_e( 'Switching themes', 'one-christian-church' ); ?></h2>
		<p><?php esc_html_e( 'Switching to another theme puts back the pages, Elementor settings, menus and front page the site had before this theme was activated, and keeps this theme\'s versions for next time. Pages this theme created are set to draft.', 'one-christian-church' ); ?></p>
	</div>
	<?php
}

/**
 * A "Reset to the design" button for a page or the footer pattern.
 *
 * @param int $id Post ID.
 */
function occ_reset_button( $id ) {
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Replace this page\'s content with the theme\'s design? The current version stays in the revisions.', 'one-christian-church' ) ); ?>');" style="margin:0">
		<input type="hidden" name="action" value="occ_reset">
		<input type="hidden" name="id" value="<?php echo (int) $id; ?>">
		<?php wp_nonce_field( 'occ_reset_' . $id ); ?>
		<button class="button" type="submit"><?php esc_html_e( 'Reset to the design', 'one-christian-church' ); ?></button>
	</form>
	<?php
}

add_action(
	'admin_post_occ_reset',
	function () {
		$id = (int) ( $_POST['id'] ?? 0 );
		check_admin_referer( 'occ_reset_' . $id );
		if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
			wp_die( esc_html__( 'You can\'t edit this page.', 'one-christian-church' ) );
		}
		occ_reset_to_design( $id );
		wp_safe_redirect( admin_url( 'themes.php?page=occ-guide&occ_reset=1' ) );
		exit;
	}
);

add_action(
	'admin_bar_menu',
	function ( $bar ) {
		if ( is_admin() || ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		$footer = occ_footer_block_id();
		if ( $footer ) {
			$bar->add_node( array( 'id' => 'occ-footer', 'title' => __( 'Edit footer', 'one-christian-church' ), 'href' => get_edit_post_link( $footer ) ) );
		}
		$bar->add_node( array( 'id' => 'occ-guide', 'title' => __( 'Site guide', 'one-christian-church' ), 'href' => admin_url( 'themes.php?page=occ-guide' ) ) );
	},
	90
);

add_action(
	'admin_notices',
	function () {
		if ( ! get_transient( 'occ_just_installed' ) || ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		delete_transient( 'occ_just_installed' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
			esc_html__( 'The pages now use the One Christian Church design, and every page can be edited in the block editor.', 'one-christian-church' ),
			esc_url( admin_url( 'themes.php?page=occ-guide' ) ),
			esc_html__( 'Open the site guide', 'one-christian-church' )
		);
	}
);
