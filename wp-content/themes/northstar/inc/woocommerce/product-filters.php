
<?php
/**
 * Northstar WooCommerce AJAX Product Filters.
 *
 * Handles product filtering, searching, sorting and pagination
 * through WordPress AJAX.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;


/**
 * Register AJAX actions for logged-in and guest users.
 */
function northstar_register_product_filter_ajax() {

    add_action(
        'wp_ajax_northstar_filter_products',
        'northstar_ajax_filter_products'
    );

    add_action(
        'wp_ajax_nopriv_northstar_filter_products',
        'northstar_ajax_filter_products'
    );

    add_action(
        'wp_ajax_northstar_load_more_categories',
        'northstar_ajax_load_more_categories'
    );

    add_action(
        'wp_ajax_nopriv_northstar_load_more_categories',
        'northstar_ajax_load_more_categories'
    );
}
add_action( 'init', 'northstar_register_product_filter_ajax' );


/**
 * Render product category checkbox items.
 *
 * @param array $categories Product categories.
 * @return string
 */
function northstar_render_product_category_items( $categories ) {

    ob_start();

    foreach ( $categories as $category ) :
        ?>
        <label class="filter-option">

            <input
                type="checkbox"
                name="category[]"
                value="<?php echo esc_attr( $category->slug ); ?>"
                data-filter="category"
            >

            <span>
                <?php echo esc_html( $category->name ); ?>
            </span>

            <span class="filter-count">
                <?php echo esc_html( $category->count ); ?>
            </span>

        </label>
        <?php
    endforeach;

    return ob_get_clean();
}


/**
 * Render the initial product category filter.
 *
 * Displays five categories initially.
 *
 * @return void
 */
function northstar_render_product_category_filter() {

	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => 5,
			'offset'     => 0,
		)
	);

	if ( is_wp_error( $categories ) || empty( $categories ) ) {
		return;
	}

	$total_categories = wp_count_terms(
		'product_cat',
		array(
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $total_categories ) ) {
		$total_categories = count( $categories );
	}

	?>

	<div class="filter-group">

		<h3 class="filter-heading">
			<?php esc_html_e( 'Collection', 'northstar' ); ?>
		</h3>

		<div class="filter-options" data-category-list>

			<?php
			echo northstar_render_product_category_items( $categories ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>

		</div>

		<?php if ( (int) $total_categories > 5 ) : ?>

			<button
				type="button"
				class="category-load-more"
				data-category-load-more
				data-page="1"
			>
				<?php esc_html_e( 'Load More', 'northstar' ); ?>
			</button>

		<?php endif; ?>

	</div>

	<?php
}

/**
 * Load the next five product categories through AJAX.
 *
 * @return void
 */
function northstar_ajax_load_more_categories() {

	check_ajax_referer(
		'northstar_product_filter',
		'nonce'
	);

	$page = isset( $_POST['page'] ) && is_scalar( $_POST['page'] )
		? absint( wp_unslash( $_POST['page'] ) )
		: 0;

	$per_page = 5;
	$offset   = $page * $per_page;

	$total_categories = wp_count_terms(
		'product_cat',
		array(
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $total_categories ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Unable to load categories.', 'northstar' ),
			),
			500
		);
	}

	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => $per_page,
			'offset'     => $offset,
		)
	);

	if ( is_wp_error( $categories ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Unable to load categories.', 'northstar' ),
			),
			500
		);
	}

	$html = northstar_render_product_category_items( $categories );

	$has_more = ( $offset + count( $categories ) ) < (int) $total_categories;

	wp_send_json_success(
		array(
			'html'     => $html,
			'has_more' => $has_more,
		)
	);
}


/**
 * Get sanitized filter values from the AJAX request.
 *
 * @param string $key     Request parameter.
 * @param array  $allowed Allowed values. Empty means any sanitized value.
 * @return array
 */
