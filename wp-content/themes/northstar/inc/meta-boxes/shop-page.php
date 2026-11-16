
<?php
/**
 * WooCommerce Shop Page Meta Boxes.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register shop page meta boxes.
 *
 * @return void
 */
function northstar_add_shop_page_meta_boxes() {

    $shop_page_id = (int) get_option( 'woocommerce_shop_page_id' );

    if ( ! $shop_page_id ) {
        return;
    }

    add_meta_box(
        'northstar-shop-page-content',
        __( 'Shop Page Content', 'northstar' ),
        'northstar_render_shop_page_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_page', 'northstar_add_shop_page_meta_boxes' );

/**
 * Render shop page meta box fields.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function northstar_render_shop_page_meta_box( $post ) {

    wp_nonce_field(
        'northstar_save_shop_page_meta',
        'northstar_shop_page_meta_nonce'
    );

    $eyebrow = get_post_meta( $post->ID, '_northstar_shop_eyebrow', true );
    $lead    = get_post_meta( $post->ID, '_northstar_shop_lead', true );
    ?>

    <p>
        <label for="northstar-shop-eyebrow">
            <strong><?php esc_html_e( 'Eyebrow', 'northstar' ); ?></strong>
        </label>
    </p>

    <input
        type="text"
        id="northstar-shop-eyebrow"
        name="northstar_shop_eyebrow"
        value="<?php echo esc_attr( $eyebrow ); ?>"
        class="widefat"
    />

    <p>
        <label for="northstar-shop-lead">
            <strong><?php esc_html_e( 'Lead', 'northstar' ); ?></strong>
        </label>
    </p>

    <textarea
        id="northstar-shop-lead"
        name="northstar_shop_lead"
        rows="4"
        class="widefat"
    ><?php echo esc_textarea( $lead ); ?></textarea>

    <?php
}

/**
 * Save shop page meta box values.
 *
 * @param int $post_id Current post ID.
 * @return void
 */
function northstar_save_shop_page_meta( $post_id ) {

    // Verify nonce.
    if (
        ! isset( $_POST['northstar_shop_page_meta_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['northstar_shop_page_meta_nonce'] ) ),
            'northstar_save_shop_page_meta'
        )
    ) {
        return;
    }

    // Prevent autosave and revisions.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Verify post type.
    if ( 'page' !== get_post_type( $post_id ) ) {
        return;
    }

    // Verify permissions.
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    // Save eyebrow.
    if ( isset( $_POST['northstar_shop_eyebrow'] ) ) {

        $eyebrow = sanitize_text_field(
            wp_unslash( $_POST['northstar_shop_eyebrow'] )
        );

        update_post_meta(
            $post_id,
            '_northstar_shop_eyebrow',
            $eyebrow
        );
    }

    // Save lead.
    if ( isset( $_POST['northstar_shop_lead'] ) ) {

        $lead = sanitize_textarea_field(
            wp_unslash( $_POST['northstar_shop_lead'] )
        );

        update_post_meta(
            $post_id,
            '_northstar_shop_lead',
            $lead
        );
    }
}
add_action( 'save_post_page', 'northstar_save_shop_page_meta' );
