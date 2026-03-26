<?php
/**
 * Server-side render — kolibri/woningen-grid block.
 *
 * Supports two modes:
 *   'latest'   — queries the N most recent published woningen.
 *   'selected' — queries a specific list of woningen by ID, preserving order.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Query;
use KolibriWoningen\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mode        = sanitize_text_field( $attributes['mode'] ?? 'latest' );
$count       = max( 1, (int) ( $attributes['count'] ?? 4 ) );
$columns     = min( 4, max( 2, (int) ( $attributes['columns'] ?? 3 ) ) );
$selected    = array_map( 'absint', $attributes['selectedIds'] ?? [] );
$selected    = array_filter( $selected ); // remove any 0s

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

$wrapper_attrs = get_block_wrapper_attributes( [ 'class' => 'kolibri-block-grid' ] );

ob_start();
Shortcodes::render_grid( $query, $columns );
wp_reset_postdata();
$grid = ob_get_clean();

echo '<div ' . $wrapper_attrs . '>' . $grid . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
