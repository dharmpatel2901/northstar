<?php
/**
 * Remove default single product hooks.
 */
function northstar_remove_single_product_hooks() {

    if ( ! is_product() ) {
        return;
    }

    // Remove default product gallery.
    remove_action(
        'woocommerce_before_single_product_summary',
        'woocommerce_show_product_images',
        20
    );

    // Remove default product summary elements.
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

    // Remove default tabs.
    remove_action(
        'woocommerce_after_single_product_summary',
        'woocommerce_output_product_data_tabs',
        10
    );

    // Remove default upsells.
    remove_action(
        'woocommerce_after_single_product_summary',
        'woocommerce_upsell_display',
        15
    );

    // Remove default related products.
    remove_action(
        'woocommerce_after_single_product_summary',
        'woocommerce_output_related_products',
        20
    );

    remove_action(
        'woocommerce_before_single_product_summary',
        'woocommerce_show_product_sale_flash',
        10
    );
}
add_action( 'wp', 'northstar_remove_single_product_hooks' );


/**
 * Register custom single product hooks.
 *
 * @return void
 */
function northstar_single_product_hooks() {

    // Open main product layout.
    add_action(
        'woocommerce_before_single_product_summary',
        'northstar_open_product_layout',
        1
    );

    // Product gallery.
    add_action(
        'woocommerce_before_single_product_summary',
        'northstar_product_gallery',
        20
    );

    // Custom product information.
    add_action(
        'woocommerce_single_product_summary',
        'northstar_product_summary',
        5
    );

    // Open main product layout.
    add_action(
        'woocommerce_after_single_product_summary',
        'northstar_close_product_layout',
        1
    );

    // Related products.
    add_action(
        'woocommerce_after_single_product',
        'northstar_output_related_products',
        20
    );
}
add_action( 'wp', 'northstar_single_product_hooks' );

/**
 * Open the product details wrapper.
 *
 * @return void
 */
function northstar_open_product_layout() {
    echo '<div class="pdp">';
}

/**
 * Close the product details wrapper.
 *
 * @return void
 */
function northstar_close_product_layout() {
    echo '</div>';
}


/**
 * Display custom single product gallery.
 *
 * @return void
 */
function northstar_product_gallery() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $attachment_ids = array();

    $featured_image_id = $product->get_image_id();

    if ( $featured_image_id ) {
        $attachment_ids[] = $featured_image_id;
    }

    $gallery_image_ids = $product->get_gallery_image_ids();

    $attachment_ids = array_merge( $attachment_ids, $gallery_image_ids );

    if ( empty( $attachment_ids ) ) {
        return;
    }

    $product_id = $product->get_id();
    $product_badge = get_post_meta(
        $product_id,
        '_northstar_product_badge',
        true
    );

    ?>

    <section class="product-gallery">

        <div class="gallery-main">

            <?php if ( $product->is_on_sale() ) : ?>
                <span class="onsale">
                    <?php esc_html_e( 'Sale!', 'northstar' ); ?>
                </span>
            <?php endif; ?>

            <?php if ($product_badge): ?>
                    <span class="tag">
                        <?php echo esc_html($product_badge); ?>
                    </span>
            <?php endif; ?>

            <?php
            echo wp_get_attachment_image(
                $attachment_ids[0],
                'medium',
                false,
                array(
                    'class' => 'main-product-image',
                )
            );
            ?>

        </div>

        <?php if ( count( $attachment_ids ) > 1 ) : ?>

            <div class="gallery-thumbs">

                <?php foreach ( $attachment_ids as $index => $attachment_id ) : ?>

                    <button
                        type="button"
                        class="thumb <?php echo 0 === $index ? 'active' : ''; ?>"
                        data-gallery-image="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' ) ); ?>"
                        aria-label="<?php echo esc_attr( sprintf( __( 'View product image %d', 'northstar' ), $index + 1 ) ); ?>"
                    >
                        <?php
                        echo wp_get_attachment_image(
                            $attachment_id,
                            'woocommerce_gallery_thumbnail'
                        );
                        ?>
                    </button>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

    <?php
}

/**
 * Add custom classes to the product gallery container.
 *
 * @param array $classes Gallery CSS classes.
 * @return array
 */
function northstar_product_gallery_classes( $classes ) {

    $classes[] = 'gallery-main';

    return $classes;
}
add_filter( 'woocommerce_single_product_image_gallery_classes', 'northstar_product_gallery_classes');

/**
 * Remove the WooCommerce sidebar on single product pages.
 *
 * @return void
 */
