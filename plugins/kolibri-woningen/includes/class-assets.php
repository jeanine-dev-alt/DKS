<?php
/**
 * Asset enqueueing — frontend and admin.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	public static function enqueue_frontend(): void {
		if ( ! self::is_kolibri_page() ) {
			return;
		}

		wp_enqueue_style(
			'kolibri-frontend',
			KOLIBRI_URI . 'assets/css/frontend.css',
			[],
			KOLIBRI_VERSION
		);

		wp_enqueue_script(
			'kolibri-frontend',
			KOLIBRI_URI . 'assets/js/frontend.js',
			[],
			KOLIBRI_VERSION,
			true
		);

		wp_enqueue_script(
			'kolibri-gallery',
			KOLIBRI_URI . 'assets/js/gallery.js',
			[],
			KOLIBRI_VERSION,
			true
		);

		wp_localize_script( 'kolibri-frontend', 'kolibriData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'kolibri_ajax' ),
			'i18n'    => [
				'noResults' => __( 'Geen woningen gevonden.', 'kolibri-woningen' ),
				'loading'   => __( 'Laden...', 'kolibri-woningen' ),
			],
		] );
	}

	public static function enqueue_admin( string $hook ): void {
		$screen = get_current_screen();
		if ( ! $screen || 'kolibri_woning' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'kolibri-admin',
			KOLIBRI_URI . 'assets/css/admin.css',
			[],
			KOLIBRI_VERSION
		);

		// Media uploader for gallery.
		wp_enqueue_media();

		wp_enqueue_script(
			'kolibri-admin',
			KOLIBRI_URI . 'assets/js/admin.js',
			[ 'jquery', 'media-upload', 'thickbox' ],
			KOLIBRI_VERSION,
			true
		);
	}

	public static function enqueue_block_editor(): void {
		wp_enqueue_script(
			'kolibri-blocks',
			KOLIBRI_URI . 'assets/js/blocks.js',
			[ 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-api-fetch' ],
			KOLIBRI_VERSION,
			true
		);
	}

	private static function is_kolibri_page(): bool {
		return is_singular( 'kolibri_woning' )
			|| is_post_type_archive( 'kolibri_woning' )
			|| is_tax( [ 'kolibri_type', 'kolibri_status', 'kolibri_stad', 'kolibri_energie' ] )
			|| is_page_template( 'page-woning-overzicht.php' );
	}
}
