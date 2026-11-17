<?php
/**
 * Display custom rating on the single product page.
 *
 * @return void
 */
function northstar_single_product_rating() {

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $average_rating = (float) $product->get_average_rating();
    $review_count   = (int) $product->get_review_count();
    $filled_stars   = (int) round( $average_rating );
    ?>

    <div class="rating">

        <span class="rating-stars"
            aria-label="<?php echo esc_attr( sprintf( __( 'Rated %1$s out of 5', 'northstar' ), $average_rating ) ); ?>">

            <?php for ( $star = 1; $star <= 5; $star++ ) : ?>

                <span class="star <?php echo $star <= $filled_stars ? 'is-filled' : 'is-empty'; ?>"
                    aria-hidden="true">

                    <?php if ( $star <= $filled_stars ) : ?>

                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            width="16"
                            height="16"
                            fill="currentColor"
                            aria-hidden="true">
                            <path d="M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.47l-5.86 3.09 1.12-6.54-4.75-4.63 6.56-.95L12 2.5z"></path>
                        </svg>

                    <?php else : ?>

                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            width="16"
                            height="16"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            aria-hidden="true">
                            <path d="M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.47l-5.86 3.09 1.12-6.54-4.75-4.63 6.56-.95L12 2.5z"></path>
                        </svg>

                    <?php endif; ?>

                </span>

            <?php endfor; ?>

        </span>

        <span class="muted">
            (<?php echo esc_html( $review_count ); ?><?php echo is_product() ? ' ' . esc_html__( 'reviews', 'northstar' ) : ''; ?>)
        </span>

    </div>

    <?php
}