function northstar_remove_product_sidebar() {

    if ( is_product() ) {
        remove_action(
            'woocommerce_sidebar',
            'woocommerce_get_sidebar',
            10
        );
    }
}
add_action( 'wp', 'northstar_remove_product_sidebar' );




/**
 * Display custom single product information.
 *
 * @return void
 */
function northstar_product_summary() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }
    ?>

    <section class="pdp-info">

        <div class="eyebrow">
            <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
        </div>

        <?php woocommerce_template_single_title(); ?>

        <?php northstar_single_product_rating(); ?>

        <?php northstar_single_product_price(); ?>

        <?php northstar_single_product_excerpt(); ?>

        <div class="stock"> ●
            <?php
            if ( 'instock' === $product->get_stock_status() ) {
                echo esc_html__( 'In stock', 'northstar' );
            } elseif ( 'outofstock' === $product->get_stock_status() ) {
                echo esc_html__( 'Out of stock', 'northstar' );
            } elseif ( 'onbackorder' === $product->get_stock_status() ) {
                echo esc_html__( 'Available on backorder', 'northstar' );
            }
            ?>
        </div>

        <label class="option-label" for="quantity">
            <?php esc_html_e( 'Quantity', 'northstar' ); ?>
        </label>

        <?php northstar_single_product_add_to_cart(); ?>

        <?php
        northstar_product_trust_messages();
        northstar_product_notice();
        ?>

        <?php northstar_product_details_tabs(); ?>

    </section>

    <?php
}

/**
 * Output related products using Northstar product cards.
 *
 * @return void
 */
function northstar_output_related_products() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $related_products = wc_get_related_products(
        $product->get_id(),
        4,
        array( $product->get_id() )
    );

    if ( empty( $related_products ) ) {
        return;
    }
    ?>

    <section class="related-products">

        <div class="section-head">
            <div>
                <div class="eyebrow">
                    <?php esc_html_e( 'You may also like', 'northstar' ); ?>
                </div>

                <h2>
                    <?php esc_html_e( 'Complete the everyday', 'northstar' ); ?>
                </h2>
            </div>
        </div>

        <div class="product-grid">

            <?php foreach ( $related_products as $related_product_id ) : ?>

                <?php
                $related_product = wc_get_product( $related_product_id );

                if ( $related_product instanceof WC_Product ) {
                    northstar_render_product_card( $related_product );
                }
                ?>

            <?php endforeach; ?>

        </div>

    </section>

    <?php
}

/**
 * Display single product price.
 *
 * @return void
 */
function northstar_single_product_price() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }
    ?>

    <div class="price">
        <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>

    <?php
}

/**
 * Display single product excerpt.
 *
 * @return void
 */
function northstar_single_product_excerpt() {

    global $post;

    if ( ! $post instanceof WP_Post ) {
        return;
    }

    $excerpt = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

    if ( empty( $excerpt ) ) {
        return;
    }
    ?>

    <p class="lead">
        <?php echo esc_html( wp_strip_all_tags($excerpt) ); ?>
    </p>

    <?php
}

/**
 * Display custom single product add-to-cart form.
 *
 * @return void
 */
function northstar_single_product_add_to_cart() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $is_in_stock = $product->is_in_stock();
    ?>

    <form class="cart" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype="multipart/form-data">

        <div class="qty-row">

            <div class="qty">

                <button
                    type="button"
                    class="qty-minus"
                    aria-label="<?php esc_attr_e( 'Decrease quantity', 'northstar' ); ?>"
                    <?php disabled( ! $is_in_stock ); ?>
                >
                    −
                </button>

                <?php
                woocommerce_quantity_input(
                    array(
                        'min_value'   => $product->get_min_purchase_quantity(),
                        'max_value'   => $product->get_max_purchase_quantity(),
                        'input_value' => 1,
                        'readonly'    => true,
                    ),
                    $product
                );
                ?>

                <button
                    type="button"
                    class="qty-plus"
                    aria-label="<?php esc_attr_e( 'Increase quantity', 'northstar' ); ?>"
                    <?php disabled( ! $is_in_stock ); ?>
                >
                    +
                </button>

            </div>

            <button
                type="submit"
                name="add-to-cart"
                value="<?php echo esc_attr( $product->get_id() ); ?>"
                class="btn full single_add_to_cart_button button alt"
                <?php disabled( ! $is_in_stock ); ?>
            >
                <?php esc_html_e( 'Add to bag', 'northstar' ); ?>
            </button>

        </div>

    </form>

    <?php
}

/**
 * Display custom product details tabs.
 */
