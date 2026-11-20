
<?php
/**
 * Northstar Wishlist functionality.
 *
 * Guest users: localStorage.
 * Logged-in users: WordPress user meta.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the current user's wishlist.
 *
 * @return array
 */
function northstar_get_wishlist() {

    if ( ! is_user_logged_in() ) {
        return array();
    }

    $wishlist = get_user_meta(
        get_current_user_id(),
        '_northstar_wishlist',
        true
    );

    return is_array( $wishlist ) ? $wishlist : array();
}

/**
 * Validate wishlist product IDs.
 *
 * @param array $product_ids Product IDs.
 * @return array
 */
function northstar_validate_wishlist( $product_ids ) {

    $product_ids = array_unique(
        array_filter(
            array_map( 'absint', (array) $product_ids )
        )
    );

    $valid_products = array();

    foreach ( $product_ids as $product_id ) {

        if ( 'product' !== get_post_type( $product_id ) ) {
            continue;
        }

        $valid_products[] = $product_id;
    }

    return array_values( $valid_products );
}

/**
 * Save wishlist to the current user's account.
 *
 * @param array $wishlist Product IDs.
 * @return array
 */
function northstar_save_wishlist( $wishlist ) {

    $wishlist = northstar_validate_wishlist( $wishlist );

    if ( is_user_logged_in() ) {

        update_user_meta(
            get_current_user_id(),
            '_northstar_wishlist',
            $wishlist
        );
    }

    return $wishlist;
}

/**
 * Verify wishlist AJAX requests.
 *
 * @return void
 */
function northstar_verify_wishlist_request() {

    check_ajax_referer(
        'northstar_wishlist_nonce',
        'nonce'
    );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(
            array( 'message' => 'Please log in.' ),
            401
        );
    }
}

/**
 * Synchronise guest wishlist with account wishlist.
 *
 * @return void
 */
function northstar_sync_wishlist() {

    northstar_verify_wishlist_request();

    $guest_wishlist = isset( $_POST['wishlist'] )
        ? northstar_validate_wishlist(
            wp_unslash( $_POST['wishlist'] )
        )
        : array();

    $user_wishlist = northstar_get_wishlist();

    // Merge both wishlists without duplicate products.
    $wishlist = array_unique(
        array_merge( $user_wishlist, $guest_wishlist )
    );

    $wishlist = northstar_save_wishlist( $wishlist );

    wp_send_json_success(
        array(
            'wishlist' => $wishlist,
        )
    );
}
add_action(
    'wp_ajax_northstar_sync_wishlist',
    'northstar_sync_wishlist'
);

/**
 * Update the logged-in user's wishlist.
 *
 * @return void
 */
function northstar_update_wishlist() {

    northstar_verify_wishlist_request();

    $wishlist = isset( $_POST['wishlist'] )
        ? wp_unslash( $_POST['wishlist'] )
        : array();

    $wishlist = northstar_save_wishlist( $wishlist );

    wp_send_json_success(
        array(
            'wishlist' => $wishlist,
        )
    );
}
add_action(
    'wp_ajax_northstar_update_wishlist',
    'northstar_update_wishlist'
);

/**
 * Get logged-in user's wishlist.
 */
function northstar_ajax_get_wishlist() {

    check_ajax_referer('northstar_wishlist_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(
            array('message' => 'User is not logged in.')
        );
    }

    $wishlist = northstar_get_wishlist();

    wp_send_json_success(
        array(
            'wishlist' => $wishlist,
        )
    );
}

add_action(
    'wp_ajax_northstar_get_wishlist',
    'northstar_ajax_get_wishlist'
);


/**
 * Display the wishlist products.
 *
 * Shortcode: [northstar_wishlist]
 *
 * @return string
 */
/**
 * Display the wishlist products.
 *
 * Shortcode: [northstar_wishlist]
 *
 * @return string
 */
function northstar_wishlist_shortcode() {

    if (!function_exists('WC')) {
        return '';
    }

    $wishlist = northstar_get_wishlist();

    ob_start();

    // Wishlist breadcrumb.
    northstar_wishlist_breadcrumb();

    // Wishlist heading.
    northstar_wishlist_page_heading();
    ?>

    <div class="wishlist-page">

        <div class="wishlist-content">

            <?php if (!empty($wishlist)) : ?>

                <div class="product-grid">

                    <?php foreach ($wishlist as $product_id) : ?>

                        <?php
                        $product = wc_get_product($product_id);

                        if (!$product || !$product->is_visible()) {
                            continue;
                        }

                        northstar_render_product_card($product);
                        ?>

                    <?php endforeach; ?>

                </div>

            <?php else : ?>

                <div class="wishlist-empty">
                    <h2>
                        <?php esc_html_e('Your wishlist is empty', 'northstar'); ?>
                    </h2>

                    <p>
                        <?php esc_html_e(
                            'You haven\'t added any products to your wishlist yet.',
                            'northstar'
                        ); ?>
                    </p>

                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                        <?php esc_html_e('Continue Shopping', 'northstar'); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <?php

    return ob_get_clean();
}
add_shortcode('northstar_wishlist', 'northstar_wishlist_shortcode');

/**
 * Hide the default Wishlist page title.
 */
function northstar_hide_wishlist_page_title($title, $post_id) {

     if (
        !is_admin() &&
        is_main_query() &&
        in_the_loop() &&
        is_page('wishlist') &&
        (int) $post_id === (int) get_queried_object_id()
    ) {
        return '';
    }

    return $title;
}
add_filter('the_title', 'northstar_hide_wishlist_page_title', 10, 2);

/**
 * Display breadcrumb on the Wishlist page.
 */
function northstar_wishlist_breadcrumb() {

    $wishlist_title = get_post_field(
        'post_title',
        get_queried_object_id()
    );

    if (empty($wishlist_title)) {
        return;
    }

    echo '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'northstar') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'northstar') . '</a>';
    echo ' / ';
    echo esc_html($wishlist_title);
    echo '</nav>';
}

/**
 * Display the Wishlist page heading.
 */
function northstar_wishlist_page_heading() {

    $wishlist_title = get_post_field(
        'post_title',
        get_queried_object_id()
    );

    if (empty($wishlist_title)) {
        return;
    }

    echo '<h1 class="woocommerce-wishlist-title">';
    echo esc_html($wishlist_title);
    echo '</h1>';
}

