<?php
/**
 * Render a reusable Northstar product card.
 *
 * @param WC_Product $product WooCommerce product object.
 * @return void
 */
function northstar_render_product_card($product)
{

    if (!$product instanceof WC_Product) {
        return;
    }

    $product_id = $product->get_id();
    $product_badge = get_post_meta(
        $product_id,
        '_northstar_product_badge',
        true
    );
    ?>

    <article class="product-card" data-product-card data-name="<?php echo esc_attr($product->get_name()); ?>"
        data-price="<?php echo esc_attr($product->get_price()); ?>">

        <div class="product-image">

            <a href="<?php echo esc_url($product->get_permalink()); ?>">

                <?php if ($product_badge): ?>
                    <span class="tag">
                        <?php echo esc_html($product_badge); ?>
                    </span>
                <?php endif; ?>

                <?php
                echo wp_kses_post(
                    $product->get_image('thumbnail')
                );
                ?>

            </a>

            <button class="heart" type="button" data-wishlist-button
                data-product-id="<?php echo esc_attr($product_id); ?>"
                aria-label="<?php esc_attr_e('Add to wishlist', 'northstar'); ?>" aria-pressed="false">
                <svg class="heart-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path
                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                    </path>
                </svg>
            </button>

        </div>

        <div class="product-info">

            <a href="<?php echo esc_url($product->get_permalink()); ?>">

                <?php if (wc_review_ratings_enabled()): ?>

                    <?php northstar_single_product_rating($product); ?>

                <?php endif; ?>

                <h3><?php echo esc_html($product->get_name()); ?></h3>

                <span class="price">
                    <?php echo wp_kses_post($product->get_price_html()); ?>
                </span>

            </a>

        </div>

    </article>

    <?php
}
