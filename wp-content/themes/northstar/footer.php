<?php
/**
 * Footer template.
 *
 * @package Northstar
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<footer class="footer">
	<div class="container">
		<div class="footer-grid">
			<div>
				<?php northstar_site_branding(); ?>
				<?php
				$footer_description = get_option( 'northstar_footer_description', '' );

				if ( '' !== trim( $footer_description ) ) :
				?>
					<p>
						<?php echo nl2br( esc_html( $footer_description ) ); ?>
					</p>
				<?php endif; ?>
			</div>
			<div>
				<h4><?php esc_html_e( 'Explore', 'northstar' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
			<div>
				<h4><?php esc_html_e( 'Help', 'northstar' ); ?></h4>
				<p><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'northstar' ); ?></a></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Stay in the loop', 'northstar' ); ?></h4>
				<p><?php esc_html_e( 'Notes on new things and good ideas.', 'northstar' ); ?></p>
			</div>
		</div>
		<div class="footer-bottom">
			<?php
			printf(
				/* translators: %1$s: current year, %2$s: site name. */
				esc_html__( '© %1$s %2$s. All rights reserved.', 'northstar' ),
				esc_html( gmdate( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
