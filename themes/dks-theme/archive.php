<?php
/**
 * archive.php — Archive template (categories, tags, author, date).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<div class="dks-container dks-section">

	<header class="page-header" style="margin-bottom:3rem;">
		<?php the_archive_title( '<h1 class="page-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description" style="margin-top:1rem;font-size:1rem;color:rgba(26,26,26,.6);">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="posts-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:2.5rem;">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', get_post_type() ); ?>
			<?php endwhile; ?>
		</div>
		<?php dks_numeric_posts_nav(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</div>

<?php get_footer();
