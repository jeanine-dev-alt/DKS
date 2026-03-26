<?php
/**
 * CPT + taxonomy registration for Kolibri Woningen.
 *
 * @package KolibriWoningen
 */

namespace KolibriWoningen;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Types {

	public static function register(): void {
		self::register_cpt();
		self::register_taxonomy_type();
		self::register_taxonomy_status();
		self::register_taxonomy_city();
		self::register_taxonomy_energy();
	}

	// ── CPT ──────────────────────────────────────────────────────────────────

	private static function register_cpt(): void {
		$labels = [
			'name'               => _x( 'Woningen', 'post type general name', 'kolibri-woningen' ),
			'singular_name'      => _x( 'Woning', 'post type singular name', 'kolibri-woningen' ),
			'add_new'            => __( 'Woning toevoegen', 'kolibri-woningen' ),
			'add_new_item'       => __( 'Nieuwe woning toevoegen', 'kolibri-woningen' ),
			'edit_item'          => __( 'Woning bewerken', 'kolibri-woningen' ),
			'new_item'           => __( 'Nieuwe woning', 'kolibri-woningen' ),
			'view_item'          => __( 'Woning bekijken', 'kolibri-woningen' ),
			'search_items'       => __( 'Woningen zoeken', 'kolibri-woningen' ),
			'not_found'          => __( 'Geen woningen gevonden', 'kolibri-woningen' ),
			'not_found_in_trash' => __( 'Geen woningen in de prullenbak', 'kolibri-woningen' ),
			'menu_name'          => __( 'Woningen', 'kolibri-woningen' ),
		];

		register_post_type( 'kolibri_woning', [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'properties', 'with_front' => false ],
			'capability_type'    => 'post',
			'has_archive'        => 'properties',
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-building',
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
			'show_in_rest'       => true,
		] );
	}

	// ── Taxonomy: Woningtype ─────────────────────────────────────────────────

	private static function register_taxonomy_type(): void {
		$labels = [
			'name'              => _x( 'Woningtypen', 'taxonomy general name', 'kolibri-woningen' ),
			'singular_name'     => _x( 'Woningtype', 'taxonomy singular name', 'kolibri-woningen' ),
			'search_items'      => __( 'Woningtypen zoeken', 'kolibri-woningen' ),
			'all_items'         => __( 'Alle woningtypen', 'kolibri-woningen' ),
			'edit_item'         => __( 'Woningtype bewerken', 'kolibri-woningen' ),
			'update_item'       => __( 'Woningtype bijwerken', 'kolibri-woningen' ),
			'add_new_item'      => __( 'Nieuw woningtype toevoegen', 'kolibri-woningen' ),
			'new_item_name'     => __( 'Nieuw woningtype', 'kolibri-woningen' ),
			'menu_name'         => __( 'Woningtypen', 'kolibri-woningen' ),
		];

		register_taxonomy( 'kolibri_type', 'kolibri_woning', [
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'rewrite'           => [ 'slug' => 'woningtype' ],
			'show_in_rest'      => true,
			'show_admin_column' => true,
		] );
	}

	// ── Taxonomy: Status ──────────────────────────────────────────────────────

	private static function register_taxonomy_status(): void {
		$labels = [
			'name'          => _x( 'Status', 'taxonomy general name', 'kolibri-woningen' ),
			'singular_name' => _x( 'Status', 'taxonomy singular name', 'kolibri-woningen' ),
			'menu_name'     => __( 'Status', 'kolibri-woningen' ),
		];

		register_taxonomy( 'kolibri_status', 'kolibri_woning', [
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'rewrite'           => [ 'slug' => 'woning-status' ],
			'show_in_rest'      => true,
			'show_admin_column' => true,
		] );
	}

	// ── Taxonomy: Stad ────────────────────────────────────────────────────────

	private static function register_taxonomy_city(): void {
		$labels = [
			'name'          => _x( 'Steden', 'taxonomy general name', 'kolibri-woningen' ),
			'singular_name' => _x( 'Stad', 'taxonomy singular name', 'kolibri-woningen' ),
			'menu_name'     => __( 'Steden', 'kolibri-woningen' ),
		];

		register_taxonomy( 'kolibri_stad', 'kolibri_woning', [
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'rewrite'           => [ 'slug' => 'stad' ],
			'show_in_rest'      => true,
			'show_admin_column' => true,
		] );
	}

	// ── Taxonomy: Energielabel ────────────────────────────────────────────────

	private static function register_taxonomy_energy(): void {
		$labels = [
			'name'          => _x( 'Energielabels', 'taxonomy general name', 'kolibri-woningen' ),
			'singular_name' => _x( 'Energielabel', 'taxonomy singular name', 'kolibri-woningen' ),
			'menu_name'     => __( 'Energielabels', 'kolibri-woningen' ),
		];

		register_taxonomy( 'kolibri_energie', 'kolibri_woning', [
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'rewrite'           => [ 'slug' => 'energielabel' ],
			'show_in_rest'      => true,
			'show_admin_column' => false,
		] );
	}

	// ── Helper: get meta field value ──────────────────────────────────────────

	public static function get_meta( int $post_id, string $key, mixed $default = '' ): mixed {
		$value = get_post_meta( $post_id, '_kolibri_' . $key, true );
		return ( '' !== $value && false !== $value ) ? $value : $default;
	}

	// ── Helper: formatted price ───────────────────────────────────────────────

	public static function format_price( string $price ): string {
		$num = (float) preg_replace( '/[^0-9.]/', '', $price );
		if ( ! $num ) {
			return '';
		}
		return '€ ' . number_format( $num, 0, ',', '.' );
	}
}
