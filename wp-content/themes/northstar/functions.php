<?php
/**
 * Theme bootstrap.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

define( 'NORTHSTAR_VERSION', '1.0.0' );
define( 'NORTHSTAR_DIR', get_template_directory() );
define( 'NORTHSTAR_URI', get_template_directory_uri() );

require_once NORTHSTAR_DIR . '/inc/setup.php';
require_once NORTHSTAR_DIR . '/inc/enqueue.php';
require_once NORTHSTAR_DIR . '/inc/template-functions.php';
require_once NORTHSTAR_DIR . '/inc/theme-options.php';
require_once NORTHSTAR_DIR . '/inc/meta-boxes/shop-page.php';
require_once NORTHSTAR_DIR . '/inc/meta-boxes/product-meta.php';
require_once NORTHSTAR_DIR . '/inc/meta-boxes/homepage-hero.php';
require_once NORTHSTAR_DIR . '/inc/woocommerce/product-filters.php';
require_once NORTHSTAR_DIR . '/inc/woocommerce/product-card.php';
require_once NORTHSTAR_DIR . '/inc/woocommerce/setup.php';
require_once NORTHSTAR_DIR . '/inc/woocommerce/shop.php';
require_once NORTHSTAR_DIR . '/inc/woocommerce/hooks.php';