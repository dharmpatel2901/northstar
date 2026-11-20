<?php
/**
 * Northstar - WooCommerce Checkout Customizations.
 *
 * Customize the WooCommerce checkout using native hooks and filters.
 *
 * @package Northstar
 */

defined('ABSPATH') || exit;

/**
 * Hide the default checkout page title.
 *
 * @param string $title   Page title.
 * @param int    $post_id Post ID.
 * @return string
 */
function northstar_hide_checkout_page_title($title, $post_id){

    if ( !is_admin() && function_exists('is_checkout') && in_the_loop() && is_checkout() && !is_order_received_page() && (int) $post_id === (int) wc_get_page_id('checkout')) return '';

    return $title;
}
add_filter('the_title', 'northstar_hide_checkout_page_title', 10, 2);

/**
 * Display breadcrumb on the Checkout page.
 */
function northstar_checkout_breadcrumb(){

    if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) {
        return;
    }

    $checkout_page_id = (int) wc_get_page_id('checkout');

    if ($checkout_page_id <= 0) {
        return;
    }

    $checkout_title = get_post_field('post_title', $checkout_page_id);

    echo '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'northstar') . '">';
    echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'northstar') . '</a>';
    echo ' / ';
    echo esc_html($checkout_title);
    echo '</nav>';
}
add_action('woocommerce_before_checkout_form', 'northstar_checkout_breadcrumb', 5);

/**
 * Display the checkout page title.
 */
function northstar_checkout_page_heading(){

    $checkout_page_id = (int) wc_get_page_id('checkout');

    if ($checkout_page_id <= 0) return;

    $checkout_title = get_post_field('post_title', $checkout_page_id);

    if (empty($checkout_title)) return;

    echo '<h1 class="woocommerce-checkout-title">' . esc_html($checkout_title) . '</h1>';
}
add_action('woocommerce_before_checkout_form', 'northstar_checkout_page_heading', 6);



/**
 * Customize WooCommerce checkout billing fields.
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function northstar_customize_billing_fields($fields){

    /*
     * Contact information.
     */
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['label'] = __('Email address', 'northstar');
        $fields['billing']['billing_email']['placeholder'] = __('you@example.com', 'northstar');
        $fields['billing']['billing_email']['priority'] = 5;
    }

    /*
     * First and last name.
     */
    if (isset($fields['billing']['billing_first_name'])) {
        $fields['billing']['billing_first_name']['priority'] = 10;
    }

    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['priority'] = 20;
    }

    /*
     * Address.
     */
    if (isset($fields['billing']['billing_address_1'])) {
        $fields['billing']['billing_address_1']['label'] = __('Address', 'northstar');
        $fields['billing']['billing_address_1']['placeholder'] = __('House number and street name', 'northstar');
        $fields['billing']['billing_address_1']['priority'] = 30;
    }

    if (isset($fields['billing']['billing_city'])) {
        $fields['billing']['billing_city']['label'] = __('City', 'northstar');
        $fields['billing']['billing_city']['priority'] = 40;
    }

    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['label'] = __('Postal code', 'northstar');
        $fields['billing']['billing_postcode']['priority'] = 50;
    }

    if (isset($fields['billing']['billing_country'])) {
        $fields['billing']['billing_country']['label'] = __('Country', 'northstar');
        $fields['billing']['billing_country']['priority'] = 60;
    }

    if (isset($fields['billing']['billing_state'])) {
        $fields['billing']['billing_state']['priority'] = 65;
    }

    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label'] = __('Phone', 'northstar');
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['priority'] = 80;
    }

    /*
     * "Shipping address" heading (pseudo-field, sits between email and first name).
     */
    $fields['billing']['ns_address_heading'] = array(
        'type' => 'ns_heading',
        'label' => __('Shipping address', 'northstar'),
        'required' => false,
        'priority' => 8,
    );

    $full_width = array('billing_email', 'billing_address_1');
    $half_width = array('billing_first_name', 'billing_last_name', 'billing_postcode', 'billing_country');

    foreach ($full_width as $key) {
        if (isset($fields['billing'][$key])) {
            $fields['billing'][$key]['class'] = array('ns-field', 'ns-full');
        }
    }
    foreach ($half_width as $key) {
        if (isset($fields['billing'][$key])) {
            $fields['billing'][$key]['class'] = array('ns-field');
        }
    }

    // Fields we don't use.
    unset($fields['billing']['billing_address_2'], $fields['billing']['billing_state']);

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'northstar_customize_billing_fields', 20);

/**
 * Rename the default "Billing details" heading to "Contact information".
 */
function northstar_rename_billing_heading($translated, $text, $domain)
{

    if ('woocommerce' !== $domain || !function_exists('is_checkout') || !is_checkout() || is_order_received_page()) {
        return $translated;
    }

    if ('Billing details' === $text || 'Billing &amp; Shipping' === $text) {
        return __('Contact information', 'northstar');
    }

    return $translated;
}
add_filter('gettext', 'northstar_rename_billing_heading', 10, 3);

/**
 * Render the pseudo-field as a heading instead of an input.
 */
