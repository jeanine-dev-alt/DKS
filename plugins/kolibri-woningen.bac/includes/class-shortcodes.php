<?php
/**
 * Shortcodes.
 *
 * [kolibri_woningen_search]        — full search form + results grid
 * [kolibri_woningen_grid]          — grid only (no form)
 * [kolibri_woning_uitgelicht]      — featured listings
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public static function register(): void {
		add_shortcode( 'kolibri_woningen_search', [ self::class, 'search' ] );
		add_shortcode( 'kolibri_woningen_grid',   [ self::class, 'grid' ] );
		add_shortcode( 'kolibri_woning_uitgelicht', [ self::class, 'uitgelicht' ] );
	}

	// ── [kolibri_woningen_search] ─────────────────────────────────────────────

	public static function search( array $atts ): string {
		$atts = shortcode_atts( [
			'per_page'   => 12,
			'columns'    => 3,
			'show_filter'=> 'yes',
		], $atts, 'kolibri_woningen_search' );

		ob_start();

		// Enqueue assets if not already loaded.
		if ( ! wp_style_is( 'kolibri-frontend', 'enqueued' ) ) {
			Assets::enqueue_frontend();
		}

		$filters   = self::get_request_filters();
		$query     = Query::get_woningen( array_merge( $filters, [ 'per_page' => (int) $atts['per_page'] ] ) );

		echo '<div class="kolibri-search-wrap" data-columns="' . esc_attr( $atts['columns'] ) . '">';

		if ( 'yes' === $atts['show_filter'] ) {
			Template_Loader::partial( 'search-form', [
				'filters'    => $filters,
				'show_count' => true,
			] );
		}

		echo '<div id="kolibri-results">';
		self::render_grid( $query, (int) $atts['columns'] );
		echo '</div>';

		echo '</div>';

		wp_reset_postdata();
		return ob_get_clean();
	}

	// ── [kolibri_woningen_grid] ───────────────────────────────────────────────

	public static function grid( array $atts ): string {
		$atts = shortcode_atts( [
			'per_page'  => 6,
			'columns'   => 3,
			'stad'      => '',
			'type'      => '',
			'status'    => '',
			'uitgelicht'=> '',
			'orderby'   => 'date',
			'order'     => 'DESC',
		], $atts, 'kolibri_woningen_grid' );

		$query = Query::get_woningen( [
			'per_page'   => (int) $atts['per_page'],
			'stad'       => $atts['stad'],
			'type'       => $atts['type'],
			'status'     => $atts['status'],
			'uitgelicht' => $atts['uitgelicht'],
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
		] );

		ob_start();
		self::render_grid( $query, (int) $atts['columns'] );
		wp_reset_postdata();
		return ob_get_clean();
	}

	// ── [kolibri_woning_uitgelicht] ───────────────────────────────────────────

	public static function uitgelicht( array $atts ): string {
		$atts = shortcode_atts( [
			'per_page' => 3,
			'columns'  => 3,
		], $atts, 'kolibri_woning_uitgelicht' );

		$query = Query::get_woningen( [
			'per_page'   => (int) $atts['per_page'],
			'uitgelicht' => '1',
		] );

		ob_start();
		self::render_grid( $query, (int) $atts['columns'] );
		wp_reset_postdata();
		return ob_get_clean();
	}

	// ── Grid renderer ─────────────────────────────────────────────────────────

	public static function render_grid( \WP_Query $query, int $columns = 3 ): void {
		if ( ! $query->have_posts() ) {
			echo '<p class="kolibri-no-results">' . esc_html__( 'Geen woningen gevonden.', 'kolibri-woningen' ) . '</p>';
			return;
		}

		$col_class = 'kolibri-cols-' . $columns;
		echo '<div class="kolibri-grid ' . esc_attr( $col_class ) . '">';

		while ( $query->have_posts() ) {
			$query->the_post();
			Template_Loader::partial( 'card', [ 'post_id' => get_the_ID() ] );
		}

		echo '</div>';

		// Pagination.
		if ( $query->max_num_pages > 1 ) {
			echo '<div class="kolibri-pagination">';
			$paged = (int) ( get_query_var( 'paged' ) ?: 1 );
			for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
				$active = $i === $paged ? ' kolibri-active' : '';
				echo '<button type="button" class="kolibri-page-btn' . esc_attr( $active ) . '" data-page="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</button>';
			}
			echo '</div>';
		}
	}

	// ── Helpers ───────────────────────────────────────────────────────────────

	private static function get_request_filters(): array {
		$allowed = [ 'type', 'stad', 'status', 'energie', 'min_prijs', 'max_prijs', 'min_m2', 'kamers', 'paged' ];
		$filters = [];
		foreach ( $allowed as $key ) {
			if ( ! empty( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$filters[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			}
		}
		return $filters;
	}
}
