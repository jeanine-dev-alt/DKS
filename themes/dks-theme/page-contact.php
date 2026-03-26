<?php
/**
 * Template Name: Contact
 *
 * Contact page with CF7 form on the left and contact details on the right.
 * The CF7 form ID is configured via Appearance → Customizer → Forms.
 *
 * @package DKS_Theme
 * @since   1.0.1
 */

get_header();
?>

<main id="primary" class="site-main">

	<!-- ── Page hero ──────────────────────────────────────────────────────── -->
	<section class="dks-page-hero">
		<div class="dks-container">
			<p class="dks-page-hero__eyebrow"><?php esc_html_e( 'Get in touch', 'dks-theme' ); ?></p>
			<h1 class="dks-page-hero__title">
				<?php echo wp_kses_post( get_the_title() ?: __( 'Contact', 'dks-theme' ) ); ?>
			</h1>
		</div>
	</section>

	<!-- ── Contact section ────────────────────────────────────────────────── -->
	<section class="dks-contact-section">
		<div class="dks-container">
			<div class="dks-contact-grid">

				<!-- CF7 form column -->
				<div class="dks-contact-form-col">
					<h2 class="dks-contact-form-col__heading">
						<?php esc_html_e( 'Send us a message', 'dks-theme' ); ?>
					</h2>

					<?php
					// Priority 1: shortcode saved in the meta box (Contact Form Shortcode).
					$meta_shortcode = get_post_meta( get_the_ID(), '_dks_contact_shortcode', true );

					if ( ! empty( trim( $meta_shortcode ) ) ) :
						?>
						<div class="dks-form-wrap" style="max-width:100%;">
							<?php echo do_shortcode( $meta_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<?php
					else :
						// Priority 2: page editor content (classic/block shortcode in body).
						$post_content = get_the_content();

						if ( ! empty( trim( $post_content ) ) ) :
							?>
							<div class="dks-form-wrap" style="max-width:100%;">
								<?php the_content(); ?>
							</div>
							<?php
						else :
							// Priority 3: CF7 form ID from Customizer (legacy).
							$form_id = (int) get_theme_mod( 'dks_cf7_contact', 0 );

							if ( $form_id && function_exists( 'wpcf7_contact_form' ) ) :
								echo do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="dks-cf7-form"]', $form_id ) );
							elseif ( $form_id ) :
								?>
								<p class="dks-notice">
									<?php esc_html_e( 'Contact Form 7 is not activated. Please install and activate the plugin.', 'dks-theme' ); ?>
								</p>
								<?php
							else :
								?>
								<p style="color:rgba(26,26,26,.5);font-size:.9rem;">
									<?php esc_html_e( 'Add a shortcode via the Contact Form Shortcode meta box, or place content in the page editor.', 'dks-theme' ); ?>
								</p>
								<?php
							endif;
						endif;
					endif;
					?>
				</div>

				<!-- Contact details column -->
				<div class="dks-contact-details-col">
					<h2 class="dks-contact-details-col__heading">
						<?php esc_html_e( 'Contact details', 'dks-theme' ); ?>
					</h2>

					<?php
					$address = get_theme_mod( 'dks_contact_address', __( 'Prinsengracht 124, 1016 DZ Amsterdam', 'dks-theme' ) );
					$phone   = get_theme_mod( 'dks_contact_phone', '+31 (0) 20 123 4567' );
					$email   = get_theme_mod( 'dks_contact_email', 'info@dksrealestate.nl' );
					?>

					<ul class="dks-contact-details__list">
						<?php if ( $address ) : ?>
							<li>
								<span class="dks-contact-details__label"><?php esc_html_e( 'Address', 'dks-theme' ); ?></span>
								<span class="dks-contact-details__value"><?php echo esc_html( $address ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $phone ) : ?>
							<li>
								<span class="dks-contact-details__label"><?php esc_html_e( 'Phone', 'dks-theme' ); ?></span>
								<a class="dks-contact-details__value"
								   href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ); ?>">
									<?php echo esc_html( $phone ); ?>
								</a>
							</li>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<li>
								<span class="dks-contact-details__label"><?php esc_html_e( 'E-mail', 'dks-theme' ); ?></span>
								<a class="dks-contact-details__value"
								   href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
									<?php echo esc_html( antispambot( $email ) ); ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>

					<?php
					$weekdays = get_theme_mod( 'dks_hours_weekdays', '09:00 – 18:00' );
					$saturday = get_theme_mod( 'dks_hours_saturday', '10:00 – 15:00' );
					?>
					<div class="dks-contact-details__hours">
						<p class="dks-contact-details__hours-title">
							<?php esc_html_e( 'Office hours', 'dks-theme' ); ?>
						</p>
						<ul class="dks-contact-details__hours-list">
							<li>
								<span><?php esc_html_e( 'Mon – Fri', 'dks-theme' ); ?></span>
								<span><?php echo esc_html( $weekdays ); ?></span>
							</li>
							<li>
								<span><?php esc_html_e( 'Saturday', 'dks-theme' ); ?></span>
								<span><?php echo esc_html( $saturday ); ?></span>
							</li>
							<li>
								<span><?php esc_html_e( 'Sunday', 'dks-theme' ); ?></span>
								<span><?php esc_html_e( 'Closed', 'dks-theme' ); ?></span>
							</li>
						</ul>
					</div>
				</div>

			</div><!-- .dks-contact-grid -->
		</div><!-- .dks-container -->
	</section>

</main><!-- #primary -->

<?php get_footer(); ?>
