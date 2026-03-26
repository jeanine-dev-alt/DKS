<?php
/**
 * AJAX handlers for filtering and loading woningen.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {

	public static function register(): void {
		add_action( 'wp_ajax_kolibri_filter',        [ self::class, 'filter' ] );
		add_action( 'wp_ajax_nopriv_kolibri_filter', [ self::class, 'filter' ] );
	}

	/**
	 * Returns rendered HTML cards based on filter params.
	 * Called via fetch() from frontend.js.
	 */
	public static function filter(): void {
		check_ajax_referer( 'kolibri_ajax', 'nonce' );

		$filters = [
			'type'      => sanitize_text_field( wp_unslash( $_POST['type']      ?? '' ) ),
			'stad'      => sanitize_text_field( wp_unslash( $_POST['stad']      ?? '' ) ),
			'status'    => sanitize_text_field( wp_unslash( $_POST['status']    ?? '' ) ),
			'energie'   => sanitize_text_field( wp_unslash( $_POST['energie']   ?? '' ) ),
			'min_prijs' => (int) ( $_POST['min_prijs'] ?? 0 ),
			'max_prijs' => (int) ( $_POST['max_prijs'] ?? 0 ),
			'min_m2'    => (int) ( $_POST['min_m2']    ?? 0 ),
			'kamers'    => (int) ( $_POST['kamers']    ?? 0 ),
			'paged'     => max( 1, (int) ( $_POST['paged'] ?? 1 ) ),
			'per_page'  => max( 1, min( 48, (int) ( $_POST['per_page'] ?? 12 ) ) ),
		];

		// Remove empty strings and zero ints.
		$filters = array_filter( $filters );

		$query = Query::get_woningen( $filters );

		ob_start();
		Shortcodes::render_grid( $query, 3 );
		wp_reset_postdata();
		$html = ob_get_clean();

		wp_send_json_success( [
			'html'       => $html,
			'found'      => $query->found_posts,
			'max_pages'  => $query->max_num_pages,
		] );
	}
}
