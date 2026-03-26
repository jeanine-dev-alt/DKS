<?php
/**
 * template-parts/content.php
 *
 * Default post card used in the blog loop (index.php, archive.php).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'dks-post-card' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="dks-post-card__thumb" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'dks-thumbnail', [ 'class' => 'dks-post-card__img' ] ); ?>
		</a>
	<?php endif; ?>

	<div class="dks-post-card__body">

		<?php
		$categories = get_the_category();
		if ( $categories ) : ?>
			<div class="dks-post-card__cats">
				<?php foreach ( array_slice( $categories, 0, 2 ) as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
					   class="dks-post-card__cat">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h2 class="dks-post-card__title">
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<?php the_title(); ?>
			</a>
		</h2>

		<div class="dks-post-card__meta">
			<?php dks_posted_on(); ?>
			<?php dks_posted_by(); ?>
		</div>

		<div class="dks-post-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a href="<?php the_permalink(); ?>" class="dks-post-card__read-more">
			<?php esc_html_e( 'Read More', 'dks-theme' ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
				stroke="currentColor" stroke-width="1.5" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round"
					d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/>
			</svg>
		</a>

	</div>

</article>
