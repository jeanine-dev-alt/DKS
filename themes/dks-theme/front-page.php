<?php
/**
 * front-page.php — Homepage template.
 *
 * When "A static page" is set as the homepage in Settings › Reading,
 * WordPress loads this template for that page. The Gutenberg editor
 * controls all content via blocks (dks/hero, dks/listings, etc.).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
	<div class="page-content">
		<?php the_content(); ?>
	</div>
<?php endwhile; ?>

<?php get_footer();
