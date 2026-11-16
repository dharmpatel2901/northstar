
<?php
/**
 * Product archive template.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the default WooCommerce content wrappers.
 *
 * This template provides its own main container.
 */
remove_action(
	'woocommerce_before_main_content',
	'woocommerce_output_content_wrapper',
	10
);

remove_action(
	'woocommerce_after_main_content',
	'woocommerce_output_content_wrapper_end',
	10
);

/**
 * Remove the default WooCommerce breadcrumb.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * Display custom WooCommerce breadcrumb.
 */
function northstar_woocommerce_breadcrumb() {
    if ( function_exists( 'woocommerce_breadcrumb' ) ) {
        woocommerce_breadcrumb(
            array(
                'wrap_before' => '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'northstar' ) . '">',
                'wrap_after'  => '</nav>',
            )
        );
    }
}
add_action( 'woocommerce_before_main_content', 'northstar_woocommerce_breadcrumb', 20 );

get_header( 'shop' );
?>

	<?php
	/**
	 * WooCommerce breadcrumb.
	 */
	do_action( 'woocommerce_before_main_content' );
	?>

    <?php
    $shop_page_id = (int) get_option( 'woocommerce_shop_page_id' );

    $eyebrow = get_post_meta(
        $shop_page_id,
        '_northstar_shop_eyebrow',
        true
    );

    $lead = get_post_meta(
        $shop_page_id,
        '_northstar_shop_lead',
        true
    );
    ?>

	<!-- Shop heading -->
	<div class="section-head">
		<div>
            <?php if ( $eyebrow ) : ?>
			<div class="eyebrow">
				<?php echo esc_html( $eyebrow ); ?>
			</div>
            <?php endif; ?>

			<h1>
				<?php woocommerce_page_title(); ?>
			</h1>

            <?php if ( $lead ) : ?>
			<p class="lead">
				<?php echo esc_html( $lead ); ?>
			</p>
            <?php endif; ?>
		</div>
    </div>

	<div class="shop-layout">

		<!-- Sidebar filters -->
		<?php
		require get_theme_file_path(
			'/woocommerce/product-filters.php'
		);
		?>

		<!-- Shop results -->
		<section
			class="shop-results"
			data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'northstar_product_filter' ) ); ?>"
			aria-live="polite"
		>

			<?php
			/**
			 * Result count and sorting.
			 */
			do_action( 'woocommerce_before_shop_loop' );
			?>

			<!-- AJAX product results -->
			<div id="northstar-shop-results">

				<?php if ( woocommerce_product_loop() ) : ?>

					<div class="product-grid" data-product-grid>

						<?php
						while ( have_posts() ) :
							the_post();

							$product = wc_get_product( get_the_ID() );

							if (
								! $product instanceof WC_Product ||
								! $product->is_visible()
							) {
								continue;
							}

							northstar_render_product_card( $product );

						endwhile;
						?>

					</div>

					<?php
					/**
					 * WooCommerce pagination.
					 */
					do_action( 'woocommerce_after_shop_loop' );
					?>

				<?php else : ?>

					<?php
					/**
					 * No products found message.
					 */
					do_action( 'woocommerce_no_products_found' );
					?>

				<?php endif; ?>

			</div>

		</section>

	</div>


<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
