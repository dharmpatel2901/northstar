<?php
/**
 * Theme setup.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'northstar_setup' );

/**
 * Register theme supports and navigation menus.
 *
 * @return void
 */
function northstar_setup() {
	load_theme_textdomain( 'northstar', NORTHSTAR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'northstar' ),
			'footer'  => __( 'Footer Menu', 'northstar' ),
		)
	);
}
