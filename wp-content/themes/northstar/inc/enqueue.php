<?php
/**
 * Theme asset registration.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'northstar_enqueue_assets' );

/**
 * Enqueue front-end assets.
 *
 * @return void
 */
function northstar_enqueue_assets() {

	$theme_path = get_template_directory();
    $theme_uri  = get_template_directory_uri();

	wp_enqueue_style(
		'northstar-style',
		get_stylesheet_uri(),
		array(),
		false
	);

	wp_enqueue_style(
		'northstar-storefront',
		NORTHSTAR_URI . '/assets/css/styles.css',
		array( 'northstar-style' ),
		false
	);

	wp_enqueue_style(
		'northstar-theme-overrides',
		NORTHSTAR_URI . '/assets/css/theme-overrides.css',
		array( 'northstar-storefront' ),
		false
	);

	wp_enqueue_script(
		'northstar-navigation',
		NORTHSTAR_URI . '/assets/js/navigation.js',
		array(),
		false,
        true
	);

	wp_enqueue_script(
		'northstar-wishlist',
		$theme_uri . '/assets/js/wishlist.js',
		array(),
		false,
		true
	);

	wp_enqueue_script(
		'northstar-product-filters',
		$theme_uri . '/assets/js/product-filters.js',
		array(),
		false,
		true
	);

	wp_enqueue_script(
		'northstar-category-load-more',
		$theme_uri . '/assets/js/category-load-more.js',
		array(),
		false,
		true
	);
}

add_action( 'admin_enqueue_scripts', 'northstar_enqueue_homepage_media' );

/**
 * Enqueue homepage media uploader scripts.
 *
 * @param string $hook_suffix Current admin page.
 * @return void
 */	
function northstar_enqueue_homepage_media( $hook_suffix ) {

    // Only load on the page edit screen.
    if ( 'post.php' !== $hook_suffix ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || 'page' !== $screen->post_type ) {
        return;
    }

    // Get the page being edited.
    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

    // Only load on the static homepage.
    if ( ! $post_id || $post_id !== (int) get_option( 'page_on_front' ) ) {
        return;
    }

    // Load WordPress Media Library.
    wp_enqueue_media();

    // Load our custom media uploader JavaScript.
    wp_enqueue_script(
        'northstar-homepage-media',
        NORTHSTAR_URI . '/assets/js/homepage-media.js',
        array( 'jquery' ),
        filemtime( get_template_directory() . '/assets/js/homepage-media.js' ),
        true
    );
}