function northstar_get_product_filter_values( $key, $allowed = array() ) {

    if (
        ! isset( $_POST[ $key ] ) ||
        ! is_array( $_POST[ $key ] )
    ) {
        return array();
    }

    $values = wp_unslash( $_POST[ $key ] );

    $values = array_map(
        'sanitize_text_field',
        $values
    );

    $values = array_filter( $values );

    $values = array_unique( $values );

    if ( ! empty( $allowed ) ) {
        $values = array_intersect( $values, $allowed );
    }

    return array_values( $values );
}



/**
 * Get sanitized product filter request data.
 *
 * @return array
 */
function northstar_get_product_filter_data() {

    $search = isset( $_POST['search'] )
        ? sanitize_text_field( wp_unslash( $_POST['search'] ) )
        : '';

    $sort = isset( $_POST['sort'] )
        ? sanitize_key( wp_unslash( $_POST['sort'] ) )
        : 'featured';

    $paged = isset( $_POST['paged'] )
        ? absint( $_POST['paged'] )
        : 1;

    // Get minimum price.
    $min_price = isset( $_POST['min_price'] ) && is_scalar( $_POST['min_price'] )
        ? wc_format_decimal( wp_unslash( $_POST['min_price'] ) )
        : '';

    // Get maximum price.
    $max_price = isset( $_POST['max_price'] ) && is_scalar( $_POST['max_price'] )
        ? wc_format_decimal( wp_unslash( $_POST['max_price'] ) )
        : '';

    // Validate minimum price.
    if (
        '' !== $min_price &&
        ( ! is_numeric( $min_price ) || (float) $min_price < 0 )
    ) {
        $min_price = '';
    }

    // Validate maximum price.
    if (
        '' !== $max_price &&
        ( ! is_numeric( $max_price ) || (float) $max_price < 0 )
    ) {
        $max_price = '';
    }

    $allowed_sort_values = array(
        'featured',
        'popularity',
        'rating',
        'latest',
        'low',
        'high',
        'name',
    );

    return array(
        'categories' => northstar_get_product_filter_values( 'category' ),
        'stock'      => northstar_get_product_filter_values(
            'stock',
            array( 'instock', 'outofstock' )
        ),
        'min_price'  => $min_price,
        'max_price'  => $max_price,
        'search'     => $search,
        'sort' => in_array(
            $sort,
            $allowed_sort_values,
            true
        ) ? $sort : 'featured',
        'paged'      => max( 1, $paged ),
    );
}


/**
 * Build the WooCommerce product query arguments.
 *
 * @param array $filters Sanitized filter data.
 * @return array
 */
function northstar_get_product_filter_query_args( $filters ) {

    $args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'posts_per_page'      => 12,
        'paged'               => $filters['paged'],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    );

    /*
     * Product search.
     */
    if ( ! empty( $filters['search'] ) ) {
        $args['s'] = $filters['search'];
    }

    /*
     * Product visibility.
     */
    if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {

        $visibility_terms = wc_get_product_visibility_term_ids();

        if ( ! empty( $visibility_terms['exclude-from-catalog'] ) ) {

            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'term_taxonomy_id',
                'terms'    => array(
                    $visibility_terms['exclude-from-catalog'],
                ),
                'operator' => 'NOT IN',
            );
        }
    }

    /*
     * Product categories.
     */
    if ( ! empty( $filters['categories'] ) ) {

        $valid_categories = array();

        foreach ( $filters['categories'] as $category_slug ) {

            $term = get_term_by(
                'slug',
                sanitize_title( $category_slug ),
                'product_cat'
            );

            if ( $term && ! is_wp_error( $term ) ) {
                $valid_categories[] = (int) $term->term_id;
            }
        }

        if ( ! empty( $valid_categories ) ) {

            $args['tax_query'][] = array(
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => $valid_categories,
                'operator'         => 'IN',
                'include_children' => true,
            );
        }
    }

    /*
     * Stock status.
     */
    if ( ! empty( $filters['stock'] ) ) {

        $args['meta_query'][] = array(
            'key'     => '_stock_status',
            'value'   => $filters['stock'],
            'compare' => 'IN',
        );
    }

    /*
     * Minimum and maximum price filtering.
     */
    $min_price = $filters['min_price'];
    $max_price = $filters['max_price'];

    if ( '' !== $min_price && '' !== $max_price ) {

        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => array(
                (float) $min_price,
                (float) $max_price,
            ),
            'compare' => 'BETWEEN',
            'type'    => 'DECIMAL',
        );

    } elseif ( '' !== $min_price ) {

        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => (float) $min_price,
            'compare' => '>=',
            'type'    => 'DECIMAL',
        );

    } elseif ( '' !== $max_price ) {

        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => (float) $max_price,
            'compare' => '<=',
            'type'    => 'DECIMAL',
        );
    }

    /*
    * Sorting.
    */
    switch ( $filters['sort'] ) {

        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        case 'latest':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;

        case 'low':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;

        case 'high':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        case 'name':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;

        case 'featured':
        default:
            $args['orderby'] = array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            );
            break;
    }

    return $args;
}