function northstar_render_heading_field($field, $key, $args)
{
    return sprintf(
        '<div class="form-row ns-heading-row ns-full" id="%1$s_field" data-priority="%2$s"><h3 class="ns-heading ns-heading--address">%3$s</h3></div>',
        esc_attr($key),
        esc_attr($args['priority']),
        esc_html($args['label'])
    );
}
add_filter('woocommerce_form_field_ns_heading', 'northstar_render_heading_field', 10, 3);

/**
 * Stop per-country locales from overriding priorities
 * (this data is also passed to checkout JS, which re-sorts fields).
 */
/**
 * Labels and priorities for address fields.
 * WooCommerce's checkout JS re-applies these from locale data,
 * so they must be set here as well as in woocommerce_checkout_fields.
 */
function northstar_address_overrides()
{
    return array(
        'address_1' => array(
            'label' => __('Address', 'northstar'),
            'priority' => 30,
        ),
        'city' => array(
            'label' => __('City', 'northstar'),
            'priority' => 40,
            'class' => array('ns-field'),
        ),
        'postcode' => array(
            'label' => __('Postal code', 'northstar'),
            'priority' => 50,
            'class' => array('ns-field'),
        ),
        'country' => array(
            'label' => __('Country', 'northstar'),
            'priority' => 60,
        ),
    );
}

function northstar_merge_address_overrides($fields)
{
    foreach (northstar_address_overrides() as $key => $args) {
        $fields[$key] = isset($fields[$key]) ? array_merge($fields[$key], $args) : $args;
    }
    return $fields;
}

// Per-country locales (for example India's "PIN Code").
function northstar_locale_priorities($locales)
{
    foreach ($locales as $country_code => $fields) {
        $locales[$country_code] = northstar_merge_address_overrides($fields);
    }
    return $locales;
}
add_filter('woocommerce_get_country_locale', 'northstar_locale_priorities', 20);

// Default address fields (countries without their own locale).
function northstar_default_address_fields($fields)
{
    return northstar_merge_address_overrides($fields);
}
add_filter('woocommerce_default_address_fields', 'northstar_default_address_fields', 20);

/**
 * Remove "Apartment, suite, unit" (address line 2) everywhere.
 */
// function northstar_remove_address_2( $fields ) {
// 	unset( $fields['address_2'] );
// 	return $fields;
// }
// add_filter( 'woocommerce_default_address_fields', 'northstar_remove_address_2', 20 );

/**
 * Title for the right column (replaces the default "Your order" heading, hidden in CSS).
 */
function northstar_order_summary_title()
{
    echo '<h3 class="ns-summary-title">' . esc_html__('Your Order', 'northstar') . '</h3>';
}
add_action('woocommerce_checkout_order_review', 'northstar_order_summary_title', 5);

// Optional: match the prototype's button text.
add_filter('woocommerce_order_button_text', function () {
    return __('Place order →', 'northstar');
});

/**
 * Remove the "Additional information" section (order notes) from checkout.
 */
add_filter('woocommerce_enable_order_notes_field', '__return_false');


/**
 * Display delivery methods after customer details.
 */
function northstar_delivery_heading()
{

    if (!WC()->cart || !WC()->cart->needs_shipping()) {
        return;
    }

    echo '<h3 class="ns-section-title ns-section-title--delivery">';
    esc_html_e('Delivery method', 'northstar');
    echo '</h3>';

    echo '<div id="northstar-checkout-shipping-methods">';
    echo '<table class="shop_table">';
    echo '<tbody>';

    wc_cart_totals_shipping_html();

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
add_action('woocommerce_checkout_after_customer_details', 'northstar_delivery_heading', 10);

/**
 * Display payment heading after delivery methods.
 */
function northstar_payment_heading()
{

    echo '<h3 class="ns-section-title ns-section-title--payment">';
    esc_html_e('Payment', 'northstar');
    echo '</h3>';
}
add_action('woocommerce_checkout_after_customer_details', 'northstar_payment_heading', 15);

remove_action(
    'woocommerce_checkout_order_review',
    'woocommerce_checkout_payment',
    20
);

add_action(
    'woocommerce_checkout_after_customer_details',
    'woocommerce_checkout_payment',
    20
);


function northstar_strip_shipping_row($html)
{
    return preg_replace('#<tr class="woocommerce-shipping-totals shipping">.*?</tr>#s', '', $html);
}

/**
 * Update checkout shipping methods and order summary during AJAX refresh.
 *
 * @param array $fragments Checkout fragments.
 * @return array
 */
function northstar_update_checkout_fragments($fragments)
{

    if (isset($fragments['.woocommerce-checkout-review-order-table'])) {
        $fragments['.woocommerce-checkout-review-order-table'] = northstar_strip_shipping_row(
            $fragments['.woocommerce-checkout-review-order-table']
        );
    }

    ob_start();
    wc_cart_totals_shipping_html();
    $fragments['#northstar-checkout-shipping-methods'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_update_order_review_fragments', 'northstar_update_checkout_fragments', 20);