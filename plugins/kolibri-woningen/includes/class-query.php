<?php
/**
 * WP_Query helpers for Kolibri Woningen.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Query {

	/**
	 * Build a WP_Query for woningen with optional filter args.
	 *
	 * @param array $filters {
	 *   @type string $type       Taxonomy slug (kolibri_type).
	 *   @type string $stad       Taxonomy slug (kolibri_stad).
	 *   @type string $status     Taxonomy slug (kolibri_status).
	 *   @type string $energie    Taxonomy slug (kolibri_energie).
	 *   @type int    $min_prijs  Min koopprijs.
	 *   @type int    $max_prijs  Max koopprijs.
	 *   @type int    $min_m2     Min woon_m2.
	 *   @type int    $kamers     Exact aantal kamers.
	 *   @type int    $paged      Page number.
	 *   @type int    $per_page   Posts per page.
	 * }
	 */
	public static function get_woningen( array $filters = [] ): \WP_Query {
		$args = [
			'post_type'      => 'kolibri_woning',
			'post_status'    => 'publish',
			'posts_per_page' => (int) ( $filters['per_page'] ?? 12 ),
			'paged'          => (int) ( $filters['paged'] ?? 1 ),
			'orderby'        => $filters['orderby'] ?? 'date',
			'order'          => strtoupper( $filters['order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC',
		];

		// Taxonomy filters.
		$tax_query = [];
		$tax_map   = [
			'type'    => 'kolibri_type',
			'stad'    => 'kolibri_stad',
			'status'  => 'kolibri_status',
			'energie' => 'kolibri_energie',
		];

		foreach ( $tax_map as $param => $taxonomy ) {
			if ( ! empty( $filters[ $param ] ) ) {
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $filters[ $param ] ),
				];
			}
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		// Meta filters.
		$meta_query = [];

		if ( ! empty( $filters['min_prijs'] ) ) {
			$meta_query[] = [
				'key'     => '_kolibri_koopprijs',
				'value'   => (int) $filters['min_prijs'],
				'compare' => '>=',
				'type'    => 'NUMERIC',
			];
		}
		if ( ! empty( $filters['max_prijs'] ) ) {
			$meta_query[] = [
				'key'     => '_kolibri_koopprijs',
				'value'   => (int) $filters['max_prijs'],
				'compare' => '<=',
				'type'    => 'NUMERIC',
			];
		}
		if ( ! empty( $filters['min_m2'] ) ) {
			$meta_query[] = [
				'key'     => '_kolibri_woon_m2',
				'value'   => (int) $filters['min_m2'],
				'compare' => '>=',
				'type'    => 'NUMERIC',
			];
		}
		if ( ! empty( $filters['kamers'] ) ) {
			$meta_query[] = [
				'key'     => '_kolibri_kamers',
				'value'   => (int) $filters['kamers'],
				'compare' => '>=',
				'type'    => 'NUMERIC',
			];
		}
		if ( ! empty( $filters['uitgelicht'] ) ) {
			$meta_query[] = [
				'key'   => '_kolibri_uitgelicht',
				'value' => '1',
			];
		}

		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}
		if ( $meta_query ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		return new \WP_Query( $args );
	}

	/**
	 * Get all unique values for a meta key across published woningen.
	 * Used to build dynamic filter options.
	 */
	public static function get_meta_values( string $meta_key ): array {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$results = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s
				  AND p.post_type = 'kolibri_woning'
				  AND p.post_status = 'publish'
				  AND pm.meta_value != ''
				ORDER BY pm.meta_value ASC",
				'_kolibri_' . $meta_key
			)
		);

		return $results ?: [];
	}

	/**
	 * Get price range across all published woningen.
	 *
	 * @return array{ min: int, max: int }
	 */
	public static function get_price_range(): array {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT MIN(CAST(pm.meta_value AS UNSIGNED)) as min_price,
				        MAX(CAST(pm.meta_value AS UNSIGNED)) as max_price
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s
				  AND p.post_type = 'kolibri_woning'
				  AND p.post_status = 'publish'
				  AND pm.meta_value != ''
				  AND pm.meta_value REGEXP '^[0-9]+$'",
				'_kolibri_koopprijs'
			)
		);

		return [
			'min' => (int) ( $row->min_price ?? 0 ),
			'max' => (int) ( $row->max_price ?? 0 ),
		];
	}
}
