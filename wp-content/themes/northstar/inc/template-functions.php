<?php
/**
 * Theme presentation helpers.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the site logo as a text fallback.
 *
 * @return void
 */
function northstar_site_branding() {
	$home_url  = home_url( '/' );
	$site_name = get_bloginfo( 'name' );
	// This is temperory solution.
	$site_name = str_replace(
		'star.',
		'<span>star.</span>',
		$site_name
	);

	
	?>
	<a class="logo" href="<?php echo esc_url( $home_url ); ?>" rel="home">
		<?php echo wp_kses( $site_name, array( 'span' => array() ) ); ?>
	</a>
	<?php
}

/**
 * Fallback menu when no menu has been assigned.
 *
 * @return void
 */
function northstar_primary_menu_fallback() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	?>
	<ul class="northstar-menu">
		<li><a href="<?php echo esc_url( $shop_url ? $shop_url : home_url( '/' ) ); ?>"><?php esc_html_e( 'Shop all', 'northstar' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#everyday-favourites"><?php esc_html_e( 'Everyday favourites', 'northstar' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Return the announcement bar copy.
 *
 * @return string
 */
function northstar_announcement_text() {
	$text = get_option( 'northstar_announcement_text', '' );

	return is_string( $text ) ? $text : '';
}
