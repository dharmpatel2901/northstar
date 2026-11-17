<?php
/**
 * WooCommerce hook customizations.
 *
 * Keep changes in hooks where possible instead of copying WooCommerce templates.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the default WooCommerce content wrappers.
 *
 * This template provides its own main container.
 */
remove_action(
	'woocommerce_before_main_content',
	'woocommerce_output_content_wrapper',
	10
);

remove_action(
	'woocommerce_after_main_content',
	'woocommerce_output_content_wrapper_end',
	10
);

/**
 * Remove the default WooCommerce breadcrumb.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Display custom WooCommerce breadcrumb.
 */
function northstar_woocommerce_breadcrumb() {
    if ( function_exists( 'woocommerce_breadcrumb' ) ) {
        woocommerce_breadcrumb(
            array(
                'wrap_before' => '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'northstar' ) . '">',
                'wrap_after'  => '</nav>',
            )
        );
    }
}
add_action( 'woocommerce_before_main_content', 'northstar_woocommerce_breadcrumb', 20 );
