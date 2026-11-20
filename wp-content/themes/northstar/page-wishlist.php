<?php


defined('ABSPATH') || exit;

get_header();

$wishlist = northstar_get_wishlist();

// Get wishlist products.
$products = array_filter(
    array_map('wc_get_product', $wishlist)
);

?>

<main id="primary" class="site-main wishlist-page">

    <div class="container">

        <header class="wishlist-header">
            <h1 class="wishlist-title">
                <?php esc_html_e('My Wishlist', 'northstar'); ?>
            </h1>

            <p class="wishlist-description">
                <?php esc_html_e(
                    'Your favourite products, all in one place.',
                    'northstar'
                ); ?>
            </p>
        </header>

        <div class="wishlist-content">

            <?php if (!empty($products)) : ?>

                <div class="product-grid">

                    <?php foreach ($products as $product) : ?>

                        <?php
                        if (!$product->is_visible()) {
                            continue;
                        }

                        northstar_render_product_card($product);
                        ?>

                    <?php endforeach; ?>

                </div>

            <?php else : ?>

                <div class="wishlist-empty">

                    <h2>
                        <?php esc_html_e(
                            'Your wishlist is empty',
                            'northstar'
                        ); ?>
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

</main>

<?php get_footer(); ?>