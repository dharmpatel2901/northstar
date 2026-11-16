<?php
/**
 * Homepage.
 *
 * @package Northstar
 */

defined('ABSPATH') || exit;

get_header();

$page_id = get_queried_object_id();

$eyebrow = get_post_meta($page_id, '_northstar_hero_eyebrow', true);
$title = get_post_meta($page_id, '_northstar_hero_title', true);
$description = get_post_meta($page_id, '_northstar_hero_description', true);
$button_text = get_post_meta($page_id, '_northstar_hero_button_text', true);
$button_url = get_post_meta($page_id, '_northstar_hero_button_url', true);
$hero_image_id = absint(
    get_post_meta($page_id, '_northstar_hero_image', true)
);
?>
<section class="hero">
    <div>
        <?php if ($eyebrow): ?>
            <div class="eyebrow">
                <?php echo esc_html($eyebrow); ?>
            </div>
        <?php endif; ?>
        <?php if ($title): ?>
            <h1><?php echo esc_html($title); ?></h1>
        <?php endif; ?>
        <?php if ($description): ?>
            <p class="lead"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php if ($button_url && $button_text): ?>
            <a class="btn" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?> <span
                    aria-hidden="true">→</span></a>
        <?php endif; ?>
    </div>
    <div class="hero-art">
            <?php
            if ($hero_image_id) {
                echo wp_get_attachment_image(
                    $hero_image_id,
                    'thumbnail',
                    false,
                    array(
                        'class' => 'hero-image',
                        'loading' => 'eager',
                    )
                );
            }
            ?>
    </div>
</section>
<?php get_template_part('home', 'hero'); ?>


<?php
$product_subtitle = get_post_meta($page_id, '_northstar_product_subtitle', true);
$product_title = get_post_meta($page_id, '_northstar_product_title', true);
$product_button_text = get_post_meta($page_id, '_northstar_product_button_text', true);
$product_button_url = get_post_meta($page_id, '_northstar_product_button_url', true);
?>

<div class="section-head" id="everyday-favourites">
    <div>
        <?php if ($product_subtitle): ?>
            <div class="eyebrow">
                <?php echo esc_html($product_subtitle); ?>
            </div>
        <?php endif; ?>

        <?php if ($product_title): ?>
            <h2><?php echo esc_html($product_title); ?></h2>
        <?php endif; ?>
    </div>

    <?php if ($product_button_text && $product_button_url): ?>
        <a class="text-link" href="<?php echo esc_url($product_button_url); ?>">
            <?php echo esc_html($product_button_text); ?> →
        </a>
    <?php endif; ?>
</div>



<?php
if (class_exists('WooCommerce')):

    $products = wc_get_products(
        array(
            'limit' => 4,
            'status' => 'publish',
            'orderby' => 'popularity',
            'order' => 'DESC',
        )
    );

    if (!empty($products)):
        ?>

        <div class="product-grid">

            <?php foreach ( $products as $product ) : ?>

                <?php northstar_render_product_card( $product ); ?>

            <?php endforeach; ?>

        </div>
        <?php
    endif;

else:
    echo '<p>' . esc_html__('Activate WooCommerce to display products here.', 'northstar') . '</p>';
endif;
?>



<?php
$secondary_eyebrow = get_post_meta($page_id, '_northstar_secondary_eyebrow', true);
$secondary_title = get_post_meta($page_id, '_northstar_secondary_title', true);
$secondary_description = get_post_meta($page_id, '_northstar_secondary_description', true);
$secondary_button_text = get_post_meta($page_id, '_northstar_secondary_button_text', true);
$secondary_button_url = get_post_meta($page_id, '_northstar_secondary_button_url', true);
$secondary_image_id = absint(
    get_post_meta($page_id, '_northstar_secondary_image', true)
);
?>

<?php if ($secondary_eyebrow || $secondary_title || $secondary_description || ($secondary_button_text && $secondary_button_url)): ?>
    <section class="hero" style="margin-top:65px">
        <div>
            <?php if ($secondary_eyebrow): ?>
                <div class="eyebrow">
                    <?php echo esc_html($secondary_eyebrow); ?>
                </div>
            <?php endif; ?>

            <?php if ($secondary_title): ?>
                <h2><?php echo esc_html($secondary_title); ?></h2>
            <?php endif; ?>

            <?php if ($secondary_description): ?>
                <p class="lead">
                    <?php echo esc_html($secondary_description); ?>
                </p>
            <?php endif; ?>

            <?php if ($secondary_button_text && $secondary_button_url): ?>
                <a class="btn secondary" href="<?php echo esc_url($secondary_button_url); ?>">
                    <?php echo esc_html($secondary_button_text); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="hero-art">
                    <?php
                    if ($secondary_image_id) {
                        echo wp_get_attachment_image(
                            $secondary_image_id,
                            'thumbnail',
                            false,
                            array(
                                'class' => 'hero-image',
                                'loading' => 'lazy',
                            )
                        );
                    }
                    ?>
        </div>
    </section>
<?php endif; ?>
<?php
get_footer();
