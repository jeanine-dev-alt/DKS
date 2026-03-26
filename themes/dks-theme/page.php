<?php
/**
 * page.php — Default page template.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if ( ! is_front_page() ) : ?>
			<header class="entry-header dks-container" style="padding-top:4rem;padding-bottom:2rem;">
				<?php the_title( '<h1 class="entry-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;">', '</h1>' ); ?>
			</header>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

	</article>

<?php endwhile; ?>

<?php get_footer();
