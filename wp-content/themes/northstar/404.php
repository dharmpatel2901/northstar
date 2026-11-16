<?php
/**
 * Not-found template.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="empty-state">
	<h1><?php esc_html_e( 'Page not found', 'northstar' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for may have moved.', 'northstar' ); ?></p>
	<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'northstar' ); ?></a>
</section>
<?php
get_footer();
