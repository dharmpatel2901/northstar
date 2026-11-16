<?php
/**
 * WooCommerce hook customizations.
 *
 * Keep changes in hooks where possible instead of copying WooCommerce templates.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp', 'northstar_customize_woocommerce_layout' );

/**
 * Apply small WooCommerce layout adjustments.
 *
 * @return void
 */
function northstar_customize_woocommerce_layout() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( is_shop() || is_product_taxonomy() ) {
		// Remove default wrappers only when a matching replacement is added.
		// Keep WooCommerce's standard loop and product-card output for compatibility.
	}
}
