<?php
/**
 * Native WordPress meta box for the homepage hero.
 *
 * @package Northstar
 */

defined('ABSPATH') || exit;

add_action('add_meta_boxes_page', 'northstar_add_homepage_hero_meta_box');
add_action('save_post_page', 'northstar_save_homepage_hero_meta_box');

/**
 * Add the hero fields to the page editor.
 *
 * @param WP_Post $post Current page.
 * @return void
 */
function northstar_add_homepage_hero_meta_box($post)
{
	$front_page_id = (int) get_option('page_on_front');

	if ($front_page_id !== (int) $post->ID) {
		return;
	}

	add_meta_box(
		'northstar-homepage-hero',
		__('Northstar Homepage Hero', 'northstar'),
		'northstar_render_homepage_hero_meta_box',
		'page',
		'normal',
		'high'
	);
}

/**
 * Render homepage hero fields.
 *
 * @param WP_Post $post Current page.
 * @return void
 */
function northstar_render_homepage_hero_meta_box($post)
{
	wp_nonce_field('northstar_save_homepage_hero', 'northstar_homepage_hero_nonce');

	$sections = array(
		'section1' => array(
			'label' => __('Hero Section', 'northstar'),
			'prefix' => 'northstar_hero',
			'meta_prefix' => '_northstar_hero',
			'fields' => array(
				'eyebrow' => 'Eyebrow',
				'title' => 'Heading',
				'description' => 'Description',
				'button_text' => 'Button Label',
				'button_url' => 'Button URL',
				'image' => 'Hero Image',
			),
		),
		'section2' => array(
			'label' => __('Product Section', 'northstar'),
			'prefix' => 'northstar_product',
			'meta_prefix' => '_northstar_product',
			'fields' => array(
				'subtitle' => 'Sub Title',
				'title' => 'Heading',
				'button_text' => 'Button Label',
				'button_url' => 'Button URL',
			),
		),
		'section3' => array(
			'label' => __('Secondary Section', 'northstar'),
			'prefix' => 'northstar_secondary',
			'meta_prefix' => '_northstar_secondary',
			'fields' => array(
				'eyebrow' => 'Eyebrow',
				'title' => 'Heading',
				'description' => 'Description',
				'button_text' => 'Button Label',
				'button_url' => 'Button URL',
				'image' => 'Secondary Hero Image',
			),
		),
	);
	foreach ($sections as $section_key => $section) {
		?>
		<div class="<?php echo esc_attr($section_key); ?>">
			<h3><?php echo esc_html($section['label']); ?></h3>

			<?php foreach ($section['fields'] as $field_key => $label): ?>
				<?php
				$meta_key = $section['meta_prefix'] . '_' . $field_key;
				$value = get_post_meta($post->ID, $meta_key, true);
				$field_id = $section['prefix'] . '-' . $field_key;
				$name = $section['prefix'] . '_' . $field_key;
				?>

				<p>
					<label for="<?php echo esc_attr($field_id); ?>">
						<strong><?php echo esc_html($label); ?></strong>
					</label><br>

					<?php if ('image' === $field_key): ?>

						<?php
						$image_id = absint($value);
						$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
						?>

					<div class="northstar-image-field">

						<input type="hidden" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>"
							value="<?php echo esc_attr($image_id); ?>">

						<div class="northstar-image-preview">
							<?php if ($image_url): ?>
								<img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 100%; height: auto;">
							<?php endif; ?>
						</div>

						<button type="button" class="button northstar-upload-image">
							<?php esc_html_e('Select Image', 'northstar'); ?>
						</button>

						<button type="button" class="button northstar-remove-image" <?php disabled(!$image_id); ?>>
							<?php esc_html_e('Remove Image', 'northstar'); ?>
						</button>

					</div>

				<?php elseif ('description' === $field_key): ?>

					<textarea class="widefat" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>"
						rows="4"><?php echo esc_textarea($value); ?></textarea>

				<?php elseif ('button_url' === $field_key): ?>

					<input class="widefat" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>" type="url"
						value="<?php echo esc_attr($value); ?>">

				<?php else: ?>

					<input class="widefat" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($name); ?>" type="text"
						value="<?php echo esc_attr($value); ?>">

				<?php endif; ?>
				</p>
			<?php endforeach; ?>
		</div>
		<?php
	}

	?>
	<p class="description">
		<?php esc_html_e('These fields use WordPress post meta. No custom-field plugin is required.', 'northstar'); ?>
	</p>
	<?php
}

/**
 * Validate and save homepage hero meta.
 *
 * @param int $post_id Saved page ID.
 * @return void
 */
function northstar_save_homepage_hero_meta_box($post_id)
{
	if (!isset($_POST['northstar_homepage_hero_nonce'])) {
		return;
	}

	$nonce = sanitize_text_field(wp_unslash($_POST['northstar_homepage_hero_nonce']));

	if (!wp_verify_nonce($nonce, 'northstar_save_homepage_hero')) {
		return;
	}

	if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) {
		return;
	}

	if (!current_user_can('edit_page', $post_id)) {
		return;
	}

	$sections = array(
		'northstar_hero' => array(
			'eyebrow',
			'title',
			'description',
			'button_text',
			'button_url',
			'image',
		),
		'northstar_product' => array(
			'subtitle',
			'title',
			'button_text',
			'button_url',
		),
		'northstar_secondary' => array(
			'eyebrow',
			'title',
			'description',
			'button_text',
			'button_url',
			'image',
		),
	);

	foreach ($sections as $prefix => $fields) {
		foreach ($fields as $field) {
			$input_key = $prefix . '_' . $field;
			$meta_key = '_' . $input_key;

			if (!isset($_POST[$input_key])) {
				delete_post_meta($post_id, $meta_key);
				continue;
			}

			$value = wp_unslash($_POST[$input_key]);
			if ('image' === $field) {
				$value = absint($value);
			} elseif ('image' === $field) {
				$value = absint($value);
			} elseif ('button_url' === $field) {
				$value = esc_url_raw($value);
			} elseif ('description' === $field) {
				$value = sanitize_textarea_field($value);
			} else {
				$value = sanitize_text_field($value);
			}

			update_post_meta($post_id, $meta_key, $value);
		}
	}
}