/**
 * Render the AJAX product results.
 *
 * Uses the reusable Northstar product card function.
 *
 * @param WP_Query $products Product query.
 * @param array    $filters  Sanitized filter data.
 * @return string
 */
function northstar_render_ajax_product_results( $products, $filters ) {

    ob_start();

    if ( $products->have_posts() ) :
        ?>

        <div class="product-grid" data-product-grid>

            <?php
            while ( $products->have_posts() ) :

                $products->the_post();

                $product = wc_get_product( get_the_ID() );

                if (
                    $product instanceof WC_Product &&
                    $product->is_visible()
                ) {
                    northstar_render_product_card( $product );
                }

            endwhile;
            ?>

        </div>

        <?php
        /*
         * Product pagination.
         */
        
        $big = 999999999;

        $pagination = paginate_links(
            array(
                'base'      => str_replace(
                    $big,
                    '%#%',
                    esc_url( get_pagenum_link( $big ) )
                ),
                'format'    => '',
                'current'   => max( 1, (int) $filters['paged'] ),
                'total'     => (int) $products->max_num_pages,
                'type'      => 'list',
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
            )
        );

        if ( $pagination ) :
            ?>

            <nav
                class="woocommerce-pagination northstar-pagination"
                aria-label="<?php esc_attr_e( 'Product pagination', 'northstar' ); ?>"
            >
                <?php echo wp_kses_post( $pagination ); ?>
            </nav>

        <?php endif; ?>

    <?php else : ?>

        <div class="woocommerce-info" role="status">
            <?php
            esc_html_e(
                'No products found matching your filters.',
                'northstar'
            );
            ?>
        </div>

    <?php
    endif;

    wp_reset_postdata();

    return ob_get_clean();
}



/**
 * Handle the AJAX product filter request.
 *
 * @return void
 */
function northstar_ajax_filter_products() {

    /*
     * Verify the AJAX nonce.
     */
    check_ajax_referer(
        'northstar_product_filter',
        'nonce'
    );

    /*
     * Check WooCommerce availability.
     */
    if ( ! class_exists( 'WooCommerce' ) ) {

        wp_send_json_error(
            array(
                'message' => __(
                    'WooCommerce is not active.',
                    'northstar'
                ),
            ),
            400
        );
    }

    /*
     * Get sanitized filter values.
     */
    $filters = northstar_get_product_filter_data();

    /*
     * Build product query.
     */
    $query_args = northstar_get_product_filter_query_args( $filters );

    /*
     * Execute query.
     */
    $products = new WP_Query( $query_args );

    /*
     * Render product results.
     */
    $html = northstar_render_ajax_product_results(
        $products,
        $filters
    );

    /*
     * Send AJAX response.
     */
    wp_send_json_success(
        array(
            'html'         => $html,
            'found_posts'  => (int) $products->found_posts,
            'max_pages'    => (int) $products->max_num_pages,
            'current_page' => (int) $products->get( 'paged' ),
            'per_page'     => (int) $products->get( 'posts_per_page' ),
        )
    );
}
