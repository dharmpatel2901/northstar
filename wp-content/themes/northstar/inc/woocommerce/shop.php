<?php
/**
 * Register shop toolbar.
 *
 * @return void
 */
function northstar_setup_shop_toolbar() {

    // Remove default WooCommerce sorting.
    remove_action(
        'woocommerce_before_shop_loop',
        'woocommerce_catalog_ordering',
        30
    );

    remove_action(
        'woocommerce_before_shop_loop',
        'woocommerce_result_count',
        20
    );

    // Add custom toolbar.
    add_action(
        'woocommerce_before_shop_loop',
        'northstar_shop_toolbar',
        30
    );
}
add_action( 'wp', 'northstar_setup_shop_toolbar' );


/**
 * Display search bar, sorting dropdown, and result count.
 *
 * @return void
 */
function northstar_shop_toolbar() {
    ?>

    <div class="shop-toolbar">

        <?php
        woocommerce_result_count();
        ?>

        <div class="shop-toolbar-controls">

            <form role="search" method="get" class="shop-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">

                <input
                    class="searchbox"
                    type="search"
                    name="s"
                    placeholder="<?php esc_attr_e( 'Search products...', 'northstar' ); ?>"
                    value="<?php echo esc_attr( get_search_query() ); ?>"
                >

                <input type="hidden" name="post_type" value="product">

            </form>

            <div class="shop-sorting">
                <?php woocommerce_catalog_ordering(); ?>
            </div>

        </div>

    </div>

    <?php
}