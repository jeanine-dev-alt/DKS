<?php
/**
 * search.php — Search results template.
 *
 * Handles requests via ?s= (GET parameter).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
get_header();
?>

<div class="dks-container dks-section">

	<header class="page-header" style="margin-bottom:3rem;">
		<h1 class="page-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for: %s', 'dks-theme' ),
				'<span style="color:var(--dks-color-accent);">' . get_search_query() . '</span>'
			);
			?>
		</h1>
		<p style="margin-top:1rem;font-size:1rem;color:rgba(26,26,26,.6);">
			<?php
			global $wp_query;
			printf(
				/* translators: %d: number of results */
				esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'dks-theme' ) ),
				(int) $wp_query->found_posts
			);
			?>
		</p>
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
