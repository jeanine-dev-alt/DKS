<?php
/**
 * archive-kolibri_woning.php — Theme override for the Kolibri Woningen archive.
 *
 * Loaded automatically by the plugin's Template_Loader (theme takes priority).
 * Uses the same search bar and card grid design as the rest of the DKS theme.
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

use KolibriWoningen\Post_Types;
use KolibriWoningen\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();


// ── Active filters from URL ────────────────────────────────────────────────────
$filters = [
	'stad'      => sanitize_text_field( wp_unslash( $_GET['stad']      ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification
	'min_prijs' => absint( $_GET['min_prijs'] ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'max_prijs' => absint( $_GET['max_prijs'] ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'kamers'    => absint( $_GET['kamers']    ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'min_m2'    => absint( $_GET['min_m2']    ?? 0 ),                              // phpcs:ignore WordPress.Security.NonceVerification
	'per_page'  => 9,
	'paged'     => max( 1, (int) ( get_query_var( 'paged' ) ?: 1 ) ),
];

// array_filter removes empty strings and zeros; always keep per_page + paged.
$woningen  = Query::get_woningen( array_filter( $filters ) + [ 'per_page' => 9, 'paged' => $filters['paged'] ] );

$max_pages = $woningen->max_num_pages;
$total     = $woningen->found_posts;
?>

<!-- ── Page hero ────────────────────────────────────────────────────────────── -->
<section class="dks-page-hero">
	<div class="dks-container">
		<p class="dks-page-hero__eyebrow"><?php esc_html_e( 'Properties', 'dks-theme' ); ?></p>
		<h1 class="dks-page-hero__title">
			<?php
			if ( is_tax() ) {
				single_term_title();
			} else {
				echo wp_kses_post( post_type_archive_title( '', false ) ?: __( 'All Properties', 'dks-theme' ) );
			}
			?>
		</h1>
	</div>
</section>

<!-- ── Search bar ──────────────────────────────────────────────────────────────────────── -->
<div class="dks-hero-search dks-overzicht-search" role="search">
	<div class="dks-container">
		<div class="dks-hero-search__wrap">
			<?php
			$dks_active_filters = $filters;
			include get_theme_file_path( 'template-parts/search-filters.php' );
			?>
		</div>
	</div>
</div>

<!-- ── Properties grid ──────────────────────────────────────────────────────── -->
<section class="dks-overzicht dks-section">
	<div class="dks-container">

		<p class="dks-overzicht__count">
			<?php
			printf(
				esc_html( _n( '%d property found', '%d properties found', $total, 'dks-theme' ) ),
				$total
			);
			?>
		</p>

		<div
			id="dks-overzicht-grid"
			class="dks-listings__grid"
			style="--dks-cols:3;"
			data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
			data-current-page="<?php echo esc_attr( $filters['paged'] ); ?>"
		>
			<?php if ( $woningen->have_posts() ) : ?>
				<?php while ( $woningen->have_posts() ) : $woningen->the_post(); ?>
					<?php
					$post_id     = get_the_ID();
					$straat      = Post_Types::get_meta( $post_id, 'straat' );
					$huisnummer  = Post_Types::get_meta( $post_id, 'huisnummer' );
					$toevoeging  = Post_Types::get_meta( $post_id, 'toevoeging' );
					$stad        = Post_Types::get_meta( $post_id, 'stad' );
					$wijk        = Post_Types::get_meta( $post_id, 'wijk' );
					$koopprijs   = Post_Types::get_meta( $post_id, 'koopprijs' );
					$huurprijs   = Post_Types::get_meta( $post_id, 'huurprijs' );
					$prijs_tekst = Post_Types::get_meta( $post_id, 'prijs_tekst' );
					$woon_m2     = Post_Types::get_meta( $post_id, 'woon_m2' );
					$slaapkamers = Post_Types::get_meta( $post_id, 'slaapkamers' );
					$badkamers   = Post_Types::get_meta( $post_id, 'badkamers' );
					$uitgelicht  = Post_Types::get_meta( $post_id, 'uitgelicht' );

					$adres    = trim( $straat . ' ' . $huisnummer . $toevoeging );
					$location = $wijk ? $stad . ', ' . $wijk : $stad;

					$prijs_label = $koopprijs
						? Post_Types::format_price( $koopprijs ) . ( $prijs_tekst ? ' ' . $prijs_tekst : '' )
						: ( $huurprijs ? Post_Types::format_price( $huurprijs ) . ' /mnd' : '' );

					$badge     = ( '1' === $uitgelicht ) ? esc_html__( 'Uitgelicht', 'kolibri-woningen' ) : '';
					$permalink = esc_url( get_permalink( $post_id ) );
					?>
					<article class="dks-property-card">

						<a href="<?php echo $permalink; ?>" class="dks-property-card__image-wrap">
							<?php echo Post_Types::render_card_slides( $post_id, 'dks-property-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php if ( $badge ) : ?>
								<span class="dks-property-card__badge"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>
						</a>

						<div class="dks-property-card__info">

							<h3 class="dks-property-card__price">
								<a href="<?php echo $permalink; ?>"><?php echo esc_html( $prijs_label ); ?></a>
							</h3>

							<?php if ( $adres ) : ?>
								<p class="dks-property-card__address"><?php echo esc_html( $adres ); ?></p>
							<?php endif; ?>

							<?php if ( $location ) : ?>
								<p class="dks-property-card__location">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">
										<path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.979-5.121 3.979-8.827a8.25 8.25 0 00-16.5 0c0 3.706 2.035 6.744 3.979 8.827a19.58 19.58 0 002.686 2.282 16.975 16.975 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
									</svg>
									<?php echo esc_html( $location ); ?>
								</p>
							<?php endif; ?>

							<div class="dks-property-card__meta">
								<?php if ( $slaapkamers ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
										</svg>
										<?php echo esc_html( sprintf( _n( '%d Bedroom', '%d Bedrooms', (int) $slaapkamers, 'dks-theme' ), (int) $slaapkamers ) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( $badkamers ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a6 6 0 0112 0v1H6v-1z"/>
										</svg>
										<?php echo esc_html( sprintf( _n( '%d Bathroom', '%d Bathrooms', (int) $badkamers, 'dks-theme' ), (int) $badkamers ) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( $woon_m2 ) : ?>
									<div class="dks-property-card__meta-item">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
										</svg>
										<?php echo esc_html( $woon_m2 ); ?> m²
									</div>
								<?php endif; ?>
							</div>

						</div>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p class="dks-overzicht__empty">
					<?php esc_html_e( 'No properties found. Try adjusting your search filters.', 'dks-theme' ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $max_pages > $filters['paged'] ) : ?>
			<div class="dks-laad-meer-wrap" style="text-align:center;margin-top:2rem;padding-bottom:1rem;">
				<button
					id="dks-laad-meer"
					type="button"
					class="dks-btn dks-btn--outline"
					data-paged="<?php echo esc_attr( $filters['paged'] + 1 ); ?>"
					data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
					data-stad="<?php echo esc_attr( $filters['stad'] ); ?>"
					data-min-prijs="<?php echo esc_attr( $filters['min_prijs'] ); ?>"
					data-max-prijs="<?php echo esc_attr( $filters['max_prijs'] ); ?>"
					data-kamers="<?php echo esc_attr( $filters['kamers'] ); ?>"
					data-min-m2="<?php echo esc_attr( $filters['min_m2'] ); ?>"
				>
					<?php esc_html_e( 'Laad meer woningen', 'dks-theme' ); ?>
				</button>
				<noscript>
					<a href="<?php echo esc_url( add_query_arg( 'paged', $filters['paged'] + 1, home_url( '/?post_type=kolibri_woning' ) ) ); ?>" class="dks-btn">
						<?php esc_html_e( 'Laad meer woningen', 'dks-theme' ); ?>
					</a>
				</noscript>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php get_footer(); ?>
