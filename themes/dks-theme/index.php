<?php
/**
 * index.php — The main blog loop template.
 * WordPress requires this file. For the homepage, front-page.php takes priority.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<div class="dks-container dks-section">

	<?php if ( have_posts() ) : ?>

		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="page-header" style="margin-bottom:3rem;">
				<h1 class="page-title"><?php single_post_title(); ?></h1>
			</header>
		<?php endif; ?>

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
