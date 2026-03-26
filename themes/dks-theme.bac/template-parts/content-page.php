<?php
/**
 * template-parts/content-page.php
 *
 * Used by page.php for the main page content area.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( ! is_front_page() ) : ?>
		<header class="entry-header dks-container" style="padding-top:4rem;padding-bottom:2rem;">
			<?php the_title( '<h1 class="entry-title" style="font-size:clamp(2rem,6vw,4rem);font-weight:900;letter-spacing:-0.02em;">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<?php dks_post_thumbnail( 'dks-property-full', [ 'class' => 'entry-thumbnail' ] ); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( [
			'before'    => '<div class="page-links">' . esc_html__( 'Pages:', 'dks-theme' ),
			'after'     => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
		] );
		?>
	</div>

</article>
