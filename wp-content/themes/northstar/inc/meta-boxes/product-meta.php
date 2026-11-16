
<?php
/**
 * WooCommerce Product Meta Boxes.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the product badge meta box.
 *
 * @return void
 */
function northstar_add_product_badge_meta_box() {

    add_meta_box(
        'northstar_product_badge',
        __( 'Product Badge', 'northstar' ),
        'northstar_render_product_badge_meta_box',
        'product',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes_product', 'northstar_add_product_badge_meta_box' );

/**
 * Render the product badge meta box.
 *
 * @param WP_Post $post Current product post object.
 * @return void
 */
function northstar_render_product_badge_meta_box( $post ) {

    $product_badge = get_post_meta(
        $post->ID,
        '_northstar_product_badge',
        true
    );

    wp_nonce_field(
        'northstar_save_product_badge',
        'northstar_product_badge_nonce'
    );
    ?>

    <p>
        <label for="northstar_product_badge">
            <?php esc_html_e( 'Badge Text', 'northstar' ); ?>
        </label>
    </p>

    <input
        type="text"
        id="northstar_product_badge"
        name="northstar_product_badge"
        value="<?php echo esc_attr( $product_badge ); ?>"
        class="widefat"
        placeholder="<?php esc_attr_e( 'e.g. Bestseller', 'northstar' ); ?>"
    />

    <p class="description">
        <?php
        esc_html_e(
            'Enter a badge such as Bestseller, New Arrival, Trending or Limited Edition.',
            'northstar'
        );
        ?>
    </p>

    <?php
}

/**
 * Save the product badge meta value.
 *
 * @param int $post_id Product post ID.
 * @return void
 */
function northstar_save_product_badge_meta( $post_id ) {

    // Verify nonce.
    if (
        ! isset( $_POST['northstar_product_badge_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash( $_POST['northstar_product_badge_nonce'] )
            ),
            'northstar_save_product_badge'
        )
    ) {
        return;
    }

    // Prevent autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check user permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Check that the field was submitted.
    if ( ! isset( $_POST['northstar_product_badge'] ) ) {
        return;
    }

    // Sanitize the submitted value.
    $product_badge = sanitize_text_field(
        wp_unslash( $_POST['northstar_product_badge'] )
    );

    // Save or delete the product badge.
    if ( '' !== $product_badge ) {
        update_post_meta(
            $post_id,
            '_northstar_product_badge',
            $product_badge
        );
    } else {
        delete_post_meta(
            $post_id,
            '_northstar_product_badge'
        );
    }
}
add_action( 'save_post_product', 'northstar_save_product_badge_meta' );