function northstar_product_details_tabs() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $product_id = $product->get_id();

    // Product description.
    $details_content = $product->get_description();

    // Product shipping information.
    $shipping_content = get_post_meta(
        $product_id,
        '_northstar_shipping_content',
        true
    );

    ?>

    <section class="details">

        <div class="tabs">

            <?php if ( ! empty( $details_content ) ) : ?>
                <button
                    type="button"
                    class="tab active"
                    data-tab="details"
                >
                    <?php esc_html_e( 'Details', 'northstar' ); ?>
                </button>
            <?php endif; ?>

            <?php if ( ! empty( $shipping_content ) ) : ?>
                <button
                    type="button"
                    class="tab <?php echo empty( $details_content ) ? 'active' : ''; ?>"
                    data-tab="shipping"
                >
                    <?php esc_html_e( 'Shipping', 'northstar' ); ?>
                </button>
            <?php endif; ?>

            <button
                type="button"
                class="tab <?php echo empty( $details_content ) && empty( $shipping_content ) ? 'active' : ''; ?>"
                data-tab="reviews"
            >
                <?php esc_html_e( 'Reviews', 'northstar' ); ?>
                (<?php echo esc_html( $product->get_review_count() ); ?>)
            </button>

        </div>

        <?php if ( ! empty( $details_content ) ) : ?>
            <div class="tab-panel" data-tab-panel="details">
                <?php echo apply_filters( 'the_content', $details_content ); ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $shipping_content ) ) : ?>
            <div
                class="tab-panel"
                data-tab-panel="shipping"
                hidden
            >
                <?php echo esc_html( $shipping_content ); ?>
            </div>
        <?php endif; ?>

        <div
            class="tab-panel"
            data-tab-panel="reviews"
            <?php echo ( ! empty( $details_content ) || ! empty( $shipping_content ) ) ? 'hidden' : ''; ?>
        >
            <?php if ( $product->get_review_count() > 0 ) : ?>

                <p>
                    <?php
                    printf(
                        /* translators: 1: average rating, 2: maximum rating. */
                        esc_html__(
                            'Customers love the thoughtful design, quality finish and everyday usefulness. Average rating: %1$s out of %2$s.',
                            'northstar'
                        ),
                        esc_html( $product->get_average_rating() ),
                        esc_html( '5' )
                    );
                    ?>
                </p>

            <?php else : ?>

                <p>
                    <?php esc_html_e( 'No reviews yet.', 'northstar' ); ?>
                </p>

            <?php endif; ?>

            <button type="button" class="btn give-review" data-review-modal-open>
                <?php esc_html_e( 'Give a Review', 'northstar' ); ?>
            </button>
        </div>

    </section>
    <div class="review-modal" data-review-modal hidden>

        <div class="review-modal-content">

            <button
                type="button"
                class="review-modal-close"
                data-review-modal-close
                aria-label="<?php esc_attr_e( 'Close review form', 'northstar' ); ?>"
            >
                &times;
            </button>

            <h2><?php esc_html_e( 'Write a Review', 'northstar' ); ?></h2>

            <?php wc_get_template( 'single-product-reviews.php' ); ?>

        </div>

    </div>

    <?php
}

/**
 * Display product trust messages.
 *
 * @return void
 */
function northstar_product_trust_messages() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $trust_messages = get_post_meta(
        $product->get_id(),
        '_northstar_trust_messages',
        true
    );

    $icons = array(
        'secure-checkout.svg',
        'returns.svg',
        'thoughtful-materials.svg',
    );

    if ( ! is_array( $trust_messages ) || empty( $trust_messages ) ) {
        return;
    }
    ?>

    <div class="trust-row">

        <?php foreach ( array_slice( $trust_messages, 0, 3 ) as $index => $message ) : ?>

            <?php if ( '' !== trim( $message ) ) : ?>

                <span class="trust-item">

                    <img
                        src="<?php echo esc_url( NORTHSTAR_URI . '/assets/images/' . $icons[ $index ] ); ?>"
                        alt=""
                        aria-hidden="true"
                    />

                    <?php echo esc_html( $message ); ?>

                </span>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>

    <?php
}

/**
 * Display product notice.
 *
 * @return void
 */
function northstar_product_notice() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $product_notice = get_post_meta(
        $product->get_id(),
        '_northstar_product_notice',
        true
    );

    if ( empty( $product_notice ) ) {
        return;
    }
    ?>

    <div class="notice">
        <?php echo esc_html( $product_notice ); ?>
    </div>

    <?php
}