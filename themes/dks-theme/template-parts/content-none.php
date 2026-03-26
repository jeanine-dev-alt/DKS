<?php
/**
 * template-parts/content-none.php
 *
 * "No results" message used when the loop has no posts.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
?>

<section class="no-results not-found">
	<div class="page-content" style="text-align:center;padding:4rem 0;">

		<h2 style="font-size:clamp(1.5rem,4vw,3rem);font-weight:900;letter-spacing:-.02em;margin-bottom:1rem;">
			<?php esc_html_e( 'Nothing Found', 'dks-theme' ); ?>
		</h2>

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p style="color:rgba(26,26,26,.6);margin-bottom:2rem;">
				<?php
				printf(
					wp_kses(
						/* translators: 1: link to WP admin new post page */
						__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'dks-theme' ),
						[ 'a' => [ 'href' => [] ] ]
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>

		<?php elseif ( is_search() ) : ?>

			<p style="color:rgba(26,26,26,.6);margin-bottom:2rem;">
				<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'dks-theme' ); ?>
			</p>
			<div class="dks-form-wrap">
				<?php get_search_form(); ?>
			</div>

		<?php else : ?>

			<p style="color:rgba(26,26,26,.6);margin-bottom:2rem;">
				<?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching will help.', 'dks-theme' ); ?>
			</p>
			<div class="dks-form-wrap">
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</div>
</section>
