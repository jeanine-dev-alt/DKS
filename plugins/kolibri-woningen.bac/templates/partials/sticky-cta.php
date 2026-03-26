<?php
/**
 * Sticky sidebar CTA — contact form / action buttons.
 *
 * Variables provided:
 *   int    $post_id
 *   string $prijs_label
 *   string $adres
 *   string $brochure_url
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agent_name  = get_theme_mod( 'dks_footer_contact_name', '' );
$agent_phone = Post_Types::get_meta( $post_id, 'agent_phone' ) ?: get_theme_mod( 'dks_footer_phone', '' );
$agent_email = Post_Types::get_meta( $post_id, 'agent_email' ) ?: get_theme_mod( 'dks_footer_email', '' );

$phone_clean = preg_replace( '/[^0-9+]/', '', $agent_phone );
?>

<aside class="kolibri-sticky-cta" id="kolibri-sticky-cta">

	<!-- Price -->
	<?php if ( $prijs_label ) : ?>
		<div class="kolibri-cta-price"><?php echo esc_html( $prijs_label ); ?></div>
	<?php endif; ?>

	<?php if ( $adres ) : ?>
		<p class="kolibri-cta-adres"><?php echo esc_html( $adres ); ?></p>
	<?php endif; ?>

	<!-- Contact buttons -->
	<div class="kolibri-cta-buttons">
		<?php if ( $agent_phone ) : ?>
			<a href="tel:<?php echo esc_attr( $phone_clean ); ?>" class="kolibri-btn kolibri-btn-primary">
				<span class="material-symbols-outlined">call</span>
				<?php echo esc_html( $agent_phone ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $agent_email ) : ?>
			<a href="mailto:<?php echo esc_attr( $agent_email ); ?>?subject=<?php echo esc_attr( get_the_title( $post_id ) ); ?>" class="kolibri-btn kolibri-btn-outline">
				<span class="material-symbols-outlined">mail</span>
				<?php esc_html_e( 'E-mail versturen', 'kolibri-woningen' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<!-- Contact form toggle -->
	<details class="kolibri-cta-form-wrap">
		<summary class="kolibri-cta-form-toggle">
			<?php esc_html_e( 'Bezichtiging aanvragen', 'kolibri-woningen' ); ?>
		</summary>
		<form class="kolibri-contact-form" method="post" action="">
			<?php wp_nonce_field( 'kolibri_contact_' . $post_id, 'kolibri_contact_nonce' ); ?>
			<input type="hidden" name="kolibri_post_id" value="<?php echo esc_attr( $post_id ); ?>">
			<input type="hidden" name="kolibri_post_title" value="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">

			<div class="kolibri-form-row">
				<label for="kolibri-cf-name"><?php esc_html_e( 'Naam', 'kolibri-woningen' ); ?></label>
				<input type="text" id="kolibri-cf-name" name="cf_name" required autocomplete="name">
			</div>
			<div class="kolibri-form-row">
				<label for="kolibri-cf-email"><?php esc_html_e( 'E-mailadres', 'kolibri-woningen' ); ?></label>
				<input type="email" id="kolibri-cf-email" name="cf_email" required autocomplete="email">
			</div>
			<div class="kolibri-form-row">
				<label for="kolibri-cf-phone"><?php esc_html_e( 'Telefoonnummer', 'kolibri-woningen' ); ?></label>
				<input type="tel" id="kolibri-cf-phone" name="cf_phone" autocomplete="tel">
			</div>
			<div class="kolibri-form-row">
				<label for="kolibri-cf-message"><?php esc_html_e( 'Bericht', 'kolibri-woningen' ); ?></label>
				<textarea id="kolibri-cf-message" name="cf_message" rows="4"></textarea>
			</div>
			<button type="submit" name="kolibri_send_contact" class="kolibri-btn kolibri-btn-primary" style="width:100%">
				<?php esc_html_e( 'Versturen', 'kolibri-woningen' ); ?>
			</button>
		</form>
	</details>

	<!-- Brochure download -->
	<?php if ( ! empty( $brochure_url ) ) : ?>
		<a href="<?php echo esc_url( $brochure_url ); ?>" class="kolibri-btn kolibri-btn-ghost" download>
			<span class="material-symbols-outlined">download</span>
			<?php esc_html_e( 'Brochure downloaden', 'kolibri-woningen' ); ?>
		</a>
	<?php endif; ?>

</aside>
