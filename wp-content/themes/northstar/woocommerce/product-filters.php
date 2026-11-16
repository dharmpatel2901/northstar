<?php
/**
 * Dynamic WooCommerce product filters.
 *
 * @package Northstar
 */

defined('ABSPATH') || exit;

global $wpdb;   

/**
 * Get product stock counts.
 */
$lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';

$stock_counts = $wpdb->get_results(
    "
	SELECT lookup.stock_status, COUNT(DISTINCT lookup.product_id) AS product_count
	FROM {$lookup_table} AS lookup
	INNER JOIN {$wpdb->posts} AS posts
		ON posts.ID = lookup.product_id
	WHERE posts.post_type = 'product'
	AND posts.post_status = 'publish'
	GROUP BY lookup.stock_status
	",
    OBJECT_K
);

/**
 * Get minimum and maximum product prices.
 */
/**
 * Get minimum and maximum prices of published catalog products.
 */
$price_range = $wpdb->get_row(
    "
	SELECT
		MIN( lookup.min_price ) AS min_price,
		MAX( lookup.max_price ) AS max_price
	FROM {$lookup_table} AS lookup
	INNER JOIN {$wpdb->posts} AS posts
		ON posts.ID = lookup.product_id
	WHERE posts.post_type = 'product'
		AND posts.post_status = 'publish'
		AND NOT EXISTS (
			SELECT 1
			FROM {$wpdb->term_relationships} AS relationships
			INNER JOIN {$wpdb->term_taxonomy} AS taxonomy
				ON taxonomy.term_taxonomy_id = relationships.term_taxonomy_id
			INNER JOIN {$wpdb->terms} AS terms
				ON terms.term_id = taxonomy.term_id
			WHERE relationships.object_id = posts.ID
				AND taxonomy.taxonomy = 'product_visibility'
				AND terms.slug = 'exclude-from-catalog'
		)
	"
);

$minimum_price = isset($price_range->min_price)
    ? (float) $price_range->min_price
    : 0;

$maximum_price = isset($price_range->max_price)
    ? (float) $price_range->max_price
    : 0;

?>

<aside class="filters" id="northstar-product-filters">

    <h3><?php esc_html_e('Filter by', 'northstar'); ?></h3>

    <!-- Product Categories -->
    <?php northstar_render_product_category_filter(); ?>

    <!-- Product Availability -->
    <div class="filter-group">

        <h4><?php esc_html_e('Availability', 'northstar'); ?></h4>

        <?php
        $stock_labels = array(
            'instock' => __('In stock', 'northstar'),
            'outofstock' => __('Out of stock', 'northstar'),
        );
        ?>

        <?php foreach ($stock_labels as $stock_status => $stock_label): ?>

            <?php
            $count = isset($stock_counts[$stock_status])
                ? (int) $stock_counts[$stock_status]->product_count
                : 0;
            ?>

            <?php if ($count > 0): ?>

                <label class="filter-option">

                    <input type="checkbox" name="stock[]" value="<?php echo esc_attr($stock_status); ?>" data-filter="stock">

                    <span><?php echo esc_html($stock_label); ?></span>

                    <span class="filter-count">
                        <?php echo esc_html($count); ?>
                    </span>

                </label>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>


    <!-- Product Price -->
    <div class="filter-group">

        <h4><?php esc_html_e('Price', 'northstar'); ?></h4>

        <div class="filter-price-fields">

            <label for="northstar-min-price">
                <?php esc_html_e('Minimum price', 'northstar'); ?>
            </label>

            <input type="number" id="northstar-min-price" name="min_price" data-filter="min-price"
                min="<?php echo esc_attr($minimum_price); ?>" max="<?php echo esc_attr($maximum_price); ?>"
                step="0.01" placeholder="<?php echo esc_attr($minimum_price); ?>">

            <label for="northstar-max-price">
                <?php esc_html_e('Maximum price', 'northstar'); ?>
            </label>

            <input type="number" id="northstar-max-price" name="max_price" data-filter="max-price"
                min="<?php echo esc_attr($minimum_price); ?>" max="<?php echo esc_attr($maximum_price); ?>"
                step="0.01" placeholder="<?php echo esc_attr($maximum_price); ?>">

            <p class="filter-price-range">
                <?php
                printf(
                    /* translators: 1: Minimum price, 2: Maximum price. */
                    esc_html__('Price range: %1$s – %2$s', 'northstar'),
                    wp_kses_post(wc_price($minimum_price)),
                    wp_kses_post(wc_price($maximum_price))
                );
                ?>
            </p>

        </div>

    </div>


    <button type="button" class="btn secondary small" id="northstar-clear-filters">
        <?php esc_html_e('Clear filters', 'northstar'); ?>
    </button>

</aside>