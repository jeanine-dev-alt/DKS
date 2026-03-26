<?php
/**
 * Server-side render — kolibri/woningen-grid block.
 *
 * Supports two modes:
 *   'latest'   — queries the N most recent published woningen.
 *   'selected' — queries a specific list of woningen by ID, preserving order.
 *
 * Outputs dks-listings HTML structure so styling is identical to
 * the dks/listings (premium listings) block from the parent theme.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Query;
use KolibriWoningen\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mode       = sanitize_text_field( $attributes['mode']       ?? 'latest' );
$count      = max( 1, (int) ( $attributes['count']           ?? 4 ) );
$columns    = min( 4, max( 2, (int) ( $attributes['columns'] ?? 3 ) ) );
$selected   = array_filter( array_map( 'absint', $attributes['selectedIds'] ?? [] ) );
$title      = sanitize_text_field( $attributes['title']      ?? '' );
$eyebrow    = sanitize_text_field( $attributes['eyebrow']    ?? '' );
$browse_txt = sanitize_text_field( $attributes['browseText'] ?? '' );
$browse_url = esc_url( $attributes['browseUrl'] ?? '' );

// ── Query ──────────────────────────────────────────────────────────────────────

if ( 'selected' === $mode && ! empty( $selected ) ) {
	$query = new WP_Query( [
		'post_type'      => 'kolibri_woning',
		'post__in'       => $selected,
		'orderby'        => 'post__in',
		'posts_per_page' => count( $selected ),
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	] );
} else {
	$query = Query::get_woningen( [
		'per_page' => $count,
		'orderby'  => 'date',
		'order'    => 'DESC',
	] );
}

// ── Card renderer — dks-property-card HTML ─────────────────────────────────────

if ( ! function_exists( 'kolibri_render_woning_as_dks_card' ) ) :
	function kolibri_render_woning_as_dks_card( int $post_id ): string {
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

		$badge = ( '1' === $uitgelicht ) ? esc_html__( 'Uitgelicht', 'kolibri-woningen' ) : '';

		$permalink  = esc_url( get_permalink( $post_id ) );
		$aria_label = esc_attr( sprintf(
			/* translators: %s: prijs */
			__( 'Bekijk woning: %s', 'kolibri-woningen' ),
			$prijs_label
		) );

		ob_start();
		?>
		<article class="dks-property-card">

			<a href="<?php echo $permalink; ?>" class="dks-property-card__image-wrap" aria-label="<?php echo $aria_label; ?>">
				<?php echo Post_Types::render_card_slides( $post_id, 'dks-property-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $badge ) : ?>
					<span class="dks-property-card__badge"><?php echo $badge; ?></span>
				<?php endif; ?>
			</a>

			<div class="dks-property-card__info">

				<h3 class="dks-property-card__price">
					<a href="<?php echo $permalink; ?>"><?php echo esc_html( $prijs_label ); ?></a>
				</h3>

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
							<?php echo esc_html( sprintf(
								_n( '%d Slaapkamer', '%d Slaapkamers', (int) $slaapkamers, 'kolibri-woningen' ),
								(int) $slaapkamers
							) ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $badkamers ) : ?>
						<div class="dks-property-card__meta-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a6 6 0 0112 0v1H6v-1z"/>
							</svg>
							<?php echo esc_html( sprintf(
								_n( '%d Badkamer', '%d Badkamers', (int) $badkamers, 'kolibri-woningen' ),
								(int) $badkamers
							) ); ?>
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
		<?php
		return ob_get_clean();
	}
endif;

// ── Output ─────────────────────────────────────────────────────────────────────

$wrapper_attrs = get_block_wrapper_attributes( [ 'class' => 'dks-listings dks-section' ] );

ob_start();
?>
<section <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="dks-container">

		<?php if ( $title || $eyebrow ) : ?>
			<div class="dks-listings__header">
				<div>
					<?php if ( $eyebrow ) : ?>
						<span class="dks-listings__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h2 class="dks-listings__title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
				</div>
				<?php if ( $browse_txt && $browse_url ) : ?>
					<a href="<?php echo $browse_url; ?>" class="dks-listings__browse">
						<?php echo esc_html( $browse_txt ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/>
						</svg>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="dks-listings__grid" style="--dks-cols:<?php echo esc_attr( $columns ); ?>;">
			<?php if ( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php echo kolibri_render_woning_as_dks_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p style="grid-column:1/-1;text-align:center;padding:4rem 0;color:rgba(26,26,26,.5);font-size:.75rem;text-transform:uppercase;letter-spacing:.2em;">
					<?php esc_html_e( 'Geen woningen gevonden.', 'kolibri-woningen' ); ?>
				</p>
			<?php endif; ?>
		</div>

	</div>
</section>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput
