<?php
/**
 * WooCommerce theme support.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'northstar_woocommerce_setup', 20 );

/**
 * Declare WooCommerce support.
 *
 * @return void
 */
function northstar_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support(
		'wc-product-gallery-zoom'
	);
	add_theme_support(
		'wc-product-gallery-lightbox'
	);
	add_theme_support(
		'wc-product-gallery-slider'
	);
}
