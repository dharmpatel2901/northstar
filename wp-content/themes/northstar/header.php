<?php
/**
 * Header template.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $announcement = northstar_announcement_text(); ?>

<?php if ( '' !== trim( $announcement ) ) : ?>
    <div class="announcement">
        <?php echo esc_html( $announcement ); ?>
    </div>
<?php endif; ?>
<header class="site-header">
	<div class="header-inner">
		<button class="icon-btn mobile-menu" type="button" aria-expanded="false" aria-controls="northstar-primary-navigation">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'northstar' ); ?></span>
			☰
		</button>
		<?php northstar_site_branding(); ?>
		<nav id="northstar-primary-navigation" class="nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'northstar' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'northstar-menu',
					'fallback_cb'    => 'northstar_primary_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="header-actions">
			<a class="icon-btn hide-mobile" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) && wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Shop', 'northstar' ); ?></a>
			<a class="icon-btn" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Bag', 'northstar' ); ?>
				<b class="cart-count"><?php echo esc_html( function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></b>
			</a>
		</div>
	</div>
</header>
<main id="primary" class="container">
