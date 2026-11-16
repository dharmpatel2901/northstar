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

    $average_rating = (float) $product->get_average_rating();
    $review_count = (int) $product->get_review_count();
    $filled_stars = (int) round($average_rating);
    ?>

    <article class="product-card" data-product-card data-name="<?php echo esc_attr($product->get_name()); ?>"
        data-price="<?php echo esc_attr($product->get_price()); ?>">

        <div class="product-image">

            <a href="<?php echo esc_url($product->get_permalink()); ?>">

                <?php if ($product_badge): ?>
                    <span class="tag">
                        <?php echo esc_html($product_badge); ?>
                    </span>
                <?php elseif ($product->is_on_sale()): ?>
                    <span class="tag">
                        <?php esc_html_e('Sale', 'northstar'); ?>
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

                    <div class="rating">

                        <span class="rating-stars"
                            aria-label="<?php echo esc_attr(sprintf(__('Rated %1$s out of 5', 'northstar'), $average_rating)); ?>">

                            <?php for ($star = 1; $star <= 5; $star++): ?>

                                <span class="star <?php echo $star <= $filled_stars ? 'is-filled' : 'is-empty'; ?>"
                                    aria-hidden="true">

                                    <?php if ($star <= $filled_stars): ?>

                                        <!-- Filled star -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"
                                            fill="currentColor" aria-hidden="true">
                                            <path
                                                d="M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.47l-5.86 3.09 1.12-6.54-4.75-4.63 6.56-.95L12 2.5z">
                                            </path>
                                        </svg>

                                    <?php else: ?>

                                        <!-- Empty star -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none"
                                            stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path
                                                d="M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.47l-5.86 3.09 1.12-6.54-4.75-4.63 6.56-.95L12 2.5z">
                                            </path>
                                        </svg>

                                    <?php endif; ?>

                                </span>

                            <?php endfor; ?>

                        </span>

                        <span class="muted">
                            (<?php echo esc_html($review_count); ?>)
                        </span>

                    </div>

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
