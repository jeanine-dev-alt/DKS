<?php
/**
 * single.php — Single post template.
 *
 * Follows the same structure as page.php so typography, headings and
 * paragraph styles are identical.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<header class="entry-header dks-container" style="padding-top:4rem;padding-bottom:2rem;">
			<?php the_title( '<h1 class="entry-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;">', '</h1>' ); ?>
			<div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:rgba(26,26,26,.5);margin-top:.75rem;">
				<?php dks_posted_on(); ?>
				<?php dks_posted_by(); ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="dks-container" style="margin-bottom:2rem;">
				<?php dks_post_thumbnail( 'dks-property-full' ); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

		<footer class="entry-footer dks-container" style="margin-top:2rem;padding-bottom:4rem;">
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

<?php get_footer();
