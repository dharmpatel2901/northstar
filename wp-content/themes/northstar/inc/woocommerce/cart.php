
<?php
/**
 * Northstar Custom WooCommerce Cart.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hide the default Cart page title.
 */
function northstar_hide_cart_page_title( $title, $post_id ) {

    if (
        ! is_admin() &&
        in_the_loop() &&
        function_exists( 'is_cart' ) &&
        is_cart() &&
        (int) $post_id === (int) wc_get_page_id( 'cart' )
    ) {
        return '';
    }

    return $title;
}
add_filter( 'the_title', 'northstar_hide_cart_page_title', 10, 2 );

/**
 * Display breadcrumb on the Cart page.
 */
function northstar_cart_breadcrumb() {

    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }

    $cart_page_id = (int) wc_get_page_id( 'cart' );

    if ( $cart_page_id <= 0 ) {
        return;
    }

    $cart_title = get_post_field( 'post_title', $cart_page_id );
    $cart_url   = get_permalink( $cart_page_id );

    echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'northstar' ) . '">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'northstar' ) . '</a>';
    echo ' / ';

    if ( $cart_url && $cart_title ) {
        echo '<a href="' . esc_url( $cart_url ) . '">' . esc_html( $cart_title ) . '</a>';
    }

    echo '</nav>';
}
add_action( 'woocommerce_before_cart', 'northstar_cart_breadcrumb', 5 );

/**
 * Display the Cart page eyebrow and heading.
 */
function northstar_cart_page_heading() {

    $cart_page_id = (int) wc_get_page_id( 'cart' );

    if ( $cart_page_id <= 0 ) {
        return;
    }

    $eyebrow    = get_post_meta( $cart_page_id, '_northstar_cart_eyebrow', true );
    $cart_title = get_post_field( 'post_title', $cart_page_id );

    if ( '' === $eyebrow && '' === $cart_title ) {
        return;
    }

    echo '<div class="woocommerce-cart-header">';

    if ( '' !== $eyebrow ) {
        echo '<div class="eyebrow">' . esc_html( $eyebrow ) . '</div>';
    }

    if ( '' !== $cart_title ) {
        echo '<h1 class="woocommerce-cart-title">' . esc_html( $cart_title ) . '</h1>';
    }

    echo '</div>';
}
add_action( 'woocommerce_before_cart', 'northstar_cart_page_heading', 6 );

/**
 * Render the custom WooCommerce cart.
 *
 * @return string
 */
