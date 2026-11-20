
<?php
/**
 * WooCommerce Shop and Cart Page Meta Boxes.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;


/**
 * Register meta box only for WooCommerce Shop and Cart pages.
 */
function northstar_add_woocommerce_page_meta_boxes( $post ) {
    $shop_page_id = (int) get_option( 'woocommerce_shop_page_id' );
    $cart_page_id = (int) get_option( 'woocommerce_cart_page_id' );

    if (
        (int) $post->ID !== $shop_page_id &&
        (int) $post->ID !== $cart_page_id
    ) {
        return;
    }

    add_meta_box(
        'northstar-woocommerce-page-content',
        __( 'Page Content', 'northstar' ),
        'northstar_render_woocommerce_page_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_page', 'northstar_add_woocommerce_page_meta_boxes' );

/**
 * Render WooCommerce page meta box.
 */
function northstar_render_woocommerce_page_meta_box( $post ) {
    $shop_page_id = (int) get_option( 'woocommerce_shop_page_id' );
    $cart_page_id = (int) get_option( 'woocommerce_cart_page_id' );

    if ( (int) $post->ID === $shop_page_id ) {
        $page_type = 'shop';
    } elseif ( (int) $post->ID === $cart_page_id ) {
        $page_type = 'cart';
    } else {
        return;
    }

    wp_nonce_field(
        'northstar_save_woocommerce_page_meta',
        'northstar_woocommerce_page_meta_nonce'
    );

    $eyebrow = get_post_meta(
        $post->ID,
        '_northstar_' . $page_type . '_eyebrow',
        true
    );

    $lead = get_post_meta(
        $post->ID,
        '_northstar_' . $page_type . '_lead',
        true
    );
    ?>

    <p>
        <label for="northstar_<?php echo esc_attr( $page_type ); ?>_eyebrow">
            <strong><?php esc_html_e( 'Eyebrow', 'northstar' ); ?></strong>
        </label>
    </p>

    <input
        type="text"
        id="northstar_<?php echo esc_attr( $page_type ); ?>_eyebrow"
        name="northstar_<?php echo esc_attr( $page_type ); ?>_eyebrow"
        value="<?php echo esc_attr( $eyebrow ); ?>"
        class="widefat"
    >

    <p>
        <label for="northstar_<?php echo esc_attr( $page_type ); ?>_lead">
            <strong><?php esc_html_e( 'Lead', 'northstar' ); ?></strong>
        </label>
    </p>

    <textarea
        id="northstar_<?php echo esc_attr( $page_type ); ?>_lead"
        name="northstar_<?php echo esc_attr( $page_type ); ?>_lead"
        rows="4"
        class="widefat"
    ><?php echo esc_textarea( $lead ); ?></textarea>

    <?php
}

/**
 * Save WooCommerce page meta box data.
 */
function northstar_save_woocommerce_page_meta( $post_id ) {
    if (
        ! isset( $_POST['northstar_woocommerce_page_meta_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['northstar_woocommerce_page_meta_nonce'] ) ),
            'northstar_save_woocommerce_page_meta'
        )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    if ( 'page' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    $shop_page_id = (int) get_option( 'woocommerce_shop_page_id' );
    $cart_page_id = (int) get_option( 'woocommerce_cart_page_id' );

    if ( (int) $post_id === $shop_page_id ) {
        $page_type = 'shop';
    } elseif ( (int) $post_id === $cart_page_id ) {
        $page_type = 'cart';
    } else {
        return;
    }

    $eyebrow_key = 'northstar_' . $page_type . '_eyebrow';
    $lead_key    = 'northstar_' . $page_type . '_lead';

    if ( isset( $_POST[ $eyebrow_key ] ) ) {
        update_post_meta(
            $post_id,
            '_northstar_' . $page_type . '_eyebrow',
            sanitize_text_field( wp_unslash( $_POST[ $eyebrow_key ] ) )
        );
    }

    if ( isset( $_POST[ $lead_key ] ) ) {
        update_post_meta(
            $post_id,
            '_northstar_' . $page_type . '_lead',
            sanitize_textarea_field( wp_unslash( $_POST[ $lead_key ] ) )
        );
    }
}
add_action( 'save_post_page', 'northstar_save_woocommerce_page_meta' );