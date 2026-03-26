<?php
/**
 * Server-side render — kolibri/woningen-zoekformulier block.
 *
 * @package KolibriWoningen
 */

use KolibriWoningen\Shortcodes;
use KolibriWoningen\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$per_page    = (int) ( $attributes['perPage']    ?? 12 );
$columns     = (int) ( $attributes['columns']    ?? 3 );
$show_filter = (bool) ( $attributes['showFilter'] ?? true );

// Enqueue assets.
if ( ! wp_style_is( 'kolibri-frontend', 'enqueued' ) ) {
	Assets::enqueue_frontend();
}

$wrapper_attrs = get_block_wrapper_attributes();

$output = Shortcodes::search( [
	'per_page'    => $per_page,
	'columns'     => $columns,
	'show_filter' => $show_filter ? 'yes' : 'no',
] );

echo '<div ' . $wrapper_attrs . '>' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
