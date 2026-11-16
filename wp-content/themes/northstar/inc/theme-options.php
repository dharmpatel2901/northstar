
<?php
/**
 * Northstar Theme Options.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Theme Options page.
 */
function northstar_add_theme_options_page() {
	add_menu_page(
		__( 'Theme Options', 'northstar' ),
		__( 'Theme Options', 'northstar' ),
		'manage_options',
		'northstar-options',
		'northstar_render_theme_options',
		'dashicons-admin-generic'
	);
}
add_action( 'admin_menu', 'northstar_add_theme_options_page' );

/**
 * Register theme options.
 */
function northstar_register_theme_options() {

    // Header settings.
    register_setting(
        'northstar_header_options_group',
        'northstar_announcement_text',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    // Footer settings.
    register_setting(
        'northstar_footer_options_group',
        'northstar_footer_description',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default'           => '',
        )
    );

}
add_action( 'admin_init', 'northstar_register_theme_options' );

/**
 * Render the Theme Options page.
 */
function northstar_render_theme_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] )
		? sanitize_key( wp_unslash( $_GET['tab'] ) )
		: 'header';

	$tabs = array(
		'header' => __( 'Header', 'northstar' ),
		'footer' => __( 'Footer', 'northstar' ),
		'general' => __( 'General', 'northstar' ),
	);

	if ( ! isset( $tabs[ $active_tab ] ) ) {
		$active_tab = 'header';
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Theme Options', 'northstar' ); ?></h1>

		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Theme Options', 'northstar' ); ?>">
			<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
				<a
					href="<?php echo esc_url( add_query_arg( array(
						'page' => 'northstar-options',
						'tab'  => $tab_key,
					), admin_url( 'admin.php' ) ) ); ?>"
					class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>"
				>
					<?php echo esc_html( $tab_label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php">
			<?php
			if ( 'header' === $active_tab ) {
				settings_fields( 'northstar_header_options_group' );
			} elseif ( 'footer' === $active_tab ) {
				settings_fields( 'northstar_footer_options_group' );
			} else {
				settings_fields( 'northstar_general_options_group' );
			}
			?>

			<?php if ( 'header' === $active_tab ) : ?>
				<h2><?php esc_html_e( 'Header Settings', 'northstar' ); ?></h2>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="northstar_announcement_text">
								<?php esc_html_e( 'Announcement Bar Text', 'northstar' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="northstar_announcement_text"
								name="northstar_announcement_text"
								value="<?php echo esc_attr( get_option(
									'northstar_announcement_text', '') ); ?>"
								class="regular-text"
							>
							<p class="description">
								<?php esc_html_e( 'Text displayed in the announcement bar at the top of the website.', 'northstar' ); ?>
							</p>
						</td>
					</tr>
				</table>

			<?php elseif ( 'footer' === $active_tab ) : ?>
				<h2><?php esc_html_e( 'Footer Settings', 'northstar' ); ?></h2>
				<table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="northstar_footer_description">
                                <?php esc_html_e( 'Footer Description', 'northstar' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea
                                id="northstar_footer_description"
                                name="northstar_footer_description"
                                rows="3"
                                class="large-text"
                            ><?php echo esc_textarea( get_option( 'northstar_footer_description', '' ) ); ?></textarea>
                        </td>
                    </tr>
                </table>

			<?php elseif ( 'general' === $active_tab ) : ?>
				<h2><?php esc_html_e( 'General Settings', 'northstar' ); ?></h2>
				<p><?php esc_html_e( 'General settings will be added here.', 'northstar' ); ?></p>
			<?php endif; ?>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
