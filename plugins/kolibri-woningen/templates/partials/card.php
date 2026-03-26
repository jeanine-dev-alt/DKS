<?php
/**
 * Woning card — used in grid views.
 *
 * Expected $post_id to be set before include (via Template_Loader::partial).
 * Falls back to get_the_ID() inside the loop.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id         = $post_id ?? get_the_ID();
$straat     = Post_Types::get_meta( $id, 'straat' );
$huisnummer = Post_Types::get_meta( $id, 'huisnummer' );
$toevoeging = Post_Types::get_meta( $id, 'toevoeging' );
$stad       = Post_Types::get_meta( $id, 'stad' );
$koopprijs  = Post_Types::get_meta( $id, 'koopprijs' );
$huurprijs  = Post_Types::get_meta( $id, 'huurprijs' );
$prijs_tekst= Post_Types::get_meta( $id, 'prijs_tekst' );
$woon_m2    = Post_Types::get_meta( $id, 'woon_m2' );
$kamers     = Post_Types::get_meta( $id, 'kamers' );
$slaapkamers= Post_Types::get_meta( $id, 'slaapkamers' );
$energielabel=Post_Types::get_meta( $id, 'energielabel' );

$adres = trim( $straat . ' ' . $huisnummer . $toevoeging );

$prijs_label = $koopprijs
	? Post_Types::format_price( $koopprijs ) . ( $prijs_tekst ? ' ' . $prijs_tekst : '' )
	: ( $huurprijs ? Post_Types::format_price( $huurprijs ) . ' /mnd' : '' );

// Status term.
$status_terms = get_the_terms( $id, 'kolibri_status' );
$status_label = ! is_wp_error( $status_terms ) && $status_terms ? $status_terms[0]->name : '';
$status_slug  = ! is_wp_error( $status_terms ) && $status_terms ? $status_terms[0]->slug : '';

?>

<article class="kolibri-card" data-id="<?php echo esc_attr( $id ); ?>">
	<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="kolibri-card-img-wrap" tabindex="-1" aria-hidden="true">
		<?php echo Post_Types::render_card_slides( $id, 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

		<!-- Status badge -->
		<?php if ( $status_label ) : ?>
			<span class="kolibri-card-status kolibri-status--<?php echo esc_attr( $status_slug ); ?>">
				<?php echo esc_html( $status_label ); ?>
			</span>
		<?php endif; ?>

		<!-- Energy label badge -->
		<?php if ( $energielabel ) : ?>
			<span class="kolibri-card-energy kolibri-energy--<?php echo esc_attr( strtolower( str_replace( '+', 'plus', $energielabel ) ) ); ?>">
				<?php echo esc_html( $energielabel ); ?>
			</span>
		<?php endif; ?>
	</a>

	<div class="kolibri-card-body">
		<p class="kolibri-card-prijs"><?php echo esc_html( $prijs_label ); ?></p>

		<h2 class="kolibri-card-title">
			<a href="<?php echo esc_url( get_permalink( $id ) ); ?>">
				<?php echo esc_html( $adres ?: get_the_title( $id ) ); ?>
			</a>
		</h2>

		<?php if ( $stad ) : ?>
			<p class="kolibri-card-stad">
				<span class="material-symbols-outlined">location_on</span>
				<?php echo esc_html( $stad ); ?>
			</p>
		<?php endif; ?>

		<div class="kolibri-card-stats">
			<?php if ( $woon_m2 ) : ?>
				<span class="kolibri-card-stat">
					<span class="material-symbols-outlined">straighten</span>
					<?php echo esc_html( $woon_m2 ); ?> m²
				</span>
			<?php endif; ?>
			<?php if ( $kamers ) : ?>
				<span class="kolibri-card-stat">
					<span class="material-symbols-outlined">meeting_room</span>
					<?php echo esc_html( $kamers ); ?>
				</span>
			<?php endif; ?>
			<?php if ( $slaapkamers ) : ?>
				<span class="kolibri-card-stat">
					<span class="material-symbols-outlined">bed</span>
					<?php echo esc_html( $slaapkamers ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
</article>
