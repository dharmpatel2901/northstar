<?php
/**
 * Single post template.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h1><?php the_title(); ?></h1>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>
		<?php the_content(); ?>
	</article>
	<?php
endwhile;
get_footer();
