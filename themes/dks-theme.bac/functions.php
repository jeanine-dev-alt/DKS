<?php
/**
 * DKS Real Estate — functions.php
 *
 * Bootstraps the theme by requiring feature files from /inc/.
 * Child themes can override any function by declaring it before
 * the parent's include fires (standard WordPress child-theme pattern).
 *
 * @package DKS_Theme
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Never load this file directly.
}

// ── Constants ──────────────────────────────────────────────────────────────
define( 'DKS_VERSION',   '1.0.0' );
define( 'DKS_THEME_DIR', get_template_directory() );
define( 'DKS_THEME_URI', get_template_directory_uri() );

// ── Load feature files ─────────────────────────────────────────────────────
// Each file is wrapped in a function_exists() guard so a child theme can
// override individual features simply by defining the same function earlier.

$dks_includes = [
	'/inc/setup.php',          // Theme supports, menus, image sizes
	'/inc/enqueue.php',        // Scripts & styles
	'/inc/blocks.php',         // Gutenberg block registration
	'/inc/block-filters.php',  // Allowed blocks, block patterns
	'/inc/meta-boxes.php',     // Custom meta boxes for property data
	'/inc/template-tags.php',  // Reusable template helper functions
	'/inc/widgets.php',        // Sidebar / widget area registration
	'/inc/listings-hooks.php', // Hooks for listings — overridable via child
	'/inc/customizer.php',     // Customizer: social links, CF7 form IDs
];

foreach ( $dks_includes as $file ) {
	$path = DKS_THEME_DIR . $file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
