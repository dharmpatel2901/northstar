
<?php
/**
 * WooCommerce Product Meta Boxes.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Northstar product information meta box.
 *
 * @return void
 */
function northstar_add_product_information_meta_box() {

    add_meta_box(
        'northstar_product_information',
        __( 'Northstar Product Information', 'northstar' ),
        'northstar_render_product_information_meta_box',
        'product',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes_product', 'northstar_add_product_information_meta_box' );

/**
 * Render the Northstar product information meta box.
 *
 * @param WP_Post $post Current product post object.
 * @return void
 */
function northstar_render_product_information_meta_box( $post ) {

    // Get existing product badge.
    $product_badge = get_post_meta(
        $post->ID,
        '_northstar_product_badge',
        true
    );

    // Get existing shipping information.
    $shipping_content = get_post_meta(
        $post->ID,
        '_northstar_shipping_content',
        true
    );

    // Get trust messages.
    $trust_messages = get_post_meta(
        $post->ID,
        '_northstar_trust_messages',
        true
    );

    if ( ! is_array( $trust_messages ) ) {
        $trust_messages = array();
    }

    // Get product notice.
    $product_notice = get_post_meta(
        $post->ID,
        '_northstar_product_notice',
        true
    );

    // Add nonce.
    wp_nonce_field(
        'northstar_save_product_information',
        'northstar_product_information_nonce'
    );
    ?>

    <!-- Product Badge -->
    <h4><?php esc_html_e( 'Product Badge', 'northstar' ); ?></h4>

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

    <hr />

    <!-- Product Shipping Information -->
    <h4><?php esc_html_e( 'Product Shipping Information', 'northstar' ); ?></h4>

    <p>
        <label for="northstar_shipping_content">
            <?php esc_html_e( 'Shipping Information', 'northstar' ); ?>
        </label>
    </p>

    <textarea
        id="northstar_shipping_content"
        name="northstar_shipping_content"
        rows="5"
        class="widefat"
    ><?php echo esc_textarea( $shipping_content ); ?></textarea>

    <hr />

    <!-- Trust Messages -->
    <h4><?php esc_html_e( 'Product Trust Messages', 'northstar' ); ?></h4>

    <?php
    $default_trust_messages = array(
        'Secure checkout',
        '30-day returns',
        'Thoughtful materials',
    );

    for ( $index = 0; $index < 3; $index++ ) :

        $message = isset( $trust_messages[ $index ] )
            ? $trust_messages[ $index ]
            : $default_trust_messages[ $index ];
        ?>

        <p>
            <label for="northstar_trust_message_<?php echo esc_attr( $index ); ?>">
                <?php
                echo esc_html(
                    sprintf( __( 'Trust Message %d', 'northstar' ), $index + 1 )
                );
                ?>
            </label>
        </p>

        <input
            type="text"
            id="northstar_trust_message_<?php echo esc_attr( $index ); ?>"
            name="northstar_trust_messages[]"
            value="<?php echo esc_attr( $message ); ?>"
            class="widefat"
        />

    <?php endfor; ?>

    <hr />

    <!-- Product Notice -->
    <h4><?php esc_html_e( 'Product Notice', 'northstar' ); ?></h4>

    <textarea
        name="northstar_product_notice"
        rows="3"
        class="widefat"
        placeholder="<?php esc_attr_e( 'e.g. Standard one-time purchase.', 'northstar' ); ?>"
    ><?php echo esc_textarea( $product_notice ); ?></textarea>

    <?php
}


/**
 * Save Northstar product information.
 *
 * @param int $post_id Product ID.
 * @return void
 */
function northstar_save_product_information_meta( $post_id ) {

    // Verify nonce.
    if (
        ! isset( $_POST['northstar_product_information_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash( $_POST['northstar_product_information_nonce'] )
            ),
            'northstar_save_product_information'
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

    // Save product badge.
    $product_badge = isset( $_POST['northstar_product_badge'] )
        ? sanitize_text_field( wp_unslash( $_POST['northstar_product_badge'] ) )
        : '';

    if ( '' !== $product_badge ) {
        update_post_meta( $post_id, '_northstar_product_badge', $product_badge );
    } else {
        delete_post_meta( $post_id, '_northstar_product_badge' );
    }

    // Save shipping information.
    $shipping_content = isset( $_POST['northstar_shipping_content'] )
        ? sanitize_textarea_field( wp_unslash( $_POST['northstar_shipping_content'] ) )
        : '';

    if ( '' !== $shipping_content ) {
        update_post_meta( $post_id, '_northstar_shipping_content', $shipping_content );
    } else {
        delete_post_meta( $post_id, '_northstar_shipping_content' );
    }

    // Save trust messages.
    $trust_messages = array();

    if (
        isset( $_POST['northstar_trust_messages'] ) &&
        is_array( $_POST['northstar_trust_messages'] )
    ) {
        $submitted_messages = array_slice(
            wp_unslash( $_POST['northstar_trust_messages'] ),
            0,
            3
        );

        foreach ( $submitted_messages as $message ) {
            $trust_messages[] = sanitize_text_field( $message );
        }
    }

    if ( ! empty( $trust_messages ) ) {
        update_post_meta(
            $post_id,
            '_northstar_trust_messages',
            $trust_messages
        );
    } else {
        delete_post_meta( $post_id, '_northstar_trust_messages' );
    }

    // Save product notice.
    $product_notice = isset( $_POST['northstar_product_notice'] )
        ? sanitize_textarea_field( wp_unslash( $_POST['northstar_product_notice'] ) )
        : '';

    if ( '' !== $product_notice ) {
        update_post_meta(
            $post_id,
            '_northstar_product_notice',
            $product_notice
        );
    } else {
        delete_post_meta( $post_id, '_northstar_product_notice' );
    }
}
add_action( 'save_post_product', 'northstar_save_product_information_meta' );
