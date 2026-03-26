<?php
/**
 * Theme Customizer settings.
 *
 * Adds sections for:
 *  - Social links (LinkedIn, Instagram, Facebook)
 *  - Contact info (address, phone, e-mail, office hours)
 *  - Forms (CF7 contact form ID, CF7 newsletter form ID)
 *
 * @package DKS_Theme
 * @since   1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── 1. Register Customizer settings & controls ──────────────────────────────

if ( ! function_exists( 'dks_customize_register' ) ) :
	function dks_customize_register( WP_Customize_Manager $wp_customize ): void {

		// ── Social links section ─────────────────────────────────────────────

		$wp_customize->add_section( 'dks_social', [
			'title'    => __( 'Social Links', 'dks-theme' ),
			'priority' => 130,
		] );

		$social_fields = [
			'dks_social_linkedin'  => [ __( 'LinkedIn URL', 'dks-theme' ), 'https://linkedin.com/' ],
			'dks_social_instagram' => [ __( 'Instagram URL', 'dks-theme' ), 'https://instagram.com/' ],
			'dks_social_facebook'  => [ __( 'Facebook URL', 'dks-theme' ), 'https://facebook.com/' ],
		];

		foreach ( $social_fields as $key => [ $label, $placeholder ] ) {
			$wp_customize->add_setting( $key, [
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			] );

			$wp_customize->add_control( $key, [
				'label'       => $label,
				'section'     => 'dks_social',
				'type'        => 'url',
				'input_attrs' => [ 'placeholder' => $placeholder ],
			] );
		}

		// ── Forms section ────────────────────────────────────────────────────

		$wp_customize->add_section( 'dks_forms', [
			'title'       => __( 'Forms (Contact Form 7)', 'dks-theme' ),
			'priority'    => 140,
			'description' => __( 'Enter the numeric ID of the CF7 form. Leave empty to hide the form.', 'dks-theme' ),
		] );

		$form_fields = [
			'dks_cf7_contact'    => __( 'Contact page form ID', 'dks-theme' ),
			'dks_cf7_newsletter' => __( 'Newsletter form ID', 'dks-theme' ),
		];

		foreach ( $form_fields as $key => $label ) {
			$wp_customize->add_setting( $key, [
				'default'           => '',
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			] );

			$wp_customize->add_control( $key, [
				'label'       => $label,
				'section'     => 'dks_forms',
				'type'        => 'number',
				'input_attrs' => [ 'min' => 0 ],
			] );
		}

		// ── Contact info section (already used in footer.php) ────────────────

		if ( ! $wp_customize->get_section( 'dks_contact_info' ) ) {
			$wp_customize->add_section( 'dks_contact_info', [
				'title'    => __( 'Contact Info', 'dks-theme' ),
				'priority' => 135,
			] );

			$contact_fields = [
				'dks_contact_address'  => [ __( 'Address', 'dks-theme' ),          'sanitize_text_field', 'text', 'Prinsengracht 124, 1016 DZ Amsterdam' ],
				'dks_contact_phone'    => [ __( 'Phone number', 'dks-theme' ),      'sanitize_text_field', 'text', '+31 (0) 20 123 4567' ],
				'dks_contact_email'    => [ __( 'E-mail address', 'dks-theme' ),    'sanitize_email',      'email', 'info@dksrealestate.nl' ],
				'dks_hours_weekdays'   => [ __( 'Office hours: Mon–Fri', 'dks-theme' ), 'sanitize_text_field', 'text', '09:00 – 18:00' ],
				'dks_hours_saturday'   => [ __( 'Office hours: Saturday', 'dks-theme' ), 'sanitize_text_field', 'text', '10:00 – 15:00' ],
			];

			foreach ( $contact_fields as $key => [ $label, $sanitize, $type, $default ] ) {
				$wp_customize->add_setting( $key, [
					'default'           => $default,
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				] );

				$wp_customize->add_control( $key, [
					'label'   => $label,
					'section' => 'dks_contact_info',
					'type'    => $type,
				] );
			}
		}
	}
endif;
add_action( 'customize_register', 'dks_customize_register' );


// ── 2. Social links filter ───────────────────────────────────────────────────

if ( ! function_exists( 'dks_social_links_from_customizer' ) ) :
	/**
	 * Populates the dks_social_links filter with SVG icon links
	 * built from Customizer URLs. Only includes items where URL is set.
	 */
	function dks_social_links_from_customizer( array $links ): array {

		$platforms = [
			'linkedin'  => [
				'label' => __( 'LinkedIn', 'dks-theme' ),
				'key'   => 'dks_social_linkedin',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="20" height="20"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
			],
			'instagram' => [
				'label' => __( 'Instagram', 'dks-theme' ),
				'key'   => 'dks_social_instagram',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
			],
			'facebook'  => [
				'label' => __( 'Facebook', 'dks-theme' ),
				'key'   => 'dks_social_facebook',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="20" height="20"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
			],
		];

		foreach ( $platforms as $platform ) {
			$url = get_theme_mod( $platform['key'], '' );
			if ( ! empty( $url ) ) {
				$links[] = [
					'url'   => esc_url( $url ),
					'label' => $platform['label'],
					'icon'  => $platform['icon'],
				];
			}
		}

		return $links;
	}
endif;
add_filter( 'dks_social_links', 'dks_social_links_from_customizer' );


// ── 3. Newsletter CF7 filter ─────────────────────────────────────────────────

if ( ! function_exists( 'dks_cf7_newsletter_form' ) ) :
	/**
	 * Hooks into dks_newsletter_form_html to replace the default mock
	 * with a real CF7 shortcode when a form ID is configured.
	 */
	function dks_cf7_newsletter_form( string $html, array $attributes ): string {
		$form_id = (int) get_theme_mod( 'dks_cf7_newsletter', 0 );

		if ( ! $form_id ) {
			return $html;
		}

		// Only render if CF7 is active.
		if ( ! function_exists( 'wpcf7_contact_form' ) ) {
			return $html;
		}

		return do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="dks-newsletter__cf7-form"]', $form_id ) );
	}
endif;
add_filter( 'dks_newsletter_form_html', 'dks_cf7_newsletter_form', 10, 2 );