function northstar_cart_shortcode() {

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return '';
    }

    $cart = WC()->cart;

    ob_start();

    wc_print_notices();

    // Display the custom breadcrumb and Cart heading.
    do_action( 'woocommerce_before_cart' );

    if ( $cart->is_empty() ) {
        ?>
        <div class="woocommerce-cart-empty">
            <p><?php esc_html_e( 'Your bag is currently empty.', 'northstar' ); ?></p>
            <a class="btn full" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                <?php esc_html_e( 'Continue shopping', 'northstar' ); ?>
            </a>
        </div>
        <?php
    } else {
        ?>

        <form
            class="woocommerce-cart-form"
            action="<?php echo esc_url( wc_get_cart_url() ); ?>"
            method="post"
        >
            <div class="cart-layout">

                <section data-cart-items="">

                    <?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) : ?>

                        <?php
                        $product = $cart_item['data'];
                        $quantity = $cart_item['quantity'];

                        if (
                            ! $product ||
                            ! $product->exists() ||
                            $quantity <= 0
                        ) {
                            continue;
                        }

                        $product_id = $cart_item['product_id'];
                        $min_quantity = $product->get_min_purchase_quantity();
                        $max_quantity = $product->get_max_purchase_quantity();
                        $description = $product->get_short_description();
                        ?>

                        <div class="cart-row">

                            <div class="cart-thumb">
                                <?php
                                echo wp_kses_post(
                                    $product->get_image( 'woocommerce_thumbnail' )
                                );
                                ?>
                            </div>

                            <div class="cart-product-details">

                                <strong>
                                    <?php echo esc_html( $product->get_name() ); ?>
                                </strong>

                                <?php if ( $description ) : ?>
                                    <div class="muted" style="font-size:11px;margin-top:6px">
                                        <?php echo esc_html( wp_strip_all_tags( $description ) ); ?>
                                    </div>
                                <?php endif; ?>

                                <button
                                    class="text-link"
                                    style="border:0;background:none;padding:8px 0;font-size:11px"
                                    href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
                                    aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'northstar' ), $product->get_name() ) ); ?>"
                                >
                                    <?php esc_html_e( 'Remove', 'northstar' ); ?>
                                </button>

                            </div>

                            <div class="qty northstar-qty">

                                <button
                                    type="button"
                                    class="minus"
                                    aria-label="<?php esc_attr_e( 'Decrease quantity', 'northstar' ); ?>"
                                >−</button>

                                <input
                                    type="number"
                                    class="input-text qty text"
                                    name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                                    value="<?php echo esc_attr( $quantity ); ?>"
                                    min="<?php echo esc_attr( $min_quantity ); ?>"
                                    <?php if ( $max_quantity > 0 ) : ?>
                                        max="<?php echo esc_attr( $max_quantity ); ?>"
                                    <?php endif; ?>
                                    step="1"
                                    readonly
                                >

                                <button
                                    type="button"
                                    class="plus"
                                    aria-label="<?php esc_attr_e( 'Increase quantity', 'northstar' ); ?>"
                                >+</button>

                            </div>

                            <strong class="row-total">
                                <?php
                                echo wp_kses_post(
                                    $cart->get_product_subtotal( $product, $quantity )
                                );
                                ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </section>

                <aside class="summary" data-cart-summary="">

                    <h3><?php esc_html_e( 'Order summary', 'northstar' ); ?></h3>

                    <div class="summary-line">
                        <span><?php esc_html_e( 'Subtotal', 'northstar' ); ?></span>
                        <strong><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></strong>
                    </div>

                    <?php if ( $cart->get_discount_total() > 0 ) : ?>
                        <div class="summary-line">
                            <span><?php esc_html_e( 'Discount', 'northstar' ); ?></span>
                            <strong>
                                -<?php echo wp_kses_post( wc_price( $cart->get_discount_total() ) ); ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <div class="summary-line">
                        <span><?php esc_html_e( 'Shipping', 'northstar' ); ?></span>
                        <strong><?php echo wp_kses_post( $cart->get_cart_shipping_total() ); ?></strong>
                    </div>

                    <div class="summary-line">
                        <span><?php esc_html_e( 'Estimated tax', 'northstar' ); ?></span>
                        <strong><?php echo wp_kses_post( $cart->get_taxes_total() ); ?></strong>
                    </div>

                    <div class="summary-line total">
                        <span><?php esc_html_e( 'Total', 'northstar' ); ?></span>
                        <strong><?php echo wp_kses_post( $cart->get_total() ); ?></strong>
                    </div>

                    <div style="display:flex;gap:7px;margin:18px 0">

                        <input
                            class="field"
                            type="text"
                            name="coupon_code"
                            placeholder="<?php esc_attr_e( 'Discount code', 'northstar' ); ?>"
                            style="min-width:0;width:100%"
                        >

                        <button
                            type="submit"
                            class="btn secondary small"
                            name="apply_coupon"
                            value="<?php esc_attr_e( 'Apply', 'northstar' ); ?>"
                        >
                            <?php esc_html_e( 'Apply', 'northstar' ); ?>
                        </button>

                    </div>

                    <a class="btn full" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
                        <?php esc_html_e( 'Continue to checkout', 'northstar' ); ?> →
                    </a>

                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

                    <button
                        type="submit"
                        name="update_cart"
                        value="<?php esc_attr_e( 'Update cart', 'northstar' ); ?>"
                        class="screen-reader-text"
                    >
                        <?php esc_html_e( 'Update cart', 'northstar' ); ?>
                    </button>

                </aside>

            </div>
        </form>

        <?php
    }

    do_action( 'woocommerce_after_cart' );

    return ob_get_clean();
}
add_shortcode( 'northstar_cart', 'northstar_cart_shortcode' );


/**
 * Enqueue quantity controls.
 */
function northstar_cart_quantity_script() {

    if ( ! is_cart() ) {
        return;
    }

    wp_enqueue_script( 'jquery' );

    $script = "
        document.addEventListener('click', function(event) {

            const button = event.target.closest('.northstar-qty button');

            if (!button) {
                return;
            }

            const quantityWrapper = button.closest('.northstar-qty');
            const input = quantityWrapper.querySelector('input.qty');
            const form = button.closest('form');

            if (!input || !form) {
                return;
            }

            let quantity = parseFloat(input.value) || 0;
            const min = parseFloat(input.min) || 1;
            const max = input.max ? parseFloat(input.max) : Infinity;

            if (button.classList.contains('plus')) {
                quantity = Math.min(quantity + 1, max);
            } else {
                quantity = Math.max(quantity - 1, min);
            }

            input.value = quantity;

            const updateButton = document.createElement('input');
            updateButton.type = 'hidden';
            updateButton.name = 'update_cart';
            updateButton.value = 'Update cart';

            form.appendChild(updateButton);
            form.submit();
        });
    ";

    wp_add_inline_script( 'jquery', $script );
}
add_action( 'wp_enqueue_scripts', 'northstar_cart_quantity_script' );
