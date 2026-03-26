<?php
/**
 * single.php — Single post template.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<div class="dks-container dks-section">

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="entry-header" style="margin-bottom:2rem;">
				<?php the_title( '<h1 class="entry-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;line-height:1;margin-bottom:1rem;">', '</h1>' ); ?>
				<div class="entry-meta" style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:rgba(26,26,26,.5);">
					<?php dks_posted_on(); ?>
					<?php dks_posted_by(); ?>
				</div>
			</header>

			<?php dks_post_thumbnail( 'dks-property-full' ); ?>

			<div class="entry-content" style="margin-top:2rem;max-width:72ch;">
				<?php the_content(); ?>
			</div>

			<footer class="entry-footer" style="margin-top:2rem;">
				<?php dks_entry_footer(); ?>
			</footer>

		</article>

		<?php
		the_post_navigation( [
			'prev_text' => '&larr; ' . __( 'Previous Post', 'dks-theme' ),
			'next_text' => __( 'Next Post', 'dks-theme' ) . ' &rarr;',
		] );
		?>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<?php comments_template(); ?>
		<?php endif; ?>

	<?php endwhile; ?>

</div>

<?php get_footer();
