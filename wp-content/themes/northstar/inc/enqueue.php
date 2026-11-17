
<?php
/**
 * Theme asset registration.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'northstar_enqueue_assets' );

/**
 * Get asset version based on file modification time.
 *
 * @param string $file_path Asset file path.
 * @return string
 */
function northstar_asset_version( $file_path ) {

    if ( file_exists( $file_path ) ) {
        return (string) filemtime( $file_path );
    }

    return wp_get_theme()->get( 'Version' );
}

/**
 * Enqueue front-end assets.
 *
 * @return void
 */
function northstar_enqueue_assets() {

    $theme_path = get_template_directory();
    $theme_uri  = get_template_directory_uri();

    // Main stylesheet.
    wp_enqueue_style(
        'northstar-style',
        get_stylesheet_uri(),
        array(),
        northstar_asset_version( get_stylesheet_directory() . '/style.css' )
    );

    // Storefront styles.
    wp_enqueue_style(
        'northstar-storefront',
        NORTHSTAR_URI . '/assets/css/styles.css',
        array( 'northstar-style' ),
        northstar_asset_version( $theme_path . '/assets/css/styles.css' )
    );

    // Theme overrides.
    wp_enqueue_style(
        'northstar-theme-overrides',
        NORTHSTAR_URI . '/assets/css/theme-overrides.css',
        array( 'northstar-storefront' ),
        northstar_asset_version( $theme_path . '/assets/css/theme-overrides.css' )
    );

    // Navigation.
    wp_enqueue_script(
        'northstar-navigation',
        $theme_uri . '/assets/js/navigation.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/navigation.js' ),
        true
    );

    // Wishlist.
    wp_enqueue_script(
        'northstar-wishlist',
        $theme_uri . '/assets/js/wishlist.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/wishlist.js' ),
        true
    );

    // Product filters.
    wp_enqueue_script(
        'northstar-product-filters',
        $theme_uri . '/assets/js/product-filters.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/product-filters.js' ),
        true
    );

    // Category load more.
    wp_enqueue_script(
        'northstar-category-load-more',
        $theme_uri . '/assets/js/category-load-more.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/category-load-more.js' ),
        true
    );

	// Product Quantity.
    wp_enqueue_script(
        'northstar-product-quantity',
        $theme_uri . '/assets/js/product-quantity.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/product-quantity.js' ),
        true
    );

	// Detail Tab
    wp_enqueue_script(
        'northstar-detail-tab',
        $theme_uri . '/assets/js/detail-tab.js',
        array(),
        northstar_asset_version( $theme_path . '/assets/js/detail-tab.js' ),
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

    if ( 'post.php' !== $hook_suffix ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || 'page' !== $screen->post_type ) {
        return;
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

    if ( ! $post_id || $post_id !== (int) get_option( 'page_on_front' ) ) {
        return;
    }

    wp_enqueue_media();

    $script_path = get_template_directory() . '/assets/js/homepage-media.js';

    wp_enqueue_script(
        'northstar-homepage-media',
        NORTHSTAR_URI . '/assets/js/homepage-media.js',
        array( 'jquery' ),
        northstar_asset_version( $script_path ),
        true
    );
}
